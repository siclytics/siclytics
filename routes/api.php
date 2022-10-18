<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\VoipController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceProvidersController;
use App\Http\Controllers\Api\OrgnanizationController;
use App\Http\Controllers\Api\Voipnow;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\PermissionsController;
use App\Http\Controllers\Api\VoipUserController;
use App\Http\Controllers\Api\ExtensionController;
use App\Http\Controllers\Api\DidController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AutoSyncController;
use App\Http\Controllers\Api\BillingRatesController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['auth:sanctum'])->prefix('v2/')->group(function () {
   Route::any('/dashboard', [DashboardController::class,'index']);
   Route::any('/get-audit-logs', [DashboardController::class,'get_audit_logs']);

   Route::get('/sync', [AutoSyncController::class,'test']);
   Route::post('/add-user', [LoginController::class,'add_user']);
   Route::post('/user-listing', [DashboardController::class,'get_users']);
   Route::post('/update-user', [LoginController::class,'update_user']);
   Route::post('/delete-user', [LoginController::class,'destroy']);


});

Route::group(['prefix'=>'v2', 'as'=>'api.v2'],function(){
    Route::post('login',[LoginController::class,'login']);
    Route::post('forget',[LoginController::class,'forget']);
    Route::post('verify',[LoginController::class,'verify']);
    Route::post('reset',[LoginController::class,'reset']);
});

Route::middleware(['auth:sanctum'])->prefix('v2/sps/')->group(function () {
    Route::get('sync',[ServiceProvidersController::class,'getServiceProvidersData'])->name('sps.sync');
    Route::any('get',[ServiceProvidersController::class,'get_sps'])->name('sps.get');
    Route::post('update',[ServiceProvidersController::class,'update'])->name('sps.update');
    Route::get('details/{id}',[ServiceProvidersController::class,'details'])->name('sps.details');
    // Route::get('roles/{id}',[ServiceProvidersController::class,'get_roles'])->name('sps.roles');

});

Route::middleware(['auth:sanctum'])->prefix('v2/org/')->group(function () {
    Route::any('get-all',[OrgnanizationController::class,'index'])->name('org.get-all');
    Route::get('get/{providerId}',[OrgnanizationController::class,'getOrganizationsData'])->name('org.get');
    Route::get('get-details/{id}',[OrgnanizationController::class,'details'])->name('org.details');
     Route::post('add-details/{id}',[OrgnanizationController::class,'add_details'])->name('org.add-details');
     Route::post('assign-org',[OrgnanizationController::class,'assign_organizations'])->name('ext.assign-org');
     Route::post('un-assign-org',[OrgnanizationController::class,'unassign_organizations'])->name('ext.un-assign-org');
     Route::post('get-assigned-orgs',[OrgnanizationController::class,'get_assigned_organizations'])->name('ext.get-assigned-orgs');
     Route::post('set-default-organization',[OrgnanizationController::class,'set_default_organizations'])->name('ext.set-default-organization');
     // Route::get('roles/{organization_id}',[OrgnanizationController::class,'get_roles'])->name('ext.roles');



});

Route::middleware(['auth:sanctum'])->prefix('v2/users/')->group(function () {
    Route::get('get/{orgId}',[VoipUserController::class,'getUsersData'])->name('users.sync');
    Route::any('get-all',[VoipUserController::class,'index'])->name('users.get');
    Route::get('sync',[VoipUserController::class,'get_user_groups'])->name('sync');

});

Route::middleware(['auth:sanctum'])->prefix('v2/ext/')->group(function () {
    Route::get('get/{userId}',[ExtensionController::class,'getExtensionsData'])->name('ext.get');
     Route::any('get-extentions',[ExtensionController::class,'get_extensions'])->name('ext.get-extentions');
      Route::post('get-extentions-state',[ExtensionController::class,'get_extensions_state'])->name('ext.get-extentions-state');
      Route::post('assign-extension',[ExtensionController::class,'assign_extensions'])->name('ext.assign-extension');
      Route::post('unassign-extension',[ExtensionController::class,'unassign_extensions'])->name('ext.unassign-extension');
      Route::post('get-assigned-extensions',[ExtensionController::class,'get_assigned_extensions'])->name('ext.get-assigned-extension');

});
Route::middleware(['auth:sanctum'])->prefix('v2/did/')->group(function () {
    Route::get('get/{userId}',[DidController::class,'GetPublicNoPoll'])->name('did.get');
    Route::any('get-all',[DidController::class,'index'])->name('did.get-all');
    Route::post('assign-phone-number',[DidController::class,'assign_number'])->name('did.assign-phone-number');
    Route::post('unassign-phone-number',[DidController::class,'unassign_number'])->name('did.assign-phone-number');
    Route::post('get-assigned-phone-number',[DidController::class,'get_assigned_number'])->name('did.get-assigned-phone-number');

    });
    

Route::middleware(['auth:sanctum'])->prefix('v2/rates/')->group(function () {
    Route::post('add-rate',[BillingRatesController::class,'store'])->name('rates.add');
    Route::post('get-rate',[BillingRatesController::class,'get_all_rates'])->name('rates.get'); 
    });


Route::middleware(['auth:sanctum'])->prefix('v2/')->group(function () {
Route::resource('roles', RolesController::class);
Route::resource('permissions', PermissionsController::class);
Route::post('/assign-role',[RolesController::class,'assignRoleToUser']);
Route::post('/get-user-role/{id}',[RolesController::class,'getUserRole']);
Route::post('/assign-permission-role',[RolesController::class,'assignPermissionRole']);
});
// Route::group(['middleware' => ['auth:api']], function () {

// });


  Route::middleware(['auth:sanctum'])->get('testing-reports',[AutoSyncController::class,'check_users'])->name('ext.call');

  
Route::middleware('auth:sanctum')->get('get-users/{id}',[OrgnanizationController::class,'getUsersData']);
    Route::post('get_access_token', [Voipnow::class,'get_access_token']);
    Route::get('get_service_providers/{domin}/{key}/{secret}', [Voipnow::class,'get_service_providers']);
 Route::middleware('auth:sanctum')->get('get-phones/{id}',[OrgnanizationController::class,'test']);

