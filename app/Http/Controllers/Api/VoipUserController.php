<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\APIResponse;
use App\Models\VoipUser;
use App\Models\Organization;
use App\Models\UserGroups;
use Auth;
use VoipNow;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class VoipUserController extends Controller
{
  use APIResponse;

  function getUsersData($org_id)
  {
    $data = VoipNow::GetUsers(['parentID' => $org_id]);
    $data = json_decode($data);

    if (is_array($data)) {
      foreach ($data as $key => $value) {
        $get_user = VoipUser::where('voip_user_id', $value->ID)->first();
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
      if (array_key_exists('message', $data)) {
        if ($data->message == 'No records.') {
          return $this->success($data, 'All Users Synchronized!');
        }
      }
      $get_user = VoipUser::where('voip_user_id', $data->ID)->first();
      $details = VoipNow::GetUserDetails(['ID' => $data->ID]);

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


    return $this->success($data, 'All Users Synchronized!');
  }

  function sync()
  {
    set_time_limit(0);
    $orgs = Organization::get();
    foreach ($orgs as $key => $value) {
      $org_id = $value->organization_id;
      $data = VoipNow::GetUsers(['parentID' => $org_id]);
      $data = json_decode($data);

      if (is_array($data)) {
        foreach ($data as $key => $value) {
          $get_user = VoipUser::where('voip_user_id', $value->ID)->first();
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
        if (array_key_exists('message', $data)) {
          if ($data->message == 'No records.') {
            echo 'tested';
          }
        } else {
          $get_user = VoipUser::where('voip_user_id', $data->ID)->first();
          $details = VoipNow::GetUserDetails(['ID' => $data->ID]);

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
      }
    }
    return $this->success($data, 'All Users Synchronized!');
  }

  public function index(Request $request)
  {
    if (auth()->user()->default_organization_id != Null) {
      $users = VoipUser::select('first_name', 'last_name', 'company', 'voip_user_id', 'organization_id', 'crDate')->with(['organizations' => function ($organizations) {
        return $organizations->select('company', 'organization_id', 'service_provider_id')->with(['service_provider' => function ($sps) {
          return $sps->select('company', 'service_provider_id');
        }]);
      }])->with(['exts' => function ($exts) {
        return $exts->select('first_name', 'last_name', 'voip_user_id', 'extended_number');
      }])->withCount('groups');
       if ($request->search) {
            $users->where('company', 'like', '%' . $request->search . '%');
            $users->orWhere('first_name', 'like', '%' . $request->search . '%');
            $users->orWhere('last_name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->name_filter) {
            $users->where('company', 'like', '%' . $request->name_filter . '%');
        }
         if ($request->contact_filter) {
            $users->where('first_name', 'like', '%' . $request->contact_filter . '%');
            $users->orWhere('last_name', 'like', '%' . $request->contact_filter . '%');
        }
        if ($request->date_filter) {
            $dates = explode('_', $request->date_filter);
            $users->whereBetween('crDate', [$dates[0],$dates[1]]);
        }

        $users = $users->where('organization_id', auth()->user()->default_organization_id)
        ->paginate(50);
      return $this->success($users, 'All Users!');
    } 
    else {
      $users = VoipUser::select('first_name', 'last_name', 'company', 'voip_user_id', 'organization_id', 'crDate')->with(['organizations' => function ($organizations) {
        return $organizations->select('company', 'organization_id', 'service_provider_id')->with(['service_provider' => function ($sps) {
          return $sps->select('company', 'service_provider_id');
        }]);
      }])->with(['exts' => function ($exts) {
        return $exts->select('first_name', 'last_name', 'voip_user_id', 'extended_number');
      }])->withCount('groups');
       if ($request->search) {
            $users->where('company', 'like', '%' . $request->search . '%');
            $users->orWhere('first_name', 'like', '%' . $request->search . '%');
            $users->orWhere('last_name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->name_filter) {
            $users->where('company', 'like', '%' . $request->name_filter . '%');
        }
         if ($request->contact_filter) {
            $users->where('first_name', 'like', '%' . $request->contact_filter . '%');
            $users->orWhere('last_name', 'like', '%' . $request->contact_filter . '%');
        }
        if ($request->date_filter) {
            $dates = explode('_', $request->date_filter);
            $users->whereBetween('crDate', [$dates[0],$dates[1]]);
        }
        $users = $users->paginate(50);
      return $this->success($users, 'All Users!');
    }
  }

  function get_user_groups()
  {
    set_time_limit(0);
    $voip_users = VoipUser::where('id', '>', '819')->get();
    foreach ($voip_users as $key => $value) {
      $data = VoipNow::GetUserGroups(['ID' => 6723, 'share' => true]);

      if (isset($data['message']) && $data['message'] == 'No records.') {
        continue;
      } else {
        $get_user = UserGroups::where('voip_user_id', $value->voip_user_id)->first();
        if ($get_user == null) {
          foreach ($data as $key => $value1) {
            $new_group = new UserGroups();
            $new_group->voip_user_id = $value->voip_user_id;
            $new_group->group_id = $value1->ID;
            $new_group->name = $value1->name;
            $new_group->code = $value1->code;
            $new_group->status = $value1->status;
            $new_group->save();
          }
        }
      }
    }

    print_r('done');
    exit();
  }
}
