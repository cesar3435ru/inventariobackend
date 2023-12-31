<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = ['cantidad', 'total', 'ganacias','hora','prod_id'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'prod_id');
    }

}
