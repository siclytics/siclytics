<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\APIResponse;
use App\Models\ServiceProvider;
use App\Models\Organization;
use App\Models\VoipUser;
use App\Models\User;
use App\Models\TeleScopeModel;

class DashboardController extends Controller
{
    use APIResponse;

    function index(Request $request)
    {
        $serviceproviders = '';
        $organizations = '';
        $users = '';
        //Global filter start
        if (!empty($request->from) && !empty($request->to)) {
            $serviceproviders = ServiceProvider::whereBetween('crDate', [$request->from, $request->to])->count();
            $organizations = Organization::whereBetween('created_at', [$request->from, $request->to])->count();
            $users = VoipUser::whereBetween('created_at', [$request->from, $request->to])->count();
        } else {
            $serviceproviders = ServiceProvider::count();
            $organizations = Organization::count();
            $users = VoipUser::count();
        }
        //Global filter end

        //User filter start

        if (!empty($request->users_from) && !empty($request->users_to)) {
            $users = VoipUser::whereBetween('created_at', [$request->users_from, $request->users_to])->count();
        } else {
            $users = VoipUser::count();
        }
        //User filter end

        //Service Providers filter start

        if (!empty($request->sp_from) && !empty($request->sp_to)) {
            $serviceproviders = ServiceProvider::whereBetween('crDate', [$request->sp_from, $request->sp_to])->count();
        } else {
            $serviceproviders = ServiceProvider::count();
        }
        //Service Providers filter end


        //Organizations filter start

        if (!empty($request->org_from) && !empty($request->org_to)) {
            $organizations = Organization::whereBetween('created_at', [$request->org_from, $request->org_to])->count();
        } else {
            $organizations = Organization::count();
        }
        //Organizations filter end

        return $this->success(['totalserviceproviders' => $serviceproviders, 'totalorganizationss' => $organizations, 'totalusers' => $users], 'All Data here.');
    }

    function get_users(Request $request)
    {
        $users = User::where('created_at','!=',null);

        if ($request->service_provider_id!=null && $request->organization_id==null) {
            $users->where([
                'service_provider_id'=>$request->service_provider_id,
                'default_organization_id'=>null,
        ]);
        }
         if ($request->service_provider_id!=null && $request->organization_id!=null) {
            $users->where([
                'service_provider_id'=>$request->service_provider_id,
                'default_organization_id'=>$request->organization_id,
        ]);
        }
        $users =$users->with(['user_roles'=>function($user_roles){
                        return $user_roles->with(['permissions'=>function($permissions){
                            return $permissions->select('id','name');

                        }])->select('id','name');
                    }])->latest()->select('id','name', 'email', 'last_login', 'created_at')->paginate(50);
        if ($users) {
            return $this->success($users, 'Users list!', 200);
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }


    function get_audit_logs(){
       
    $data = TeleScopeModel::select('telescope_entries.sequence','telescope_entries.uuid','telescope_entries.batch_id','telescope_entries.content','telescope_entries.created_at')->where('type','request')->get();
    $data_array = [];
    $filtered_array = [];
    
    foreach ($data as $key => $value) {
        $data_array['sequence'] = $value->sequence;
        $data_array['uuid'] = $value->uuid;
        $data_array['batch_id'] = $value->batch_id;
        $content= json_decode($value->content);
        // $data_array['content'] = $content;
        $data_array['ip_address'] = $content->ip_address;
        $data_array['url'] = $content->uri;
        $data_array['response_status'] = $content->response_status;
        $data_array['duration'] = $content->duration;
        $data_array['hostname'] = $content->hostname;
        if (array_key_exists('user', $content)) {
           $data_array['user'] = $content->user;
           $data_array['user_sp'] = $this->get_sp_name($content->user->id);
           $data_array['user_org'] = $this->get_org_name($content->user->id);


        }
        

        $data_array['created_at'] = $value->created_at;
         array_push($filtered_array, $data_array);
    }

    return($filtered_array);
    }
    function get_sp_name($user_id){
        $get_user_details = User::find($user_id)->first();
        if ($get_user_details->service_provider_id!=null) {
           return ServiceProvider::where('service_provider_id',$get_user_details->service_provider_id)->select('company')->first();
        }
        elseif ($get_user_details->default_organization_id!=null) {
             return Organization::where('organization_id',$get_user_details->default_organization_id)->join('service_providers','organizations.service_provider_id','=','service_providers.service_provider_id')->select('service_providers.company')->first();
        }
        else{
            return '-';
        }
    }

     function get_org_name($user_id){
        $get_user_details = User::find($user_id)->first();
       if ($get_user_details->default_organization_id!=null) {
             return Organization::where('organization_id',$get_user_details->default_organization_id)->select('company')->first();
        }
        else{
            return '-';
        }
    }

    }
