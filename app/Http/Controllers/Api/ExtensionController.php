<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\APIResponse;
use App\Models\Extension;
use App\Models\ExtRegState;
use App\Models\ExtensionsAssigned;
use VoipNow;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Facades\Validator;

class ExtensionController extends Controller
{

    use APIResponse;
    public function getExtensionsData($user_id)
    {
        $data = VoipNow::GetExtensions(['parentID' => $user_id]);
        $data = json_decode($data);
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $get_ext = Extension::where('identifier', $value->identifier)->first();
                if ($get_ext == null) {
                    $details = VoipNow::GetExtensionDetails(['extendedNumber' => $value->extendedNumber]);
                    $new_ext = new Extension;
                    $new_ext->voip_user_id = $user_id;
                    $new_ext->first_name = $value->firstName;
                    $new_ext->last_name = $value->lastName;
                    $new_ext->label = $value->label;
                    $new_ext->email = $value->email;
                    $new_ext->extension_type = $value->extensionType;
                    $new_ext->extended_number = $value->extendedNumber;
                    $new_ext->identifier = $value->identifier;
                    $new_ext->parentName = $details->parentName;
                    $new_ext->parentID = $details->parentID;
                    $new_ext->parentIdentifier = $details->parentIdentifier;
                    $new_ext->extensionNo = $details->extensionNo;
                    $new_ext->crDate = $details->crDate;
                    $new_ext->templateID = $details->templateID;

                    $new_ext->save();
                }
            }
        } else {
            $get_ext = Extension::where('identifier', $data->identifier)->first();
            if ($get_ext == null) {
                $details = VoipNow::GetExtensionDetails(['extendedNumber' => $data->extendedNumber]);
                $new_ext = new Extension;
                $new_ext->voip_user_id = $user_id;
                $new_ext->first_name = $data->firstName;
                $new_ext->last_name = $data->lastName;
                $new_ext->label = $data->label;
                $new_ext->email = $data->email;
                $new_ext->extension_type = $data->extensionType;
                $new_ext->extended_number = $data->extendedNumber;
                $new_ext->identifier = $data->identifier;
                $new_ext->parentName = $details->parentName;
                $new_ext->parentID = $details->parentID;
                $new_ext->parentIdentifier = $details->parentIdentifier;
                $new_ext->extensionNo = $details->extensionNo;
                $new_ext->crDate = $details->crDate;
                $new_ext->templateID = $details->templateID;

                $new_ext->save();
            }
        }
        return $this->success($data, 'All Extensions Synchronized!');
    }

    public function get_extensions(Request $request)
    {
        if (auth()->user()->service_provider_id != Null) {
            $exts = Extension::select('label', 'extended_number', 'voip_user_id', 'crDate','available_caller_ids')->with(['users' => function ($user) {
                return $user->select('voip_user_id', 'organization_id', 'company');
            }])->with(['organization' => function ($organization) {
                return $organization->with(['service_provider' => function ($sps) {
                    return $sps->select('service_provider_id', 'company');
                }]);
            }])->with('reg_state');
            // Filters start
            if ($request->search) {
            $exts->where('label', 'like', '%' . $request->search . '%');
            $exts->orWhere('first_name', 'like', '%' . $request->search . '%');
            $exts->orWhere('last_name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->name_filter) {
            $exts->where('label', 'like', '%' . $request->name_filter . '%');
        }
         if ($request->contact_filter) {
            $exts->where('first_name', 'like', '%' . $request->contact_filter . '%');
            $exts->orWhere('last_name', 'like', '%' . $request->contact_filter . '%');
        }
        if ($request->date_filter) {
            $dates = explode('_', $request->date_filter);
            $exts->whereBetween('crDate', [$dates[0],$dates[1]]);
        }
            // Filters end

            $exts = $exts->where('organization.service_provider_id', auth()->user()->service_provider_id)
                ->paginate(50);
            return $this->success($exts, 'All Extensions!');
        } else {
            $exts = Extension::select('label', 'extended_number', 'voip_user_id', 'crDate','available_caller_ids')->with(['users' => function ($user) {
                return $user->select('voip_user_id', 'organization_id', 'company');
            }])->with(['organization' => function ($organization) {
                return $organization->with(['service_provider' => function ($sps) {
                    return $sps->select('service_provider_id', 'company');
                }]);
            }])->with('reg_state')->with('active_call_state');
             // Filters start
            if ($request->search) {
            $exts->where('label', 'like', '%' . $request->search . '%');
            $exts->orWhere('first_name', 'like', '%' . $request->search . '%');
            $exts->orWhere('last_name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->name_filter) {
            $exts->where('label', 'like', '%' . $request->name_filter . '%');
        }
         if ($request->contact_filter) {
            $exts->where('first_name', 'like', '%' . $request->contact_filter . '%');
            $exts->orWhere('last_name', 'like', '%' . $request->contact_filter . '%');
        }
        if ($request->date_filter) {
            $dates = explode('_', $request->date_filter);
            $exts->whereBetween('crDate', [$dates[0],$dates[1]]);
        }
            // Filters end
            $exts=$exts->paginate(50);
            return $this->success($exts, 'All Extensions!');
        }
    }


    function get_extensions_state(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voip_user_id' => 'required|exists:voip_users,voip_user_id',
            'extended_number' => 'required|exists:extensions,extended_number',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $accessToken = auth()->user()->voipnow_access_token;
        $url = "https://sip3.melotel.com/uapi/extensions/" . $request->voip_user_id . "/" . $request->extended_number . "/presence/";
        $curl = curl_init("$url");

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $accessToken,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);
        if (isset($res->error)) {
            if ($res->error->code == 'access_denied') {
                return $this->success([], 'Error!');
            }
        } else {
            $this->store_ext_state_response($request->voip_user_id, $request->extended_number, $response);
            return $response;
        }
    }

    function store_ext_state_response($voip_user_id, $extended_number, $response)
    {
        $get_ext_state = ExtRegState::where([
            'voip_user_id' => $voip_user_id,
            'extension' => $extended_number,
        ])->first();
        if ($get_ext_state != null) {
            $get_ext_state->voip_user_id = $voip_user_id;
            $get_ext_state->extension = $extended_number;
            $get_ext_state->state_response = $response;
            return $get_ext_state->save();
        } else {
            $new_state = new ExtRegState;
            $new_state->voip_user_id = $voip_user_id;
            $new_state->extension = $extended_number;
            $new_state->state_response = $response;
            return $new_state->save();
        }
    }

    function get_caller_id($extendedNumber){
        $data = VoipNow::GetAvailableCallerID(['extendedNumber'=>$extendedNumber]);
             $get_ext = Extension::where('extended_number', $extendedNumber)->first();
             if ($get_ext!=null) {
                 $get_ext->available_caller_ids=json_encode($data);
             }
        return $get_ext->save();
    }

    function assign_extensions(Request $request){
         $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'extended_numbers' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $extensions =explode(',', $request->extended_numbers);
        foreach ($extensions as $key => $extension) {
              if (!empty($extension)) {
                $already_assigned = ExtensionsAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'extension'=>$extension
                ])->first();
                 $check_existance = Extension::where([
                    'extended_number'=>$extension
                ])->first();
                 if ($check_existance==null) {
                         
        return $this->error([], 'Sorry extension '.$extension.' not found!',404);
                 }
                if ($already_assigned==null) {
                $assign = new ExtensionsAssigned;
                $assign->voip_user_id=$request->user_id;
                $assign->extension=$extension;
                $assign->save();
              
                }
              }
        }
     
