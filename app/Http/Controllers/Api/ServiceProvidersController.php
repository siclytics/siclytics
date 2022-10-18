<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use App\Http\Traits\APIResponse;
use App\Models\User;
use App\Models\Extension;
use App\Models\ServiceProviderDetails;
use Auth;
use VoipNow;
use DB;
use Spatie\Permission\Models\Role;

class ServiceProvidersController extends Controller
{
    use APIResponse;

    public function getServiceProvidersData()
    {
        $data = VoipNow::GetServiceProviders();
        // Sample Data below
        //         $data = '[{"ID":7024,"name":"Carlos mailto:paz","firstname":"carlos","lastname":"paz","login":"stormfell","email":"info@stormfell.com","company":"Stormfell Solutions Inc.","templateID":0,"identifier":"a96127013038e410749fd1db69a3c123","scope":""},
        //     {"ID":11,"name":"Gerald mailto:danais","firstname":"gerald","lastname":"danais","login":"danaistechnologies","email":"helpdesk@zoomfone.com","company":"Zoomfone Inc","templateID":0,"identifier":"af3a578f7af705c8cb262c21898a42ca","scope":""},
        //     {"ID":10,"name":"John mailto:meloche","firstname":"john","lastname":"meloche","login":"melotelinc","email":"webmaster@melotel.com","company":"MeloTel Phone Company","templateID":0,"identifier":"dbaec03f0b8baddbaef5868e6c02a5d5","scope":""},
        //     {"ID":7025,"name":"Tony mailto:dockrill","firstname":"tony","lastname":"dockrill","login":"doctnet","email":"anthony@wireitup.ca","company":"DocNet","templateID":0,"identifier":"d615e296af662aae5bff53f277478c01","scope":""}]
        // ';
        // Sample Data ends here

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
            }
        }

        return $this->success('All Service Providers Synchronized!',$data, 200);
    }

    public function get_sps(Request $request)
    {
        $spss = ServiceProvider::with([
                    'users'=>function($query){
                    return $query->withCount('exts');

                }])->get();
        $ex = 0;
        foreach($spss as $key=>$sp ){
            $user_ext = $sp->users->pluck('exts_count')->toArray();
            $sp_ext[$sp->id]['ext_count'] = array_sum($user_ext);
            $sp_ext[$sp->id]['sp_id'] = $sp->id;
        }

        $sps = 
                ServiceProvider::select('id','company', 'first_name', 'last_name', 'crDate', 'service_provider_id')
                ->withCount('organizations')
                ->withCount('users');

        if ($request->search) {
            $sps->where('company', 'like', '%' . $request->search . '%');
            $sps->orWhere('first_name', 'like', '%' . $request->search . '%');
            $sps->orWhere('last_name', 'like', '%' . $request->search . '%');
        }
        if ($request->name_filter) {
            $sps->where('company', 'like', '%' . $request->name_filter . '%');
        }
         if ($request->contact_filter) {
            $sps->where('first_name', 'like', '%' . $request->contact_filter . '%');
            $sps->orWhere('last_name', 'like', '%' . $request->contact_filter . '%');
        }
        if ($request->date_filter) {
            $dates = explode('_', $request->date_filter);
            $sps->whereBetween('crDate', [$dates[0],$dates[1]]);
        }
        $sps = $sps->paginate(50);

        foreach($sps as $key =>$value){
           $value['ext_count'] = (isset($sp_ext[$value->id]['ext_count'])) ? $sp_ext[$value->id]['ext_count'] : null;
           $sps[] = $value;
        }
    
        return $this->success($sps, 'All Service Providers!');
    }

    public function details($id)
    {

        $get_sp = ServiceProvider::with('details')->with(['roles'=>function($roles){
            return $roles->where('organization_id',null)->select('service_provider_id','name','created_at');
        }])
            ->with(['organizations' => function ($organizations) {
                return $organizations->select('company', 'first_name', 'last_name', 'organization_id', 'service_provider_id', 'status', 'last_sync', 'crDate')->withCount(['users', 'exts'])->paginate(50);
            }])
            ->with(['sic_users'=>function($sic_users){
                    return $sic_users->where('default_organization_id',null)->select('id','service_provider_id','username','name')->with(['user_roles'=>function($user_roles){
                        return $user_roles->select('id','name');
                    }]);
                }])
            ->where('service_provider_id', $id)->select('id','service_provider_id','company')->first();
            // dd($get_sp->toSql());
        if (!empty($get_sp)) {
            return $this->success($get_sp, 'Service provider details!', 200);
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }


    public function update(Request $request)
    {

        $request->validate([
            'service_provider_id' => 'required',
            'company_name' => 'required',
            'api_key' => 'required',
            'api_secret' => 'required',
            'whmcs_client_id' => 'required',
            'whmcs_payment_method' => 'required',
            'email' => 'required|email',
            'invoice_due_days' => 'required',
        ]);

        //SP update api call here


        //End SP update api call here

        $update_sp = ServiceProviderDetails::where('service_provider_id', $request->service_provider_id)->first();
        if ($update_sp != null) {
            $update_sp->c_name = $request->company_name;
            $update_sp->api_key    = $request->api_key;
            $update_sp->whmcs_client_id    = $request->whmcs_client_id;
            $update_sp->payment_method    = $request->whmcs_payment_method;
            $update_sp->email    = $request->email;
            $update_sp->api_secret    = $request->api_secret;
            $update_sp->invoice_due_date    = $request->invoice_due_days;
            if ($update_sp->save()) {
                return $this->success($update_sp,'Succesfully updated service provider!', 200);
            } else {
                return $this->error('Something went wrong!', 'No results', 403);
            }
        } else {
            $update_sp = new ServiceProviderDetails;
            $update_sp->service_provider_id = $request->service_provider_id;
            $update_sp->c_name = $request->company_name;
            $update_sp->api_key    = $request->api_key;
            $update_sp->whmcs_client_id    = $request->whmcs_client_id;
            $update_sp->payment_method    = $request->whmcs_payment_method;
            $update_sp->email    = $request->email;
            $update_sp->api_secret    = $request->api_secret;
            $update_sp->invoice_due_date    = $request->invoice_due_days;

            if ($update_sp->save()) {
                return $this->success( $update_sp,'Succesfully updated service provider!', 200);
            } else {
                return $this->error([],'Something went wrong!', 'No results', 403);
            }
        }
    }


    public function add_details(Request $request, $id)
    {


        $request->validate([
            'provider_name' => 'required',
            'c_name' => 'required',
            'email' => 'required|email',
            'api_key' => 'required',
            'api_secret' => 'required',
            'whmcs_client_id' => 'required',
            'payment_method' => 'required',
            'invoice_due_date' => 'required',
        ]);

        $get_record = ServiceProviderDetails::where('service_provider_id', $id)->first();
        if (!empty($get_record)) {
            $get_record->provider_name = $request->provider_name;
            $get_record->c_name = $request->c_name;
            $get_record->email = $request->email;
            $get_record->api_key = $request->api_key;
            $get_record->api_secret = $request->api_secret;
            $get_record->whmcs_client_id = $request->whmcs_client_id;
            $get_record->payment_method = $request->payment_method;
            $get_record->invoice_due_date = $request->invoice_due_date;

            if ($get_record->save()) {
                return $this->success('Succesfully updated service provider details!', $get_record, 200);
            } else {
                return $this->error('Something went wrong!', 'No results', 403);
            }
        } else {
            $new_details = new ServiceProviderDetails;
            $new_details->service_provider_id = $id;
            $new_details->provider_name = $request->provider_name;
            $new_details->c_name = $request->c_name;
            $new_details->email = $request->email;
            $new_details->api_key = $request->api_key;
            $new_details->api_secret = $request->api_secret;
            $new_details->whmcs_client_id = $request->whmcs_client_id;
            $new_details->payment_method = $request->payment_method;
            $new_details->invoice_due_date = $request->invoice_due_date;
            if ($new_details->save()) {
                return $this->success('Succesfully updated service provider details!', $new_details, 200);
            } else {
                return $this->error('Something went wrong!', 'No results', 403);
            }
        }
    }

    function destroy($id)
    {
        $get_sp = ServiceProvider::find($id);
        $details = ServiceProviderDetails::find($id);
        if (!empty($get_org)) {
            $details->delete();
            $get_sp->delete();
            return $this->success('Service provider deleted!', $get_sp, 200);
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }

    // function get_roles($sp_id){
    //   $roles = Role::where('service_provider_id',$sp_id)->select('id','name')->get();
    //   if ($roles!=Null) {
    //    return $this->success('Service provider roles below!', $roles, 200);
    //   }
    //   else{
    //     return $this->error('No roles found against this service provider!', 'No results', 403);
    //   }

    // }
}
