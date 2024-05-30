<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BondRelation extends Model
{
    use HasFactory;

    protected $table = 'bond_relations';

    protected $fillable = [
    'value',
    'note',
    'bill_id',
    'acc_id'];

    public function bond():HasMany
    {
        return $this->hasMany(Bond::class);
    }
    public function account():BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
    public function bill():BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }


}
