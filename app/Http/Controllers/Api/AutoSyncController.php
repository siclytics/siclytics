<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use App\Models\Organization;
use App\Models\VoipUser;
use App\Models\Extension;
use App\Http\Traits\APIResponse;
use App\Models\Did;
use VoipNow;

class AutoSyncController extends Controller
{
    use APIResponse;

    function index()
    {
        set_time_limit(0);
        $data = VoipNow::GetServiceProviders();
        if (!empty($data)) {

            $data = json_decode($data);

            foreach ($data as $key => $value) {

                $get_sp = ServiceProvider::where('service_provider_id', $value->ID)->first();
                if ($get_sp == null) {

                    $details = VoipNow::GetServiceProviderDetails(['ID' => $value->ID]);
                    $new_sp = new ServiceProvider;
                    $new_sp->first_name = $details->firstName;
                    $new_sp->last_name = $details->lastName;
                    $new_sp->service_provider_id = $details->ID;
                    $new_sp->contact = $details->email;
                    $new_sp->login = $details->login;
                    $new_sp->company = $details->company;
                    $new_sp->last_sync = now();
                    $new_sp->template_id = $details->templateID;
                    $new_sp->identifier = $details->identifier;
                    $new_sp->scope = $details->scope;
                    $new_sp->status = $details->status;

                    // Details field below
                    $new_sp->phone = $details->phone;
                    $new_sp->fax = $details->fax;
                    $new_sp->address = $details->address;
                    $new_sp->city = $details->city;
                    $new_sp->pcode = $details->pcode;
                    $new_sp->country = $details->country;
                    $new_sp->region = $details->region;
                    $new_sp->timezone = $details->timezone;
                    $new_sp->interfaceLang = $details->interfaceLang;
                    $new_sp->notes = $details->notes;
                    $new_sp->serverID = $details->serverID;
                    $new_sp->chargingIdentifier = $details->chargingIdentifier;
                    $new_sp->phoneStatus = $details->phoneStatus;
                    $new_sp->cpAccess = $details->cpAccess;
                    $new_sp->parentID = $details->parentID;
                    $new_sp->parentIdentifier = $details->parentIdentifier;
                    $new_sp->chargingPlanID = $details->chargingPlanID;
                    $new_sp->chargingPlanIdentifier = $details->chargingPlanIdentifier;
                    $new_sp->chargingPlan = $details->chargingPlan;
                    $new_sp->parentName = $details->parentName;
                    $new_sp->crDate = $details->crDate;
                    $new_sp->link = json_encode($details->link);


                    // Details field end
                    $new_sp->save();
                }
                $this->getOrganizationsData($value->ID);
            }
            return $this->success([], 'All Data Synchronized successfully!');
        } else {
            return $this->error('No record found!', 404);
        }
    }



    public function getOrganizationsData($provider_id)
    {

        $data = VoipNow::GetOrganizations(['parentID' => $provider_id]);
        $data = json_decode($data);
        foreach ($data as $key => $value) {
            $get_org = Organization::where('organization_id', $value->ID)->first();
            $this->getUsersData($value->ID);
            if ($get_org == null) {
                $details = VoipNow::GetOrganizationDetails(['ID' => $value->ID]);
                $new_org = new Organization;
                $new_org->service_provider_id = $provider_id;
                $new_org->organization_id = $details->ID;
                $new_org->first_name = $details->firstName;
                $new_org->last_name = $details->lastName;
                $new_org->email = $details->email;
                $new_org->login = $details->login;
                $new_org->company = $details->company;
                $new_org->template_id = $details->templateID;
                $new_org->identifier = $details->identifier;
                $new_org->scope = $details->scope;
                $new_org->status = $details->status;
                $new_org->phone = $details->phone;
                $new_org->fax = $details->fax;
                $new_org->address = $details->address;
                $new_org->city = $details->city;
                $new_org->pcode = $details->pcode;
                $new_org->country = $details->country;
                $new_org->region = $details->region;
                $new_org->timezone = $details->timezone;
                $new_org->interfaceLang = $details->interfaceLang;
                $new_org->notes = $details->notes;
                $new_org->serverID = $details->serverID;
                $new_org->chargingIdentifier = $details->chargingIdentifier;
                $new_org->phoneStatus = $details->phoneStatus;
                $new_org->cpAccess = $details->cpAccess;
                $new_org->parentID = $details->parentID;
                $new_org->parentIdentifier = $details->parentIdentifier;
                $new_org->chargingPlanID = $details->chargingPlanID;
                $new_org->chargingPlanIdentifier = $details->chargingPlanIdentifier;
                $new_org->chargingPlan = $details->chargingPlan;
                $new_org->parentName = $details->parentName;
                $new_org->crDate = $details->crDate;
                $new_org->link = json_encode($details->link);
                $new_org->save();
            }
        }
    }


