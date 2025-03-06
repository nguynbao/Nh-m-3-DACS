<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        // Lấy danh mục sản phẩm và thương hiệu để hiển thị trên trang giỏ hàng
        $cate_product = DB::table('category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();

        // Lấy product_id và số lượng từ request
        $productid = $request->productid_hidden;
        $quantity = $request->qty ?? 1; // Mặc định là 1 nếu không có giá trị

        // Lấy thông tin sản phẩm từ database
        $product = DB::table('tbl_product')->where('product_id', $productid)->first();


        // Kiểm tra nếu sản phẩm không tồn tại
        if (!$product) {
            return Redirect::back()->with('error', 'Sản phẩm không tồn tại!');
        }
        // Lấy giỏ hàng hiện tại từ session
        $cart = Session()->get('cart', []);
        // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
        if (isset($cart[$productid])) {
            $cart[$productid]['quantity'] += $quantity;
        } else {
            // Thêm sản phẩm mới vào giỏ hàng
            $cart[$productid] = [
                "id" => $product->product_id,
                "name" => $product->product_name,
                "price" => $product->product_price,
                "image" => $product->product_image,
                "quantity" => $quantity
            ];
        }

        // Lưu giỏ hàng vào Session
        Session()->put('cart', $cart);
        Session()->save();

        return Redirect('/show-cart');
    }
    public function show_cart()
    {
        $cate_product = DB::table('category_product')
            ->where('category_status', '0')
            ->orderBy('category_id', 'desc')
            ->get();

        $brand_product = DB::table('brand_product')
            ->where('brand_status', '0')
            ->orderBy('brand_id', 'desc')
            ->get();

        // Lấy giỏ hàng từ Session
        $cart = Session()->get('cart', []);



        return view('pages.cart.cart_product')
            ->with('category', $cate_product)
            ->with('brand', $brand_product)
            ->with('cart', $cart);
    }
    public function dalete_cart($product_id)
    {
        $cart = Session()->get('cart', []);

        // Kiểm tra nếu sản phẩm có tồn tại trong giỏ hàng
        if (isset($cart[$product_id])) {
            unset($cart[$product_id]); // Xóa sản phẩm khỏi giỏ hàng
            Session()->put('cart', $cart); // Cập nhật lại Session
        }

        return redirect()->back()->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng');
    }
    public function updateCart($id, $action)
    {
        $cart = Session()->get('cart');

        if (isset($cart[$id])) {
            if ($action == 'increase') {
                $cart[$id]['quantity'] += 1;
            } elseif ($action == 'decrease') {
                if ($cart[$id]['quantity'] > 1) {
                    $cart[$id]['quantity'] -= 1;
                } else {
                    unset($cart[$id]); // Nếu số lượng = 0 thì xóa luôn sản phẩm
                }
            }
            Session()->put('cart', $cart);
        }

        return redirect()->back();
    }


}
