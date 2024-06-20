<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class category extends Model
{
    use HasFactory;
    protected $table = 'categories';

    protected $fillable = [
    'category_name',
    'note'];


    public function materials(): HasMany
    {
        return $this->hasMany(material::class, 'category_id', 'id');
    }
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {

            if (empty($model->category_name)) {
                throw new \Exception('الرجاء ادخال اسم الصنف');
            }
        });
    }

}
