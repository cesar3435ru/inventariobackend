<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function addProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'cat_id' => 'required',
            'stock' => 'required | numeric',
            'precio_adquirido' => 'required',
            'precio_de_venta' => 'required',
            'caducidad' => 'required',
            // 'precio' => 'required|numeric|regex:/^\d{1,4}(\.\d{1,2})?$/|max:9999.99',
            'imagen' => 'required|image|max:2048',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rutaArchivoImg = $request->file('imagen')->store('public/imgproductos');
        $producto = Product::create([
            'nombre' => $request->nombre,
            'cat_id' => $request->cat_id,
            'imagen' => $rutaArchivoImg,
            'stock' => $request->stock,
            'precio_adquirido' => $request->precio_adquirido,
            'precio_de_venta' => $request->precio_de_venta,
            'caducidad' => $request->caducidad,

        ]);

        return response()->json(['producto' => $producto], 201);
    }

    // public function getProducts()
    // {
    //     return response()->json(product::all(), 200);
    // }

    public function getProducts() {
        $products = Product::all();
    
        foreach ($products as $product) {
            $product->imagen = asset(Storage::url($product->imagen));
        }
    
        return response()->json($products, 200);
    }
    
}
