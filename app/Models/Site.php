<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Person;
use App\Models\SiteCategory;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'person_id',
        'site_category_id',
        'address'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function category()
    {
        return $this->belongsTo(SiteCategory::class, 'site_category_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
