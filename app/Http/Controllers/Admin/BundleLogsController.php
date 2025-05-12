<?php

namespace App\Http\Controllers\Admin;

use App\Constants\DataBundleConst;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Http\Request;

class BundleLogsController extends Controller
{
    protected $basic_settings;

    public function __construct()
    {
            $this->basic_settings = BasicSettingsProvider::get();
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __("Bundle Logs");
        $transactions = Transaction::with(
          'user:id,firstname,lastname,email,username,full_mobile',
          'charge:total_charge',
        )->where('type', DataBundleConst::TOPUP_BUNDLE)->latest()->paginate(20);
        return view('admin.sections.data-bundles.index',compact(
            'page_title','transactions'
        ));
    }

    public function details($id){

        $data = Transaction::where('id',$id)->with(
          'user:id,firstname,lastname,email,username,full_mobile',
          'charge:total_charge',
        )->where('type',DataBundleConst::TOPUP_BUNDLE)->first();
        $pre_title = __("Bundle details for");
        $page_title = $pre_title.'  '.$data->trx_id.' ('.@$data->details->topup_type_name.")";
        return view('admin.sections.data-bundles.details', compact(
            'page_title',
            'data'
        ));
    }
}
