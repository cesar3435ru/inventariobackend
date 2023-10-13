<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
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
    Route::get('products', 'getProducts');
});
