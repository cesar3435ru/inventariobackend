<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    // public function productosMasVendidos()
    // {
    //     $productos = Sale::select('prod_id', DB::raw('SUM(cantidad) as total_vendido'))
    //         ->groupBy('prod_id')
    //         ->orderByDesc('total_vendido')
    //         ->limit(5) // Cambia este límite según tus necesidades
    //         ->get();

    //     // Obtener los detalles de los productos más vendidos
    //     $productosMasVendidos = [];
    //     foreach ($productos as $producto) {
    //         $prodDetalle = Product::find($producto->prod_id);
    //         if ($prodDetalle) {
    //             $producto->detalle = $prodDetalle;
    //             $productosMasVendidos[] = $producto;
    //         }
    //     }

    //     return response()->json(['productos_mas_vendidos' => $productosMasVendidos], 200);
    // }

    public function productosMasVendidos()
    {
        $productos = Sale::select('prod_id', DB::raw('CAST(SUM(cantidad) AS UNSIGNED) as total_vendido'))
            ->groupBy('prod_id')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        // Obtener los detalles de los productos más vendidos
        $productosMasVendidos = [];
        foreach ($productos as $producto) {
            $prodDetalle = Product::find($producto->prod_id);
            if ($prodDetalle) {
                $producto->detalle = $prodDetalle;
                $productosMasVendidos[] = $producto;
            }
        }

        return response()->json(['productos_mas_vendidos' => $productosMasVendidos], 200);
    }
}
