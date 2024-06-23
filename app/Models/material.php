<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\unit;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class material extends Model
{
    use HasFactory;
    protected $table = 'materials';

    protected $fillable = [
    'material_name',
    'note',
    'category_id'];

    public function unit() :HasMany
    {
        return $this->hasMany(Unit::class,'unit_mat_ID');
    }

    public function category() :BelongsTo
    {
        return $this->belongsTo(category::class);
    }

    public function Bills_details():HasMany
    {
        return $this->hasMany(Bill_details::class, 'material_id');
    }




    public static function boot()
    {

        parent::boot();

        static::saving(function ($model) {

            if (empty($model->material_name)) {
                throw new \Exception('الرجاء ادخال اسم المادة');
            }

            if (empty($model->category_id)) {
                throw new \Exception('الرجاء تحديد الصنف');
            }

            if ($model->discount_mat < 0 || $model->discount_mat > 100) {
                throw new \Exception('الرجاء ادخال قيمة صحيحة');
            }

            if (Carbon::now()->greaterThan($model->expierd_date)) {
                throw new \Exception('الرجاء ادخال تاريخ صحيح');
            }
        });

    }

}
