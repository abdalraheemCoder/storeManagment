<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class driver extends Model
{
    use HasFactory;
    protected $table = 'drivers';

    protected $fillable = [
        'driver_name',
        'driver_phone',
        'driver_address',
        'note'];

    protected $guarded = ['driver_ID'];

    public function Bills()
    {
        return $this->hasMany(driver::class, 'driver_id');
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {

            if (empty($model->driver_name)) {
                throw new \Exception('الرجاء ادخال اسم السائق');
            }
        });
    }
}
