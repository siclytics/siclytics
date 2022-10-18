<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtensionsAssigned extends Model
{
    use HasFactory;

    function extension_details(){
        return $this->hasMany(Extension::class,'extended_number','extension');
    }
}
