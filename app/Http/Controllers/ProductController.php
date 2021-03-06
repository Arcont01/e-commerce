<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginate()
    {
        return response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                'products' => Product::where('status', true)->paginate(9)
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response([
            'status' => 'success',
            'message' => 'ok',
            'data' => [
                'products' => Product::all()
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:191', 'unique:products,name'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric'],
            'status' => ['required', 'boolean'],
            'image' => ['required', 'image', 'mimes:jpg,png', 'max:5120']
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->first(),
                'data' => $validation->errors(),
            ], 400);
        }

        try {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'status' => $request->status,
            ]);

            $product->addMedia($request->file('image'))->toMediaCollection('images');

            return response([
                'status' => 'success',
                'message' => "The product {$product->name} has been created",
                'data' => []
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response([
                'status' => 'error',
                'message' => 'Server error',
                'data' => []
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        try {
            $product = Product::where('slug', $slug)->orWhere('id', $slug)->get();

            if ($product->isEmpty()) {
                return response([
                    'status' => 'error',
                    'message' => "The product doesn't exist"
                ], 404);
            }

            return response([
                'status' => 'success',
                'message' => "ok",
                'data' => [
                    'product' => new ProductResource($product->first())
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response([
                'status' => 'error',
                'message' => "Server error"
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        try {
            $product = Product::where('slug', $slug)->orWhere('id', $slug)->get();

            if ($product->isEmpty()) {
                return response([
                    'status' => 'error',
                    'message' => "The product doesn't exist"
                ], 404);
            }

            $product = $product->first();

            $validation = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:191', Rule::unique('products')->ignore($product->id)],
                'description' => ['required', 'string'],
                'price' => ['required', 'numeric'],
                'status' => ['required', 'boolean'],
                'image' => ['nullable', 'image', 'mimes:jpg,png', 'max:5120']
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validation->errors()->first(),
                    'data' => $validation->errors(),
                ], 400);
            }


            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'status' => $request->status
            ]);

            if(!empty($request->image)){
                $product->clearMediaCollection('images');
                $product->addMedia($request->file('image'))->toMediaCollection('images');
            }

            return response([
                'status' => 'success',
                'message' => "The product {$product->name} has been updated",
                'data' => [
                    'product' => new ProductResource($product)
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response([
                'status' => 'error',
                'message' => "Server error"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        try {
            $product = Product::where('slug', $slug)->orWhere('id', $slug)->get();

            if ($product->isEmpty()) {
                return response([
                    'status' => 'error',
                    'message' => "The product doesn't exist"
                ], 404);
            }

            $name = $product->name;
            $product->delete();

            return response([
                'status' => 'success',
                'message' => "The product $name has been deleted",
                'data' => []
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return response([
                'status' => 'error',
                'message' => "Server error"
            ], 500);
        }
    }
}
