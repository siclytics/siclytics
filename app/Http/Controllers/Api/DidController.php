<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Did;
use App\Models\PhoneAssigned;
use App\Http\Traits\APIResponse;
use VoipNow;
use Illuminate\Support\Facades\Validator;

class DidController extends Controller
{
    use APIResponse;

    function GetPublicNoPoll($user_id)
    {

        $data = VoipNow::GetPublicNoPoll(['userID' => $user_id]);
        if (array_key_exists('notice', $data)) {
            $d = $data->notice;
            if ($d->message == 'No records.') {
                return true;
            }
        }
        if (!empty($data->publicNo->assigned)) {
            if (is_array($data->publicNo->assigned)) {
                $loop_data = $data->publicNo->assigned;
                foreach ($loop_data as $key => $value) {
                    $get_did1 = Did::where('did_id', $data->ID)->where('voip_user_id', $user_id)->first();
                    dd($get_did1);
                    if ($get_did1 == null) {
                        $new_did1 = new Did;
                        $new_did1->did_type = 'publicNo';
                        $new_did1->assigned = 'yes';
                        $new_did1->voip_user_id = $user_id;
                        $new_did1->did_id = $value->ID;
                        $new_did1->channelID = $value->channelID;
                        $new_did1->channel = $value->channel;
                        $new_did1->externalNo = $value->externalNo;
                        $new_did1->did = $value->did;
                        $new_did1->cost = $value->cost;
                        $new_did1->callbackExt = $value->callbackExt;
                        $new_did1->callbackExtID = $value->callbackExtID;
                        $new_did1->flow = $value->flow;
                        $new_did1->crDate = $value->crDate;
                        $new_did1->save();
                    } else {
                        $get_did1->did_type = 'publicNo';
                        $get_did1->assigned = 'yes';
                        $get_did1->voip_user_id = $user_id;
                        $get_did1->did_id = $data->ID;
                        $get_did1->channelID = $data->channelID;
                        $get_did1->channel = $data->channel;
                        $get_did1->externalNo = $data->externalNo;
                        $get_did1->did = $data->did;
                        $get_did1->cost = $data->cost;
                        $get_did1->callbackExt = $data->callbackExt;
                        $get_did1->callbackExtID = $data->callbackExtID;
                        $get_did1->flow = $data->flow;
                        $get_did1->crDate = $data->crDate;
                        $get_did1->save();
                    }
                    // /test

                }
            } else {
                $data = $data->publicNo->assigned;
                $get_did = Did::where('did_id', $data->ID)->where('voip_user_id', $user_id)->first();
                dd($get_did);
                if ($get_did == null) {
                    $new_did = new Did;
                    $new_did->did_type = 'publicNo';
                    $new_did->assigned = 'yes';
                    $new_did->voip_user_id = $user_id;
                    $new_did->did_id = $data->ID;
                    $new_did->channelID = $data->channelID;
                    $new_did->channel = $data->channel;
                    $new_did->externalNo = $data->externalNo;
                    $new_did->did = $data->did;
                    $new_did->cost = $data->cost;
                    $new_did->callbackExt = $data->callbackExt;
                    $new_did->callbackExtID = $data->callbackExtID;
                    $new_did->flow = $data->flow;
                    $new_did->crDate = $data->crDate;
                    $new_did->save();
                } else {
                    $get_did->did_type = 'publicNo';
                    $get_did->assigned = 'yes';
                    $get_did->voip_user_id = $user_id;
                    $get_did->did_id = $data->ID;
                    $get_did->channelID = $data->channelID;
                    $get_did->channel = $data->channel;
                    $get_did->externalNo = $data->externalNo;
                    $get_did->did = $data->did;
                    $get_did->cost = $data->cost;
                    $get_did->callbackExt = $data->callbackExt;
                    $get_did->callbackExtID = $data->callbackExtID;
                    $get_did->flow = $data->flow;
                    $get_did->crDate = $data->crDate;
                    $get_did->save();
                }
            }
        }
    }