return $this->success($request->extended_numbers, 'All Extensions assigned to the user!',200);
    }


    function unassign_extensions(Request $request){
         $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'extended_numbers' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $extensions =explode(',', $request->extended_numbers);
        foreach ($extensions as $key => $extension) {
              if (!empty($extension)) {
                $already_assigned = ExtensionsAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'extension'=>$extension
                ])->first();
                 
                 if ($already_assigned==null) {
                         
        return $this->error([], 'Sorry extension '.$extension.' not found in current user record!',404);
                 }
                 else{
                    $already_assigned = ExtensionsAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'extension'=>$extension
                ])->delete();
                 }
                
              }
        }
     
return $this->success($request->extended_numbers, 'All Extensions removed successfully!',200);
    }

    function get_assigned_extensions(Request $request){
        $validator = Validator::make($request->all(), [
              'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

       $assigned = ExtensionsAssigned::where([
                    'voip_user_id'=>$request->user_id,
                ])->select('voip_user_id','extension')->with(['extension_details'=>function($details){
                    return $details->select('label','extension_type','parentName','extended_number');
                }])->get();
       if ($assigned!=null) {
          return $this->success($assigned, 'All phone numbers assigned to the user!',200);
       }
       else{
        return $this->error([], 'No record found!',500);
       }
    }
}
