<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill_details extends Model
{
    use HasFactory;
    protected $table = 'bills_details';
    const type_BUY = "buy" ;
    const type_SALE = "sale";
    const typeOfbill_RE_SALE = "re_sale" ;
    const typeOfbill_RE_BUY = "re_buy";
    protected $fillable = [
        'price',
        'totalPrice',
        'discount%',
        'quantity',
        'discount',
        'note',
        'unit_id',
        'material_id',
        'bill_id',
        "expier_date",
        'type',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    public function materials() :BelongsTo
    {
        return $this->belongsTo(material::class);
    }
    public function units() :BelongsTo
    {
        return $this->belongsTo(unit::class);
    }
}
