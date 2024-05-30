<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\customer;
use App\Models\driver;
use App\Models\supplier;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';

    const typeOfbill_BUY = "buy" ;
    const typeOfbill_SALE = "sale";
    const typeOfpay_DEF = "def" ;
    const typeOfpay_CASH = "cash";

    protected $fillable = [
    'price',
    'quantity',
    'date',
    'discount',
    'note',
    'customer_id',
    'driver_id',
    'supplier_id',
    'typeOfbill',
    'typeOfpay'
    ];

    public function customer() :BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver() :BelongsTo
    {
        return $this->belongsTo(driver::class);
    }
    public function supplier() :BelongsTo
    {
        return $this->belongsTo(supplier::class);
    }

    public function bill_detail():HasMany
    {
        return $this->hasMany(Bill_details::class);
    }
    public function bondRelations():HasMany
    {
        return $this->hasMany(BondRelation::class);
    }
}
