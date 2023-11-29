<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{


    public function newSaleUno(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prod_id' => 'required',
            'cantidad' => 'required|numeric',
            'total' => 'required|numeric',
            'ganacias' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $venta = Sale::create([
            'cantidad' => $request->cantidad,
            'total' => $request->total,
            'ganacias' => $request->ganacias,
            'prod_id' => $request->prod_id,

        ]);

        return response()->json(['producto' => $venta], 201);
    }

    public function getVentas()
    {
        $products = Sale::with('product')->get();
        $modifiedProducts = [];

        foreach ($products as $product) {
            if (!isset($product->product->imagen_modified)) {
                $product->product->imagen = asset(Storage::url($product->product->imagen));
                $product->product->imagen_modified = true; // Marcar la URL como modificada
            }
            $modifiedProducts[] = $product;
        }

        return response()->json($modifiedProducts, 200);
    }


    public function newSale(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prod_id' => 'required',
            'cantidad' => 'required|numeric',
            'total' => 'required|numeric',
            'ganacias' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Obtener el producto
        $producto = Product::find($request->prod_id);

        if (!$producto) {
            return response()->json(['error' => 'El producto no existe'], 404);
        }

        // Calcular el nuevo stock restando la cantidad vendida
        $nuevoStock = $producto->stock - $request->cantidad;

        if ($nuevoStock < 0) {
            return response()->json(['error' => 'No hay suficiente stock para esta venta'], 422);
        }

        // Actualizar el stock del producto
        $producto->stock = $nuevoStock;
        $producto->save();

        // Crear la venta
        $venta = Sale::create([
            'cantidad' => $request->cantidad,
            'total' => $request->total,
            'ganacias' => $request->ganacias,
            'prod_id' => $request->prod_id,
        ]);

        return response()->json(['producto' => $venta], 201);
    }
}
