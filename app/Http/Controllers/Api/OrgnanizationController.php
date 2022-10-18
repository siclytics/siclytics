<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use App\Models\User;
use App\Http\Traits\APIResponse;
use VoipNow;
use Illuminate\Support\Facades\Validator;
use App\Models\OrganizationAssigned;
use Spatie\Permission\Models\Role;

class OrgnanizationController extends Controller
{
    use APIResponse;

    public function getOrganizationsData($provider_id)
    {
        $data = VoipNow::GetOrganizations(['parentID' => $provider_id]);

        //     $data ='[{"ID":3996,"name":"Carlos Paz","firstName":"Carlos","lastName":"Paz","login":"stormfellsolutions","email":"carlos.paz@stormfell.com","company":"StormFell Solutions Inc.","templateID":0,"identifier":"6df5a25d7fa6ad3a9188f228a8e6294b","scope":""},
        // {"ID":473,"name":"Nelson Fiallo","firstName":"Nelson","lastName":"Fiallo","login":"globalconsulting","email":"selective@melotel.com","company":"Selective Consulting","templateID":0,"identifier":"ceb9c212ba6fb099b24a7e4e5c925418","scope":""}]';
        $data = json_decode($data);

        foreach ($data as $key => $value) {
            $get_org = Organization::where('organization_id', $value->ID)->first();
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
                $new_org->last_sync = now();
                // Details field below
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


                // Details field end

                $new_org->save();
            }
        }

