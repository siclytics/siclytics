<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultBillingRates extends Model
{
    use HasFactory;
    protected $table = 'default_billing_rates_services';

        protected $fillable = ['item_name','description','discount_item','discount_value','rate'];
}
