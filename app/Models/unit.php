<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class unit extends Model
{
    use HasFactory;
    protected $table = 'units';

    protected $fillable = [
        'unit_name',
        'unit_equal',
        'unitSalse_price',
        'unitbuy_price',
        'unit_mat_id',
        'Quantity',
        'Quan_return'];

    protected $guarded = ['unit_ID'];

    public function materials(): BelongsTo
    {
        return $this->belongsTo(material::class);
    }

    public function Bills_details():HasMany
    {
        return $this->hasMany(Bill_details::class, 'unit_id');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {

            if (empty($model->unit_name)) {
                throw new \Exception('الرجاء ادخال اسم الوحدة الأساسية');
            }

            if ($model->unit_equal <= 0) {
                throw new \Exception('الرجاء ادخال عدد صحيح');
            }

            // if (!is_numeric($model->unitSalse_price) || $model->unitSalse_price < 0) {
            //     throw new \Exception('الرجاء ادخال قيمة صحيحة');
            // }

            // if (!is_numeric($model->unitbuy_price) || $model->unitbuy_price < 0) {
            //     throw new \Exception('الرجاء ادخال قيمة صحيحة');
            // }

        });
    }


}
