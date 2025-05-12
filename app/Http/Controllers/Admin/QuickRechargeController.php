<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\QuickRecharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class QuickRechargeController extends Controller
{
    public function index()
    {
        $slug = 'quick-recharge';
        $buttons = QuickRecharges::where("key", $slug)->first();
        $page_title = 'Quick Add Money';
        return view('admin.sections.quick-recharge.index', compact('page_title','buttons'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['amount' => 'required']);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'amount-add');

        $slug = 'quick-recharge';
        $button_sec = QuickRecharges::where("key", $slug)->first();

        if ($button_sec != null) {
            $button_data = json_decode(json_encode($button_sec->buttons), true);
        } else {
            $button_data = [];
        }

        $validate = $validator->validate();
        $unique_id = uniqid();

        $button_data['items'][$unique_id]['id'] = $unique_id;
        $button_data['items'][$unique_id]['amount'] = $validate['amount'];
        $button_data['items'][$unique_id]['status'] = 1;
        $update_data['key'] = $slug;
        $update_data['buttons']  = $button_data;

        try {
            QuickRecharges::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),
         [
            'amount_edit' => 'required',
            'target'    => "required|string",
        ]);
        if ($validator->fails()) return back()->withErrors($validator)->withInput()->with('modal', 'amount-edit');

        $slug = 'quick-recharge';
        $button_sec = QuickRecharges::where("key", $slug)->first();

        if (!$button_sec) return back()->with(['error' => [__('Buttons data not found!')]]);
        $buttons = json_decode(json_encode($button_sec->buttons), true);
        if (!isset($buttons['items'])) return back()->with(['error' => [__('Buttons item not found!')]]);
        if (!array_key_exists($request->target, $buttons['items'])) return back()->with(['error' => [__('button is invalid!')]]);

        // dd($buttons);

        $buttons['items'][$request->target]['id'] = $request->target;
        $buttons['items'][$request->target]['amount'] = $request->amount_edit;

        try {
            $button_sec->update([
                'buttons' => $buttons,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Button updated successfully!')]]);
    }


    public function delete(Request $request)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = 'quick-recharge';
        $button_sec = QuickRecharges::where("key", $slug)->first();

        if (!$button_sec) return back()->with(['error' => [__('Buttons data not found!')]]);
        $buttons = json_decode(json_encode($button_sec->buttons), true);
        if (!isset($buttons['items'])) return back()->with(['error' => [__('Buttons item not found!')]]);
        if (!array_key_exists($request->target, $buttons['items'])) return back()->with(['error' => [__('button is invalid!')]]);

        try {

            unset($buttons['items'][$request->target]);
            $button_sec->update([
                'buttons'     => $buttons,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Section item deleted successfully!')]]);
    }
}
