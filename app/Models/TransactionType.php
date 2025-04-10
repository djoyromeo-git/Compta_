<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class TransactionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_credit',
    ];

    protected $casts = [
        'is_credit' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
