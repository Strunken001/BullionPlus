<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\ReloadlyApi;
use Illuminate\Support\Facades\Validator;

class MobileTopUpMethodController extends Controller
{
    //==============================================Top Up Method(Automatic) Start===============================================
    public function manageTopUpPayApi()
    {
        $page_title = __("Setup Mobile Top Up & Bundle API");
        $api = ReloadlyApi::reloadly()->mobileTopUp()->first();
        return view('admin.sections.mobile-topups.reloadly.api', compact(
            'page_title',
            'api',
        ));
    }
    public function updateCredentials(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'api_method'                        => 'required|in:reloadly',
            'reloadly_client_id'                => 'required_if:api_method,reloadly',
            'reloadly_secret_key'               => 'required_if:api_method,reloadly',
            'reloadly_production_base_url'      => 'required_if:api_method,reloadly',
            'reloadly_sandbox_base_url'         => 'required_if:api_method,reloadly',
            'reloadly_env'                      => 'required|string',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $validated = $validator->validate();
        if ($validated['api_method'] == 'reloadly') {
            $credentials = [
                'client_id' => $validated['reloadly_client_id'],
                'secret_key' => $validated['reloadly_secret_key'],
                'production_base_url' => $validated['reloadly_production_base_url'],
                'sandbox_base_url' => $validated['reloadly_sandbox_base_url'],
            ];
            $api = ReloadlyApi::reloadly()->mobileTopUp()->first();
            $data['credentials'] =  $credentials;
            $data['env']        = $validated['reloadly_env'];
            $data['status']     = true;
            $data['provider']   =  ReloadlyApi::PROVIDER_RELOADLY;
            $data['type']       =  ReloadlyApi::MOBILE_TOPUP;
            if (!$api) {
                ReloadlyApi::create($data);
            } else {
                $api->fill($data)->save();
            }
            return back()->with(['success' => [__("Mobile TopUp API Has Been Updated.")]]);
        }
    }
    //==============================================Top Up Method(Automatic) End=================================================
}
