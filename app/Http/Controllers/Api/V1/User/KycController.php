<?php

namespace App\Http\Controllers\Api\V1\User;


use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\SetupKyc;
use Illuminate\Support\Facades\DB;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Constants\SiteSectionConst;
use App\Lib\YouVerify;
use App\Mail\AdminKycSubmissionMail;
use App\Mail\KycApprovalMail;
use App\Mail\KycRejectionMail;
use App\Mail\KycSubmissionMail;
use App\Models\Admin\Admin;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\SiteSections;
use App\Models\User;
use App\Models\UserKycData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class KycController extends Controller
{
    use ControlDynamicInputFields;

    public function update_liveness(Request $request)
    {
        if ($request->status === "success" && $request->has('email')) {
            $kyc_owner = User::where('email', $request->email)->first();

            $kyc_owner->has_done_liveness = true;
            $kyc_owner->save();

            $kyc_owner->refresh();

            return response()->json([
                'status' => 'success',
                'message' => 'Liveness check completed successfully.',
                'data' => $kyc_owner
            ]);
        } elseif ($request->status === 'error') {
            return response()->json([
                'status' => 'error',
                'message' => 'Liveness check failed. Please try again.'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $basic_settings = BasicSettings::first();

            $user = auth()->user();
            if ($user->kyc_verified == GlobalConst::VERIFIED) return response()->json([
                "status" => "error",
                "message" => "You are already KYC Verified User"
            ], 400);

            $user_kyc_fields = SetupKyc::userKyc()->first()->fields ?? [];
            $validation_rules = $this->generateValidationRules($user_kyc_fields);
            $validated = Validator::make($request->all(), $validation_rules)->validate();
            $get_values = $this->placeValueWithFields($user_kyc_fields, $validated, true);

            $create = [
                'user_id'       => auth()->user()->id,
                'data'          => json_encode($get_values),
                'created_at'    => now(),
            ];

            $kyc_payload = [
                'id' => '',
                'image' => '',
                'document' => '',
                'lastName' => '',
                'country' => ''
            ];

            $document_map = [
                'NIN' => 'nin',
                'Drivers License' => 'license',
                'Passport' => 'passport'
            ];

            foreach ($get_values as $key) {
                if ($key['name'] === "id_number") {
                    $kyc_payload['id'] = $this->cleanInvisible(trim($key['value']));
                } else if ($key['name'] === 'selfie') {
                    $kyc_payload['image'] = get_image($key['value'], 'kyc-files');
                } else if ($key['name'] === "id_type") {
                    $kyc_payload['document'] = $document_map[trim($key['value'])];
                } else {
                }
            }

            $kyc_payload['country'] = $user->address->country;
            $kyc_payload['lastName'] = $user->lastname;
            $kyc_payload['firstName'] = $user->firstname;
            $kyc_payload['mobile'] = $user->full_mobile;

            DB::beginTransaction();
            try {
                DB::table('user_kyc_data')->updateOrInsert(["user_id" => $user->id], $create);

                Mail::to($user->email)->queue(
                    new KycSubmissionMail($user->username, env('FRONTEND_URL'), $basic_settings->site_name, get_logo($basic_settings))
                );

                $adminEmails = Admin::where('username', 'superadmin')
                    ->pluck('email')
                    ->toArray();

                Mail::to($adminEmails)->queue(
                    new AdminKycSubmissionMail($user->email, env('FRONTEND_URL'), $basic_settings->site_name, get_logo($basic_settings))
                );

                $response = (new YouVerify())->kycVerification($kyc_payload);

                if ($response) {
                    $user->update([
                        'kyc_verified'  => GlobalConst::APPROVED,
                    ]);

                    Mail::to($user->email)->queue(
                        new KycApprovalMail($user->username, env('FRONTEND_URL'), $basic_settings->site_name, get_logo($basic_settings))
                    );
                } else {
                    $user->update([
                        'kyc_verified'  => GlobalConst::PENDING,
                    ]);

                    Mail::to($user->email)->queue(
                        new KycRejectionMail($user->username, env('FRONTEND_URL'), $basic_settings->site_name, get_logo($basic_settings))
                    );
                }

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $user->update([
                    'kyc_verified'  => GlobalConst::DEFAULT,
                ]);
                $this->generatedFieldsFilesDelete($get_values);
                Log::error('KYC Submission Error: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'data' => $get_values
                ]);
                return response()->json([
                    "status" => "error",
                    "message" => "Something went wrong! Please try again"
                ], 500);
            }

            return response()->json([
                "status" => "success",
                "message" => "KYC data submitted successfully. Your verification is pending."
            ], 200);
        } catch (\Exception $e) {
            Log::error(['An error occured while verifying kyc' => $e->getMessage()]);

            return response()->json([
                "status" => "error",
                "message" => "Failed to verify KYC. Please try again later"
            ], 500);
        }
    }

    private function cleanInvisible($string)
    {
        return preg_replace('/[\p{C}]/u', '', $string);
    }
}
