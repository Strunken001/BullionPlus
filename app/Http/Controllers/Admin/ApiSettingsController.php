<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiSettingsController extends Controller
{
    public function index()
    {
        $page_title = __("API Settings");
        $data = ApiClient::where('user_id', auth()->id())->first();

        return view('admin.sections.api-settings.index', compact(
            'page_title',
            'data'
        ));
    }

    public function generateKeys()
    {
        $api_key = null;
        $secret_key = null;

        $unique_api_key = false;
        while (!$unique_api_key) {
            $api_key = Str::random(32);
            $exists = ApiClient::where('api_key', $api_key)->first();
            if (!$exists) {
                $unique_api_key = true;
            }
        }

        $unique_secret_key = false;
        while (!$unique_secret_key) {
            $secret_key = "SK_" . Str::random(64);
            $exists = ApiClient::where('secret_key', $secret_key)->first();
            if (!$exists) {
                $unique_secret_key = true;
            }
        }

        $api_client = ApiClient::where('user_id', auth()->id())->first();
        if ($api_client) {
            $api_client->api_key = $api_key;
            $api_client->secret_key = $secret_key;
            $api_client->save();
        } else {
            $api_client = new ApiClient();
            $api_client->api_key = $api_key;
            $api_client->secret_key = $secret_key;
            $api_client->user_id = auth()->id();
            $api_client->save();
        }

        return redirect()->route('admin.api.settings.index')->with(['success' => [__('API keys generated successfully')]]);
    }
}
