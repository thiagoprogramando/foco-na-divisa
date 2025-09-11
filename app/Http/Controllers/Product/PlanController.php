<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PlanController extends Controller {
    
    public function index() {

        $products = Product::where('type', 'plan')->where('status', 1)->orderBy('value', 'ASC')->get();
        return view('app.Product.Plan.index', [
            'products' => $products
        ]);
    }
}
