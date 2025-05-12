<?php

namespace App\Http\Controllers\Admin;

use App\Constants\PaymentGatewayConst;
use App\Exports\MobileTopUpTrxExport;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Providers\Admin\BasicSettingsProvider;
use Maatwebsite\Excel\Facades\Excel;

class SetupMobileTopupController extends Controller
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
        $page_title = __("Mobile Topup Logs");
        $transactions = Transaction::with(
          'user:id,firstname,lastname,email,username,full_mobile',
          'charge:total_charge',
        )->where('type', PaymentGatewayConst::MOBILETOPUP)->latest()->paginate(20);
        return view('admin.sections.mobile-topups.index',compact(
            'page_title','transactions'
        ));
    }

    public function exportData(){
        $file_name = now()->format('Y-m-d_H:i:s') . "_Mobile_Top_Up_Logs".'.xlsx';
        return Excel::download(new MobileTopUpTrxExport, $file_name);
    }

    public function details($id){

        $data = Transaction::where('id',$id)->with(
          'user:id,firstname,lastname,email,username,full_mobile',
          'charge:total_charge',
        )->where('type',PaymentGatewayConst::MOBILETOPUP)->first();
        $pre_title = __("Mobile Topup details for");
        $page_title = $pre_title.'  '.$data->trx_id.' ('.@$data->details->topup_type_name.")";
        return view('admin.sections.mobile-topups.details', compact(
            'page_title',
            'data'
        ));
    }
}
