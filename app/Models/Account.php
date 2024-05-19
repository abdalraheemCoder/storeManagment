<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Account extends Model
{
    use HasFactory;
    protected $table = 'accounts';

    protected $fillable = ['id','account_name','account_type','account_UP','account_DOWN'];

    public function bond():HasOne
    {
        return $this->hasOne(bond::class,'account_id');
    }
    public function customer():HasOne
    {
        return $this->hasOne(customer::class,'acc_client_id');
    }
    public function supplier():HasOne
    {
        return $this->hasOne(supplier::class,'acc_supplier_id');
    }
}
