<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill_details extends Model
{
    use HasFactory;
    protected $table = 'bills_details';

    protected $fillable = [
        'price',
        'discount',
        'note',
        'unit_id',
        'material_id',
        'salse_bill_id',
        'buy_bill_id'];

        protected $guarded = ['Bill_type'];

        public function materials() :BelongsTo
        {
            return $this->belongsTo(material::class);
        }

        public function units() :BelongsTo
        {
            return $this->belongsTo(unit::class);
        }
}