    function index(Request $request)
    {

        if (auth()->user()->service_provider_id != Null) {
            $dids = Did::select('did', 'cost', 'callbackExt', 'voip_user_id', 'crDate')->with(['users' => function ($user) {
                return $user->select('voip_user_id', 'organization_id', 'company');
            }])->with(['organization' => function ($organization) {
                return $organization->with(['service_provider' => function ($sps) {
                    return $sps->select('service_provider_id', 'company', 'address', 'city', 'pcode', 'country');
                }]);
            }]);
            // Filters start
        if ($request->search) {
            $dids->where('callbackExt', 'like', '%' . $request->search . '%');
            $dids->orWhere('did', 'like', '%' . $request->search . '%');
            $dids->orWhere('externalNo', 'like', '%' . $request->search . '%');
        }
        
        if ($request->callback_ext_filter) {
            $dids->where('callbackExt', 'like', '%' . $request->callback_ext_filter . '%');
        }
         if ($request->did_type_filter) {
            $dids->where('did_type', 'like', '%' . $request->did_type_filter . '%');
        }
        if ($request->date_filter) {
            $dates = explode('_', $request->date_filter);
            $dids->whereBetween('crDate', [$dates[0],$dates[1]]);
        }
        // Filters end
            $dids = $dids->where('organization.service_provider_id', auth()->user()->service_provider_id)
                ->paginate(50);
            return $this->success($dids, 'All Dids!');
        } else {
            $dids = Did::select('did', 'cost', 'callbackExt', 'voip_user_id', 'crDate')->with(['users' => function ($user) {
                return $user->select('voip_user_id', 'organization_id', 'company');
            }])->with(['organization' => function ($organization) {
                return $organization->with(['service_provider' => function ($sps) {
                    return $sps->select('service_provider_id', 'company', 'address', 'city', 'pcode', 'country');
                }]);
            }]);
              if ($request->search) {
            $dids->where('callbackExt', 'like', '%' . $request->search . '%');
            $dids->orWhere('did', 'like', '%' . $request->search . '%');
            $dids->orWhere('externalNo', 'like', '%' . $request->search . '%');
        }
        
        if ($request->callback_ext_filter) {
            $dids->where('callbackExt', 'like', '%' . $request->callback_ext_filter . '%');
        }
         if ($request->did_type_filter) {
            $dids->where('did_type', 'like', '%' . $request->did_type_filter . '%');
        }
        if ($request->date_filter) {
            $dates = explode('_', $request->date_filter);
            $dids->whereBetween('crDate', [$dates[0],$dates[1]]);
        }
        $dids = $dids->paginate(50);
            return $this->success($dids, 'All Dids!');
        }
    }

     function assign_number(Request $request){
         $validator = Validator::make($request->all(), [
           'user_id' => 'required|exists:users,id',
            'phone_numbers' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $phone_numbers =explode(',', $request->phone_numbers);
        foreach ($phone_numbers as $key => $phone_number) {
              if (!empty($phone_number)) {
                $already_assigned = PhoneAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'phone_number'=>$phone_number
                ])->first();
                 $check_existance = Did::where([
                    'did'=>$phone_number
                ])->first();
                 if ($check_existance==null) {
                         
        return $this->error([], 'Sorry phone number '.$phone_number.' not found!',404);
                 }
                if ($already_assigned==null) {
                $assign = new PhoneAssigned;
                $assign->voip_user_id=$request->user_id;
                $assign->phone_number=$phone_number;
                $assign->save();
              
                }
              }
        }
     
return $this->success($request->phone_numbers, 'All phone numbers assigned to the user!',200);
    }


    function unassign_number(Request $request){
         $validator = Validator::make($request->all(), [
           'user_id' => 'required|exists:users,id',
            'phone_numbers' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $phone_numbers =explode(',', $request->phone_numbers);
        foreach ($phone_numbers as $key => $phone_number) {
              if (!empty($phone_number)) {
                $already_assigned = PhoneAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'phone_number'=>$phone_number
                ])->first();
                 
                 if ($already_assigned==null) {
                         
        return $this->error([], 'Sorry phone number '.$phone_number.' not found in current user record!',404);
                 }
                 else{
                    $already_assigned = PhoneAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'phone_number'=>$phone_number
                ])->delete();
                 }
                
              }
        }
     
return $this->success($request->phone_numbers, 'All phone numbers removed successfully!',200);
    }

    function get_assigned_number(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',

        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

       $assigned = PhoneAssigned::where([
                    'voip_user_id'=>$request->user_id,
                ])->select('voip_user_id','phone_number')->get();
       if ($assigned!=null) {
          return $this->success($assigned, 'All phone numbers assigned to the user!',200);
       }
       else{
        return $this->error([], 'No record found!',500);
       }
    }
}
