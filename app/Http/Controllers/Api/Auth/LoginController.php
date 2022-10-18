<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\ForgetRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\VerifyOTPRequest;

use App\Http\Traits\APIResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use APIResponse;

    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();
        if (!empty($user)) {
            if (Hash::check($request->password, $user->password)) {
                auth()->login($user);
                $user = auth()->user();
                $user->last_login = now();
                $user->save();
                $data['token'] = $user->createToken('API')->plainTextToken;
                $data['user'] = $user;
                return $this->success($data, 'Loggedin successfully!');
            }
        } else {

            return $this->error('Invalid credentials!', 'No results', 403);
        }
    }


    public function forget(ForgetRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $otp = random_int(100000, 999999);

            $user->update([
                'otp' => $otp,
                'otp_expiry' => now()->addSeconds(20),
            ]);

            \Mail::to($user->email)->send(new \App\Mail\Auth\ForgetPasswordMail($user));

            return $this->success($user, 'OTP is sent successfully!');
        }

        return $this->error('User not found!', 'No results', 403);
    }


    public function verify(VerifyOTPRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->otp != $request->otp && now() > $user->otp_expiry)
                return $this->error('OTP is invalid!', 'No results', 403);

            return $this->success($user, 'OTP is verified successfully!');
        }

        return $this->error('User not found!', 'No results', 403);
    }


    public function reset(ResetPasswordRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->update(['password' => bcrypt($request->password)]);
            return $this->success($user, 'Password is updated successfully!');
        }

        return $this->error('User not found!', 'No results', 403);
    }

    function add_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'username' => 'required|unique:users,username',

        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $user = new User;
        $user->name = $request->full_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->username = $request->username;
        if ($request->default_organization_id) {
            $user->default_organization_id = $request->default_organization_id;
        }
        if ($request->service_provider_id) {
            $user->service_provider_id = $request->service_provider_id;
        }


        if ($user->save()) {
            if ($request->role_id) {
                $user->syncRoles($request->role_id);
            }

            return $this->success($user, 'User created successfully!', 200);
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }

    function update_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'full_name' => 'max:255',
            'email' => 'email',
            'password' => 'min:8|confirmed',

        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $user = User::find($request->user_id);
        $user->name = $request->full_name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        if ($request->organization_id) {
            $user->default_organization_id = $request->organization_id;
        }

        if ($request->self_extension) {
            $user->self_extension = $request->self_extension;
        }

        if ($request->self_extension) {
            $user->self_extension = $request->self_extension;
        }

        if ($user->save()) {

            return $this->success($user, 'User updated successfully!', 200);
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }
    function destroy(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'organization_id' => 'required|exists:organizations,organization_id',
            ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $get_user = User::where('id',$request->user_id);
        if ($request->service_provider_id) {
            $get_user->where('service_provider_id',$request->service_provider_id);
        }

        if ($request->organization_id) {
            $get_user->where('default_organization_id',$request->organization_id);
            $get_user->orWhere('other_organizations_ids','LIKE','%'.$request->organization_id.'%');
        }
        $get_user=$get_user->first();
        if ($get_user!=null) {
            if ($get_user->default_organization_id==$request->organization_id) {
                $get_user->default_organization_id=Null;
            }
            else{
            // $new_ids = preg_replace($request->organization_id, '', $get_user->other_organizations_ids);
             $get_user->other_organizations_ids=7025;

            }
             // $get_user->save();
            dd('Still pending! developer is working with it. So donot bother him.');
        }
        else{
             return $this->error('User not found!', 'No results', 404);
        }
    }
}
