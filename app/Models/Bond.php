<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bond extends Model
{
    use HasFactory;
    protected $table = 'bonds';
    const typeOfbond_rec = "receipt" ;
    const typeOfbond_pay = "payment";
    protected $fillable = [
    'value',
    'note',
    'account_id',
    'type',
    'bondRel_id'];

    protected $guarded = ['id'];

    public function account():BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
    public function bondRelations():BelongsTo
    {
        return $this->BelongsTo(BondRelation::class);
    }
}
