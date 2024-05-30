<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class supplier extends Model
{
    use HasFactory;
    protected $table = 'suppliers';

    protected $fillable = [
        'supplier_name',
        'supplier_phone',
        'supplier_company',
        'note',
        'acc_supplier_id'];


    public function bills():HasOne{
        return $this->hasOne(Bill::class,'supplier_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'acc_supplier_id');
    }

    public function bondRelations():HasMany
    {
        return $this->hasMany(BondRelation::class);
    }
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {

            if (empty($model->supplier_name)) {
                throw new \Exception('الرجاء ادخال اسم المورد');
            }
        });
    }

}
