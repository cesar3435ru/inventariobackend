<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Models\Category;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(CategoryController::class)->group(function () {
    Route::post('addcategory', 'addCategory');
    Route::get('categories', 'getCategories');
    Route::get('category/{id}', 'getCategoryById');
    Route::delete('category/{id}', 'deleteCategoryById');
});

Route::controller(ProductController::class)->group(function () {
    Route::post('addproduct', 'addProduct');
    Route::get('products', 'getProducts'); //Trae los productos sin importar su estado
    Route::get('activeproducts', 'getProducts'); //Trae los productos donde su estado es activo
    Route::get('product/{id}', 'getProductById');
    Route::delete('product/{id}', 'deleteProduct'); //Elimina el registro por completo de la DB
    Route::put('archproduct/{id}', 'archiveProduct'); //Archiva el producto de la DB
    Route::put('actproduct/{id}', 'activeProduct'); //Restaura el producto de la DB
    Route::put('product/{id}', 'editProductById');
    Route::put('pro/{id}', 'updateProd');
});


Route::controller(SaleController::class)->group(function () {
    Route::post('nventa', 'newSale');
    Route::get('ventas', 'getVentas');
});