        return $this->success($data, 'All Organizations Synchronized!');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->service_provider_id != Null) {
            $orgs = Organization::select('company', 'first_name', 'last_name', 'organization_id', 'service_provider_id', 'status', 'last_sync', 'crDate')->with(['service_provider' => function ($sp) {
                return $sp->select('company', 'service_provider_id');
            }])->withCount('users')
                ->withCount('exts')
                ->with('dids');
              if ($request->search) {
            $orgs->where('company', 'like', '%' . $request->search . '%');
            $orgs->orWhere('first_name', 'like', '%' . $request->search . '%');
            $orgs->orWhere('last_name', 'like', '%' . $request->search . '%');
        }
         if ($request->name_filter) {
            $orgs->where('company', 'like', '%' . $request->name_filter . '%');
        }
         if ($request->contact_filter) {
            $orgs->where('first_name', 'like', '%' . $request->contact_filter . '%');
            $orgs->orWhere('last_name', 'like', '%' . $request->contact_filter . '%');
        }
        if ($request->date_filter) {
            $dates = explode('_', $request->date_filter);
            $orgs->whereBetween('crDate', [$dates[0],$dates[1]]);
        }
              $orgs = $orgs->where('service_provider_id', auth()->user()->service_provider_id)
                ->paginate(50);
            return $this->success($orgs, 'All Organizations!');
        } else {

            $orgs = Organization::select('company', 'first_name', 'last_name', 'organization_id', 'service_provider_id', 'status', 'last_sync', 'crDate')->with(['service_provider' => function ($sp) {
                return $sp->select('company', 'service_provider_id');
            }])->withCount('users')->withCount('exts')->with('dids');
            if ($request->service_provider_id) {
                $orgs->where('service_provider_id', $request->service_provider_id);
            }
            if ($request->search) {
                $orgs->where('company', 'like', '%' . $request->search . '%');
                $orgs->orWhere('first_name', 'like', '%' . $request->search . '%');
                $orgs->orWhere('last_name', 'like', '%' . $request->search . '%');
            }
             if ($request->name_filter) {
                $orgs->where('company', 'like', '%' . $request->name_filter . '%');
            }
             if ($request->contact_filter) {
                $orgs->where('first_name', 'like', '%' . $request->contact_filter . '%');
                $orgs->orWhere('last_name', 'like', '%' . $request->contact_filter . '%');
            }
            if ($request->date_filter) {
                $dates = explode('_', $request->date_filter);
                $orgs->whereBetween('crDate', [$dates[0],$dates[1]]);
            }
            $orgs=$orgs->paginate(50);
                return $this->success($orgs, 'All Organizations!');
            }
    }


    public function getServiceProviderOrganizations($provider_id)
    {
        $orgs = Organization::withCount('users')->where('service_provider_id', $provider_id)->get();
        return $this->success($orgs, 'Organizations Data!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function add_details(Request $request, $id)
    {
        $request->validate([
            'c_name' => 'required',
            'email' => 'required|email',
            'api_key' => 'required',
            'api_secret' => 'required',
            'phone_call_events_api_key' => 'required',
            'dafeeder_company' => 'required',
            'whmcs_client_id' => 'required',
            'payment_method' => 'required',
            'invoice_due_days' => 'required',
            'timezone_shift' => 'required',
        ]);

        $get_record = OrganizationDetail::where('organization_id', $id)->first();
        if (!empty($get_record)) {
            $get_record->c_name = $request->c_name;
            $get_record->email = $request->email;
            $get_record->api_key = $request->api_key;
            $get_record->api_secret = $request->api_secret;
            $get_record->phone_call_events_api_key = $request->phone_call_events_api_key;
            $get_record->dafeeder_company = $request->dafeeder_company;
            $get_record->whmcs_client_id = $request->whmcs_client_id;
            $get_record->payment_method = $request->payment_method;
            $get_record->invoice_due_days = $request->invoice_due_days;
            $get_record->timezone_shift = $request->timezone_shift;
            if ($get_record->save()) {
                return $this->success('Succesfully updated organization details!', $get_record, 200);
            } else {
                return $this->error('Something went wrong!', 'No results', 403);
            }
        } else {
            $new_details = new OrganizationDetail;
            $new_details->organization_id = $id;
            $new_details->c_name = $request->c_name;
            $new_details->email = $request->email;
            $new_details->api_key = $request->api_key;
            $new_details->api_secret = $request->api_secret;
            $new_details->phone_call_events_api_key = $request->phone_call_events_api_key;
            $new_details->dafeeder_company = $request->dafeeder_company;
            $new_details->whmcs_client_id = $request->whmcs_client_id;
            $new_details->payment_method = $request->payment_method;
            $new_details->invoice_due_days = $request->invoice_due_days;
            $new_details->timezone_shift = $request->timezone_shift;
            if ($new_details->save()) {
                return $this->success('Succesfully updated organization details!', $new_details, 200);
            } else {
                return $this->error('Something went wrong!', 'No results', 403);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function details($id)
    {

        $get_org = Organization::select('organization_id','company','service_provider_id')->with(['details'])->with(['service_provider'=>function($service_provider){
            return $service_provider->select('service_provider_id','company','address','country','city','pcode');
        }])->with(['sic_users'=>function($sic_users){
                    return $sic_users->select('id','default_organization_id','username','name','created_at','email')->with(['user_roles'=>function($user_roles){
                        return $user_roles->select('id','name');
                    }]);
                }])
        ->with(['dids'])->with(['exts'])->with(['roles'=>function($roles){
            return $roles->select('id','name as role_name','organization_id');
        }])->where('organization_id', $id)->get();
        if (!empty($get_org)) {
            return $this->success($get_org, 'Organization details!', 200);
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }
    function destroy($id)
    {
        $get_org = Organization::find($id);
        $details = OrganizationDetail::where('organization_id', $id)->first();
        if (!empty($get_org)) {
            $details->delete();
            $get_org->delete();
            return $this->success('Organization deleted!', $get_org, 200);
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }

    function assign_organizations(Request $request){
         $validator = Validator::make($request->all(), [
             'user_id' => 'required|exists:users,id',
            'organization_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $organization_ids =explode(',', $request->organization_ids);
        foreach ($organization_ids as $key => $organization_id) {
              if (!empty($organization_id)) {
                $already_assigned = OrganizationAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'organization_id'=>$organization_id
                ])->first();
                 $check_existance = Organization::where([
                    'organization_id'=>$organization_id
                ])->first();
                 if ($check_existance==null) {
                         
        return $this->error([], 'Sorry organization id '.$organization_id.' not found!',404);
                 }
                if ($already_assigned==null) {
                $assign = new OrganizationAssigned;
                $assign->voip_user_id=$request->user_id;
                $assign->organization_id=$organization_id;
                $assign->save();
              
                }
              }
        }
     
return $this->success($request->extended_numbers, 'Organizations assigned to the user!',200);
    }


    function unassign_organizations(Request $request){
         $validator = Validator::make($request->all(), [
              'user_id' => 'required|exists:users,id',
            'organization_ids' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $organization_ids =explode(',', $request->organization_ids);
        foreach ($organization_ids as $key => $organization_id) {
              if (!empty($organization_id)) {
                $already_assigned = OrganizationAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'organization_id'=>$organization_id
                ])->first();
                 
                 if ($already_assigned==null) {
                         
        return $this->error([], 'Sorry organization id '.$organization_id.' not found in current user record!',404);
                 }
                 else{
                    $already_assigned = OrganizationAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'organization_id'=>$organization_id
                ])->delete();
                 }
                
              }
        }
     
return $this->success($request->extended_numbers, 'Organizations removed successfully!',200);
    }

  function get_assigned_organizations(Request $request){
        $validator = Validator::make($request->all(), [
             'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

       $assigned = OrganizationAssigned::where([
                    'voip_user_id'=>$request->user_id,
                ])->select('voip_user_id','organization_id')->get();
       if ($assigned!=null) {
          return $this->success($assigned, 'All organizations assigned to the user!',200);
       }
       else{
        return $this->error([], 'No record found!',500);
       }
    }

    function set_default_organizations(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'organization_id' => 'required|exists:organizations,organization_id',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }
        $already_assigned = OrganizationAssigned::where([
                    'voip_user_id'=>$request->user_id,
                    'organization_id'=>$request->organization_id
                ])->first();
        if ($already_assigned==null) {
           return $this->error([], 'This orgnization is not in the user organizations list!',500);
        }


        $user_details = User::find($request->user_id);
        $user_details->default_organization_id=$request->organization_id;
        if ($user_details->save()) {
            return $this->success($user_details, 'Default organizations changed successfully!',200);
        }
        else{
            return $this->error([], 'Something went wrong!',500); 
        }

    }

    //  function get_roles($organization_id){
    //   $roles = Role::where('organization_id',$organization_id)->select('id','name')->get();
    //   if ($roles!=Null) {
    //    return $this->success('Organization roles below!', $roles, 200);
    //   }
    //   else{
    //     return $this->error('No roles found against this organization!', 'No results', 403);
    //   }

    // }
}
