<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
class ServiceProvider extends Model
{
    use HasFactory;
     use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    protected $fillable = [
        'name',
        'contact',
        'last_sync',
        'company',
        'status'
    ];

     public function organizations()
    {
        return $this->hasMany(Organization::class,'service_provider_id','service_provider_id');
    }

      public function details()
    {
        return $this->hasOne(ServiceProviderDetails::class,'service_provider_id','service_provider_id');
    }
    function roles(){
       return $this->hasMany(Role::class,'service_provider_id','service_provider_id'); 
    }

     function sic_users(){
       return $this->hasMany(User::class,'service_provider_id','service_provider_id'); 
    }

    public function users()
    {
        return $this->hasManyThrough(VoipUser::class, Organization::class,'service_provider_id','organization_id','service_provider_id','organization_id');
    }

     public function exts()
    {
        return $this->hasManyDeep(Extension::class, [Organization::class, VoipUser::class] ,[
               'service_provider_id',
               'organization_id',    
               'voip_user_id'     
            ],[
              'service_provider_id', 
              'organization_id', 
              'voip_user_id' 
            ]);
    }

}
