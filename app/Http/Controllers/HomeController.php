<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $cate_product = DB::table('category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();
        $all_product = DB::table('tbl_product')->where('product_status', '0')->orderBy('product_id', 'desc')->limit('4')->get();

        return view('pages.home')->with('category', $cate_product)->with('brand', $brand_product)->with('all_product', $all_product);
    }
    public function search(Request $request)
    {
        $query = $request->key_word;
        $cate_product = DB::table('category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();
        $search_product = DB::table('tbl_product')->where('product_name', 'like', '%' . $query . '%')->get();

        return view('pages.search.search')->with('category', $cate_product)->with('brand', $brand_product)->with('search_product', $search_product);

    }
    public function show_contact()
    {
        $cate_product = DB::table('category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();
        return view('pages.contact.contact')->with('category', $cate_product)->with('brand', $brand_product);
    }
}
