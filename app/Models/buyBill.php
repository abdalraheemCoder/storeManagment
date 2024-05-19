<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\supplier;
use App\Models\Bill_details;

class buyBill extends Model
{
    use HasFactory;
    protected $table = 'buy_bills';

    protected $fillable = [
    'buyBill_details',
    'price',
    'quantity',
    'date',
    'discount',
    'type',
    'note',
    'supplier_id',
    'material_id'];



    public function supplier():BelongsTo
    {
        return $this->belongsTo(supplier::class);
    }

    public function bill_detail():BelongsTo
    {
        return $this->belongsTo(Bill_details::class);
    }
}
