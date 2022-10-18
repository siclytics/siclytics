<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoipUser extends Model
{
    use HasFactory;

     public function organizations()
    {
        return $this->hasMany(Organization::class,'organization_id','organization_id');
    }

    public function exts()
    {
        return $this->hasMany(Extension::class,'voip_user_id','voip_user_id');
    }
     public function dids()
    {
        return $this->hasMany(Did::class,'voip_user_id','voip_user_id');
    }
     public function groups()
    {
        return $this->hasMany(UserGroups::class,'voip_user_id','voip_user_id');
    }
}
