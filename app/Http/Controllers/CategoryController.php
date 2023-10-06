<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function addCategory(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nombre' => 'required | string | max:50',
            'color' => 'required | string | max:30'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $category = Category::create([
            'nombre' => $request->nombre,
            'color' => $request->color
        ]);

        return response()->json(['category' => $category], 201);
    }

    public function getCategories(){
        return response()->json(category::all(),200);
    }

    public function getCategoryById($id){
        $category = category::find($id);
        if (! $category) {
            return response()->json(['error' => 'Category not found in DB'], 404);
        }
        return response()->json([$category], 200);
    }

    public function deleteCategoryById($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found in DB'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully...'], 200);
    }
}
