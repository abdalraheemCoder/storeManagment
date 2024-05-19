<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bond extends Model
{
    use HasFactory;
    protected $table = 'bonds';

    protected $fillable = [
    'value',
    'note',
    'account_id',
    'bond_type'];

    protected $guarded = ['id'];

    public function account():BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
