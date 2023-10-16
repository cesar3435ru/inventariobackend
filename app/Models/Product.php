<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'cat_id', 'imagen', 'estado', 'precio_adquirido', 'precio_de_venta', 'stock', 'caducidad'];

    protected $appends = ['img_filename'];

    public function getImgFilenameAttribute()
    {
        return basename($this->imagen);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
