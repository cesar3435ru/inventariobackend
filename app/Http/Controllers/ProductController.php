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
            'imagen' => 'required|image|max:2048',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $caducidad = $request->has('caducidad') ? $request->caducidad : null;

        $rutaArchivoImg = $request->file('imagen')->store('public/imgproductos');
        $producto = Product::create([
            'nombre' => $request->nombre,
            'cat_id' => $request->cat_id,
            'imagen' => $rutaArchivoImg,
            'stock' => $request->stock,
            'precio_adquirido' => $request->precio_adquirido,
            'precio_de_venta' => $request->precio_de_venta,
            'caducidad' => $caducidad,
        ]);

        return response()->json(['producto' => $producto], 201);
    }

    // public function getProducts()
    // {
    //     return response()->json(product::all(), 200);
    // }

    public function getProducts()
    {
        $products = Product::all();

        foreach ($products as $product) {
            $product->imagen = asset(Storage::url($product->imagen));
        }

        return response()->json($products, 200);
    }

    public function getActiveProducts()
    {
        $products = Product::where('estado', 1)->get();

        foreach ($products as $product) {
            $product->imagen = asset(Storage::url($product->imagen));
        }

        return response()->json($products, 200);
    }

    public function getProductById($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found in DB'], 404);
        }

        return response()->json(['product' => $product], 200);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found in DB'], 404);
        }

        Storage::delete($product->imagen);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully...'], 200);
    }

    public function archiveProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found in DB'], 404);
        }

        $product->update(['estado' => false]);

        return response()->json(['message' => 'Product archived successfully...'], 200);
    }

    public function activeProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found in DB'], 404);
        }

        $product->update(['estado' => true]);

        return response()->json(['message' => 'Product archived successfully...'], 200);
    }



    public function editProductById($id, Request $request)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found in DB'], 404);
        }
        $rules = [
            'nombre' => 'required|string|max:100',
            'cat_id' => 'required',
            'stock' => 'required|numeric',
            'precio_adquirido' => 'required',
            'precio_de_venta' => 'required',
        ];

        // Verificar si se proporcionó una imagen en la solicitud
        if ($request->hasFile('imagen')) {
            $rules['imagen'] = 'image|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $caducidad = $request->has('caducidad') ? $request->caducidad : null;

        // Verificar si se proporcionó una imagen en la solicitud antes de almacenarla
        if ($request->hasFile('imagen')) {
            $rutaArchivoImg = $request->file('imagen')->store('public/imgproductos');
            $product->imagen = $rutaArchivoImg;
        }

        $product->update([
            'nombre' => $request->nombre,
            'cat_id' => $request->cat_id,
            'stock' => $request->stock,
            'precio_adquirido' => $request->precio_adquirido,
            'precio_de_venta' => $request->precio_de_venta,
            'caducidad' => $caducidad
        ]);

        return response()->json(['producto' => $product], 201);
    }



    public function updateProd(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:50',
            'precio_adquirido' => 'numeric',
            'precio_de_venta' => 'numeric',
            'stock' => 'numeric|nullable',
            'caducidad' => 'date|nullable',
            'cat_id' => 'numeric|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if ($request->hasFile('imagen')) {
            // Si se envía una nueva imagen, se elimina la imagen anterior y se almacena la nueva.
            if ($product->imagen) {
                Storage::delete($product->imagen);
            }
            $rutaArchivoImg = $request->file('imagen')->store('public/imgproductos');
            $product->imagen = $rutaArchivoImg;
        }
        // Ahora, se actualizan los otros campos independientemente de si se envía una imagen o no.
        if ($request->has('nombre')) {
            $product->nombre = $request->input('nombre');
        }
        if ($request->has('cat_id')) {
            $product->cat_id = $request->input('cat_id');
        }
        if ($request->has('precio_adquirido')) {
            $product->precio_adquirido = $request->input('precio_adquirido');
        }
        if ($request->has('precio_de_venta')) {
            $product->precio_de_venta = $request->input('precio_de_venta');
        }
        if ($request->has('stock')) {
            $product->stock = $request->input('stock');
        }
        if ($request->has('caducidad')) {
            $product->caducidad = $request->input('caducidad');
        }
        $product->save();
        return response()->json(['message' => 'Producto actualizado con éxito'], 200);
    }
}
