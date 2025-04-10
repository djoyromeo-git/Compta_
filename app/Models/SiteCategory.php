<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Site;

class SiteCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'value'
    ];

    public function sites()
    {
        return $this->hasMany(Site::class);
    }
}
