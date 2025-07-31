<?php

namespace App\Http\Controllers\User;

use Exception;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\SetupKyc;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Constants\SiteSectionConst;
use App\Lib\YouVerify;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\SiteSections;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class KycController extends Controller
{
    use ControlDynamicInputFields;

    public function index()
    {
        $basic_settings = BasicSettings::first();
        if (!$basic_settings->kyc_verification) {
            return back()->with(['warning' => [__("KYC Verification isn't available")]]);
        }
        $page_title = "KYC Verification";
        $user = auth()->user();
        $user_kyc = SetupKyc::userKyc()->first();
        if (!$user_kyc) return redirect()->route('user.dashboard');

        $kyc_data = $user_kyc->fields;
        $kyc_fields = [];
        if ($kyc_data) {
            $kyc_fields = array_reverse($kyc_data);
        }

        $kyc_data = $user_kyc;
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();

        return view('user.sections.kyc.index', compact('page_title', 'user', 'kyc_fields', 'kyc_data', 'footer'));
    }

    public function store(Request $request)
    {
        try {


            $user = auth()->user();
            if ($user->kyc_verified == GlobalConst::VERIFIED) return back()->with(['success' => [__('You are already KYC Verified User')]]);

            $user_kyc_fields = SetupKyc::userKyc()->first()->fields ?? [];
            $validation_rules = $this->generateValidationRules($user_kyc_fields);
            $validated = Validator::make($request->all(), $validation_rules)->validate();
            $get_values = $this->placeValueWithFields($user_kyc_fields, $validated);

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
                    $image_path = files_path('kyc-files')->path;
                    $kyc_payload['image'] = base64_encode(file_get_contents($image_path . "/" . $key['value']));
                    // $kyc_payload['image'] = get_image($key['value'], 'kyc-files');
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

            DB::table('user_kyc_data')->updateOrInsert(["user_id" => $user->id], $create);

            $response = (new YouVerify())->kycVerification($kyc_payload);

            if ($response) {
                $user->update([
                    'kyc_verified'  => GlobalConst::APPROVED,
                ]);
            } else {
                $user->update([
                    'kyc_verified'  => GlobalConst::PENDING,
                ]);
            }
            DB::commit();

            return redirect()->route('user.kyc.index')->with(['success' => [__('KYC information successfully submitted')]]);
        } catch (Exception $e) {
            DB::rollBack();
            $user->update([
                'kyc_verified'  => GlobalConst::DEFAULT,
            ]);
            $this->generatedFieldsFilesDelete($get_values);
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }
    }

    private function cleanInvisible($string)
    {
        return preg_replace('/[\p{C}]/u', '', $string);
    }
}
