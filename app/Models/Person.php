<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Site;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'middlename',
        'gender',
        'photo'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }
}
