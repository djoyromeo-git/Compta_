<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'description',
        'site_id',
        'user_id',
        'transaction_type_id',
        'currency_id'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function type()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
