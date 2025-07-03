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
use App\Models\Admin\BasicSettings;
use App\Models\Admin\SiteSections;
use Illuminate\Support\Facades\Log;

class KycController extends Controller
{
    use ControlDynamicInputFields;

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->kyc_verified == GlobalConst::VERIFIED) return response()->json([
            "status" => "error",
            "message" => "'You are already KYC Verified User'"
        ], 400);

        $user_kyc_fields = SetupKyc::userKyc()->first()->fields ?? [];
        $validation_rules = $this->generateValidationRules($user_kyc_fields);
        $validated = Validator::make($request->all(), $validation_rules)->validate();
        $get_values = $this->placeValueWithFields($user_kyc_fields, $validated);

        $create = [
            'user_id'       => auth()->user()->id,
            'data'          => json_encode($get_values),
            'created_at'    => now(),
        ];

        DB::beginTransaction();
        try {
            DB::table('user_kyc_data')->updateOrInsert(["user_id" => $user->id], $create);
            $user->update([
                'kyc_verified'  => GlobalConst::PENDING,
            ]);
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
    }
}
