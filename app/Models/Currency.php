<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'code',
        'symbol'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
