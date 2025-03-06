<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class CheckOutController extends Controller
{
    public function login_checkout()
    {
        $cate_product = DB::table('category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();

        return view('pages.checkout.login_checkout')->with('category', $cate_product)->with('brand', $brand_product);
    }

    public function login(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        // Lấy thông tin đăng nhập từ request
        $credentials = $request->only('email', 'password');

        // Kiểm tra xác thực
        if (Auth::attempt($credentials)) {
            // Nếu đăng nhập thành công, chuyển hướng đến checkout
            return redirect('/checkout')->with('success', 'Đăng nhập thành công! Vui lòng tiếp tục thanh toán.');
        }

        // Nếu thất bại, quay lại trang login với thông báo lỗi
        return redirect()->back()->with('error', 'Email hoặc mật khẩu không chính xác!')->withInput();
    }



    public function add_user(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'required|digits:10'
        ]);

        try {
            // Lưu vào database
            $userid = DB::table('users')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Thông báo đăng ký thành công và chuyển hướng đến checkout
            return redirect('/checkout')->with('success', 'Đăng ký thành công! Vui lòng tiếp tục thanh toán.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Email đã được đăng ký!')->withInput();
        }
    }
    public function checkout()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        }

        // Lấy danh mục & thương hiệu sản phẩm
        $cate_product = DB::table('category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();


        // Lấy giỏ hàng của người dùng từ session (nếu bạn đang dùng session)
        $cart = session()->get('cart', []);

        // Nếu giỏ hàng lưu trong database, dùng query để lấy:
        // $cart = DB::table('cart')->where('user_id', Auth::id())->get();

        return view('pages.checkout.show_checkout', [
            'category' => $cate_product,
            'brand' => $brand_product,
            'cart' => $cart
        ]);
    }


}
