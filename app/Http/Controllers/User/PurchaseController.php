<?php

namespace App\Http\Controllers\User;

use App\Constants\PaymentGatewayConst;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function purchaseView(){
        $purchase_type = Transaction::select('type')->ExceptAddMoney()->distinct()->get();
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer       = SiteSections::getData($section_slug)->first();
        return view('user.page.purchase-history',compact('purchase_type','footer'));
    }

    public function getPaginatedHistory(Request $request, $type)
    {
        $data = getHistory($type);

        if ($request->ajax()) {
            return view('user.components.table.purchase-history-table', compact('data', 'type'))->render();
        }

        // For non-AJAX requests
        return view('user.page.recharge-history', compact('data', 'type'));
    }
}
