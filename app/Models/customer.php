<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class customer extends Model
{
    use HasFactory;
    protected $table = 'customers';

    protected $fillable = [
        'id',
        'customer_name',
        'customer_phone',
        'customer_area',
        'note',
        'acc_client_id'];

    //protected $guarded = ['id'];

    public function Bill():HasMany
    {
        return $this->hasMany(Bill::class, 'customer_id','id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'acc_client_id'); // اسم المفتاح الخارجي
    }

    public function bondRelations():HasMany
    {
        return $this->hasMany(BondRelation::class);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {

            if (empty($model->customer_name)) {
                throw new \Exception('الرجاء ادخال اسم العميل');
            }

            // if (empty($model->customer_area)) {
            //     throw new \Exception('الرجاء ادخال العنوان الخاص بالعميل ');
            // }
        });

    }
}
