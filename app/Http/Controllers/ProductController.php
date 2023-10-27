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
        $product->update([
            'nombre' => $request->nombre,
            'cat_id' => $request->cat_id,
            'imagen' => $rutaArchivoImg,
            'stock' => $request->stock,
            'precio_adquirido' => $request->precio_adquirido,
            'precio_de_venta' => $request->precio_de_venta,
            'caducidad' => $caducidad,
        ]);
        return response()->json(['producto' => $product], 201); //Solicitud ok y devuelve el cambio hecho
        // return response()->json(['message' => 'Product updated successfully', 'producto' => $product], 200); //Solicitud ok y solo devuelve mensaje
    }

    public function updateById(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

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
        // Eliminar el archivo anterior si existe
        if ($product->rutaimg && Storage::exists($product->rutaimg)) {
            Storage::delete($product->rutaimg);
        }

        $caducidad = $request->has('caducidad') ? $request->caducidad : null;

        $rutaArchivoImg = $request->file('imagen')->store('public/imgproductos');

        $product->save([
            'nombre' => $request->nombre,
            'cat_id' => $request->cat_id,
            'imagen' => $rutaArchivoImg,
            'stock' => $request->stock,
            'precio_adquirido' => $request->precio_adquirido,
            'precio_de_venta' => $request->precio_de_venta,
            'caducidad' => $caducidad,

        ]);

        return response()->json(['product' => $product], 200);
    }
}
