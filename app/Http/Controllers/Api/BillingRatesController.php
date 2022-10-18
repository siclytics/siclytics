<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BillingRate;
use App\Models\DefaultBillingRates;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\APIResponse;

class BillingRatesController extends Controller
{
    use APIResponse;

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_provider_id' => 'required|exists:service_providers,service_provider_id',
            // 'organization_id' => 'required|exists:organizations,organization_id',
            'item_name' => 'required',
            'description' => 'required',
            'discount_item' => 'required',
            'discount_value' => 'required',
            'rate' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $new_billing_rate = BillingRate::find($request->rate_id);
        if (!empty($new_billing_rate)) {
            $new_billing_rate->service_provider_id = $request->service_provider_id;
            $new_billing_rate->organization_id = $request->organization_id;
            $new_billing_rate->item_name = $request->item_name;
            $new_billing_rate->description = $request->description;
            $new_billing_rate->discount_item = $request->discount_item;
            $new_billing_rate->discount_value = $request->discount_value;
            $new_billing_rate->rate = $request->rate;
        } else {
            $new_billing_rate = new BillingRate;
            $new_billing_rate->service_provider_id = $request->service_provider_id;
            $new_billing_rate->organization_id = $request->organization_id;
            $new_billing_rate->item_name = $request->item_name;
            $new_billing_rate->description = $request->description;
            $new_billing_rate->discount_item = $request->discount_item;
            $new_billing_rate->discount_value = $request->discount_value;
            $new_billing_rate->rate = $request->rate;
        }

        if ($new_billing_rate->save()) {

            return $this->success('Succesfully saved billing rate!', $new_billing_rate, 200);
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }
    function get_all_rates(Request $request)
    {
        $rates = [];
        $other_rates = [];
        if ($request->service_provider_id != Null && $request->organization_id != Null) {
            $rates = BillingRate::where([
                'service_provider_id' => $request->service_provider_id,
                'organization_id' => $request->organization_id
            ])->select('id', 'organization_id', 'item_name', 'description', 'discount_item', 'discount_value', 'rate')->get();
            $already_selected = $rates->pluck('item_name');
            $other_rates = DefaultBillingRates::select('item_name', 'description', 'discount_item', 'discount_value', 'rate')->whereNotIn('item_name', $already_selected)->get();
        } elseif ($request->service_provider_id != Null && $request->organization_id == Null) {
            $rates = BillingRate::where([
                'service_provider_id' => $request->service_provider_id,
                'organization_id' => Null,
            ])->select('id', 'service_provider_id', 'item_name', 'description', 'discount_item', 'discount_value', 'rate')->get();
            $already_selected = $rates->pluck('item_name');
            $other_rates = DefaultBillingRates::select('item_name', 'description', 'discount_item', 'discount_value', 'rate')->whereNotIn('item_name', $already_selected)->get();
        } else {
            $other_rates = DefaultBillingRates::select('item_name', 'description', 'discount_item', 'discount_value', 'rate')->get();
        }
        if (!empty($rates)) {
            return $this->success('Billing rated details!', ['selected_rates' => $rates, 'other_rates' => $other_rates], 200);
        } else {
            $rates = DefaultBillingRates::get();
            return $this->success('Billing rated details!', $rates, 200);
        }
    }
}
