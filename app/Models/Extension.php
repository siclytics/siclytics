<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    use HasFactory;
  
     public function users()
    {
        return $this->hasMany(VoipUser::class,'voip_user_id','voip_user_id');
    }

     public function organization()
    {
        return $this->hasManyThrough(Organization::class, VoipUser::class,'voip_user_id','organization_id','voip_user_id','organization_id');
    }

    public function reg_state()
    {
        return $this->hasMany(ExtRegState::class, 'extension','extended_number');
    }

    public function active_call_state()
    {
       return $this->hasMany(ExtRegState::class, 'extension','extended_number');
    }
}
