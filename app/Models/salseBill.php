<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\customer;
use App\Models\driver;

class salseBill extends Model
{
    use HasFactory;

    protected $table = 'salse_bills';

    const Type_BUY = "buy" , Type_SALE = "sale";

    protected $fillable = [
    'salseBill_details',
    'price',
    'quantity',
    'date',
    'discount',
    'type',
    'note',
    'customer_id',
    'driver_id',
    ];

    public function customer() :BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver() :BelongsTo
    {
        return $this->belongsTo(driver::class);
    }

    public function bill_detail():BelongsTo
    {
        return $this->belongsTo(Bill_details::class);
    }
}
