<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Did extends Model
{
    use HasFactory;
    protected $hidden = [
    'id',
    'created_at',
    'updated_at',
    'did_id',
    'channelID',
    'callbackExtID',
];
     public function users()
    {
        return $this->hasMany(VoipUser::class,'voip_user_id','voip_user_id');
    }

     public function organization()
    {
        return $this->hasManyThrough(Organization::class, VoipUser::class,'voip_user_id','organization_id','voip_user_id','organization_id');
    }

}
