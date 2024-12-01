<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'category'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
