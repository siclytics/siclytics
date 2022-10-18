<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Organization extends Model
{
    use HasFactory;
    protected $fillable =[
        'service_provider_id',
        'name',
        'company',
    ];

     public function users()
    {
        return $this->hasMany(VoipUser::class,'organization_id','organization_id');
    }

     public function sic_users()
    {
        return $this->hasMany(User::class,'default_organization_id','organization_id');
    }


      public function details()
    {
        return $this->hasOne(OrganizationDetail::class,'organization_id','organization_id');
    }

      public function service_provider()
    {
        return $this->hasOne(ServiceProvider::class,'service_provider_id','service_provider_id');
    }

     public function exts()
    {
        return $this->hasManyThrough(Extension::class, VoipUser::class,'organization_id','voip_user_id','organization_id','voip_user_id');
    }
     public function dids()
    {
        return $this->hasManyThrough(Did::class, VoipUser::class,'organization_id','voip_user_id','organization_id','voip_user_id');
    }

     function roles(){
       return $this->hasMany(Role::class,'organization_id','organization_id'); 
    }
}
