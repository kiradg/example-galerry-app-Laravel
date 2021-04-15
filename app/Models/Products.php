<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    public $timestamps = true;
    
    protected $fillable = [
        'name',
        'description'
    ];

    public function gallery()
    {
        return $this->hasMany(Gallery::class);
    }
}