    function getUsersData($org_id)
    {
        set_time_limit(0);
        $data = VoipNow::GetUsers(['parentID' => $org_id]);
        $data = json_decode($data);
        if (array_key_exists('message', $data)) {
            if ($data->message == 'No records.') {
                return true;
            }
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $get_user = VoipUser::where('voip_user_id', $value->ID)->first();
                // $this->getExtensionsData($value->ID);
                // $this->GetPublicNoPoll($value->ID);
                if ($get_user == null) {
                    $details = VoipNow::GetUserDetails(['ID' => $value->ID]);
                    $new_user = new VoipUser;
                    $new_user->organization_id = $org_id;
                    $new_user->voip_user_id = $details->ID;
                    $new_user->first_name = $details->firstName;
                    $new_user->last_name = $details->lastName;
                    $new_user->email = $details->email;
                    $new_user->login = $details->login;
                    $new_user->company = $details->company;
                    $new_user->template_id = $details->templateID;
                    $new_user->identifier = $details->identifier;
                    $new_user->scope = $details->scope;
                    $new_user->status = $details->status;
                    // Details field below
                    $new_user->phone = $details->phone;
                    $new_user->fax = $details->fax;
                    $new_user->address = $details->address;
                    $new_user->city = $details->city;
                    $new_user->pcode = $details->pcode;
                    $new_user->country = $details->country;
                    $new_user->region = $details->region;
                    $new_user->timezone = $details->timezone;
                    $new_user->interfaceLang = $details->interfaceLang;
                    $new_user->notes = $details->notes;
                    $new_user->serverID = $details->serverID;
                    $new_user->chargingIdentifier = $details->chargingIdentifier;
                    $new_user->cpAccess = $details->cpAccess;
                    $new_user->parentID = $details->parentID;
                    $new_user->parentIdentifier = $details->parentIdentifier;
                    $new_user->chargingPlanID = $details->chargingPlanID;
                    $new_user->chargingPlanIdentifier = $details->chargingPlanIdentifier;
                    $new_user->chargingPlan = $details->chargingPlan;
                    $new_user->parentName = $details->parentName;
                    $new_user->crDate = $details->crDate;
                    $new_user->save();
                }
            }
        } else {
            $get_user = VoipUser::where('voip_user_id', $data->ID)->first();
            $details = VoipNow::GetUserDetails(['ID' => $data->ID]);

            // $this->getExtensionsData($data->ID);
            //  $this->GetPublicNoPoll($data->ID);

            if ($get_user == null) {
                $new_user = new VoipUser;
                $new_user->organization_id = $org_id;
                $new_user->voip_user_id = $details->ID;
                $new_user->first_name = $details->firstName;
                $new_user->last_name = $details->lastName;
                $new_user->email = $details->email;
                $new_user->login = $details->login;
                $new_user->company = $details->company;
                $new_user->template_id = $details->templateID;
                $new_user->identifier = $details->identifier;
                $new_user->scope = $details->scope;
                $new_user->status = $details->status;
                // Details field below
                $new_user->phone = $details->phone;
                $new_user->fax = $details->fax;
                $new_user->address = $details->address;
                $new_user->city = $details->city;
                $new_user->pcode = $details->pcode;
                $new_user->country = $details->country;
                $new_user->region = $details->region;
                $new_user->timezone = $details->timezone;
                $new_user->interfaceLang = $details->interfaceLang;
                $new_user->notes = $details->notes;
                $new_user->serverID = $details->serverID;
                $new_user->chargingIdentifier = $details->chargingIdentifier;
                $new_user->cpAccess = $details->cpAccess;
                $new_user->parentID = $details->parentID;
                $new_user->parentIdentifier = $details->parentIdentifier;
                $new_user->chargingPlanID = $details->chargingPlanID;
                $new_user->chargingPlanIdentifier = $details->chargingPlanIdentifier;
                $new_user->chargingPlan = $details->chargingPlan;
                $new_user->parentName = $details->parentName;
                $new_user->crDate = $details->crDate;
                $new_user->save();
            }
        }

        return true;
    }
    public function getExtensionsData($user_id)
    {
        set_time_limit(0);
        $data = VoipNow::GetExtensions(['parentID' => $user_id]);

        $data = json_decode($data);
        if (array_key_exists('message', $data)) {
            if ($data->message == 'No records.') {
                return true;
            }
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $get_ext = Extension::where('identifier', $value->identifier)->where('voip_user_id', $user_id)->first();
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
            $get_ext = Extension::where('identifier', $data->identifier)->where('voip_user_id', $user_id)->first();
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


        return true;
    }


    function GetPublicNoPoll($user_id)
    {
        set_time_limit(0);

        $data = VoipNow::GetPublicNoPoll(['userID' => 7032]);
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
                    $get_did1 = Did::where('did_id', $value->ID)->where('voip_user_id', $user_id)->first();
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
            return true;
        }
    }

    function check_users()
    {
        $orgs = VoipUser::get();
        // foreach ($orgs as $key => $value) {
        $this->GetPublicNoPoll(7019);
        // }
        // $data = VoipNow::GetServiceProviderPL(['ID'=>11]);
        dd('data fetched');
    }

    function test()
    {
        $last_org = Extension::where('available_caller_ids','!=',Null)->latest()->first();
        $orgs = Extension::where('id','>',$last_org->id+1)->get();
        foreach ($orgs as $key => $value) {
            echo $value->id;
        $data = VoipNow::GetAvailableCallerID(['extendedNumber'=>$value->extended_number]);
        if ($data) {
           $get_ext = Extension::where('extended_number', $value->extended_number)->first();
             if ($get_ext!=null) {
                 $get_ext->available_caller_ids=json_encode($data);
             }
               $get_ext->save();
                }
             
        }
        // $data = VoipNow::GetServiceProviderPL(['ID'=>11]);
        dd('data fetched');
    }
}
