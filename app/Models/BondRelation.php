<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BondRelation extends Model
{
    use HasFactory;

    protected $table = 'bond_relations';

    protected $fillable = [
    'value',
    'note',
    'bond_id',
    'salse_bill_id',
    'buy_bill_id',
    'customer_id'];
}
