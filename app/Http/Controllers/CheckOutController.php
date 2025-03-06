<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;


class CheckOutController extends Controller
{
    public function login_checkout()
    {
        if (Auth::check()) {
            return back()->with('error', 'Bạn đã đăng nhập rồi! Vui lòng tiếp tục thanh toán.');
        }

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
            Session::put('email', $request->email);
            Session::put('user_id', Auth::id());
            if (url()->previous() == url('/show-cart')) {
                return redirect('/checkout')->with('success', 'Đăng nhập thành công! Vui lòng tiếp tục thanh toán.');
            }
            // Nếu đăng nhập thành công, chuyển hướng đến checkout
            return redirect('/trang-chu')->with('success', 'Đăng nhập thành công!');
        }

        // Nếu trước đó là trang show-cart, sau khi đăng nhập thành công, chuyển hướng đến checkout

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
            return redirect('/login-checkout')->with('message', 'Please login to proceed to checkout');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect('/show-cart')->with('error', 'Your cart is empty');
        }

        $user = null;
        if (Auth::check()) {
            $user = Auth::user();
        }

        $cate_product = DB::table('category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();

        return view('pages.checkout.show_checkout')
            ->with('category', $cate_product)
            ->with('brand', $brand_product)
            ->with('cart', $cart)
            ->with('user', $user);
    }

    public function save_checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|in:credit_card,cash_on_delivery,bank_transfer',
        ]);

        // Get cart data
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect('/show-cart')->with('error', 'Your cart is empty');
        }

        try {
            DB::beginTransaction();

            // Calculate total amount
            $total_amount = 0;
            foreach ($cart as $item) {
                $total_amount += $item['product_price'] * $item['product_qty'];
            }

            // Create order
            $order = new Order();
            $order->user_id = Auth::id();
            $order->order_date = Carbon::now();
            $order->order_status = Order::STATUS_PENDING;
            $order->total_amount = $total_amount;
            $order->shipping_address = $request->shipping_address;
            $order->payment_method = $request->payment_method;
            $order->payment_status = $request->payment_method === 'cash_on_delivery' ?
                Order::PAYMENT_STATUS_PENDING : Order::PAYMENT_STATUS_PENDING;
            $order->save();

            // Create order details
            foreach ($cart as $product_id => $item) {
                $orderDetail = new OrderDetail();
                $orderDetail->order_id = $order->order_id;
                $orderDetail->product_id = $product_id;
                $orderDetail->quantity = $item['product_qty'];
                $orderDetail->unit_price = $item['product_price'];
                $orderDetail->subtotal = $item['product_price'] * $item['product_qty'];
                $orderDetail->save();
            }

            DB::commit();

            // Clear cart after successful order
            Session::forget('cart');

            return redirect('/order-complete/' . $order->order_id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'There was an error processing your order: ' . $e->getMessage());
        }
    }

    public function process_payment(Request $request)
    {
        // Validate request data
        $request->validate([
            'payment_method' => 'required',
            'shipping_name' => 'required',
            'shipping_email' => 'required|email',
            'shipping_phone' => 'required',
            'shipping_address' => 'required',
            'shipping_notes' => 'nullable',
        ]);

        // Check if cart has items
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect('/show-cart')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        try {
            DB::beginTransaction();
            // Create order
            $order = new Order();
            $order->user_id = Auth::check() ? Auth::id() : null;
            $order->order_date = Carbon::now();
            $order->shipping_name = $request->shipping_name;
            $order->shipping_email = $request->shipping_email;
            $order->shipping_phone = $request->shipping_phone;
            $order->shipping_address = $request->shipping_address;
            $order->shipping_notes = $request->shipping_notes;
            $order->payment_method = $request->payment_method;
            $order->order_status = 'Đang chờ xử lý';

            // Calculate total
            $total = 0;
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            $order->total_amount = $total;
            $order->created_at = Carbon::now();
            $order->save();

            // Save order details
            foreach ($cart as $key => $item) {
                $orderDetail = new OrderDetail();
                $orderDetail->order_id = $order->order_id;
                $orderDetail->product_id = $item['id'];
                $orderDetail->unit_price = $item['price'];
                $orderDetail->quantity = $item['quantity'];
                $orderDetail->subtotal = $item['price'] * $item['quantity'];
                $orderDetail->save();
            }

            // Clear cart after successful order
            Session::forget('cart');

            DB::commit();

            // Redirect based on payment method
            if ($request->payment_method == 'cash') {
                return redirect('/order-complete')->with('success', 'Đặt hàng thành công! Cảm ơn bạn đã mua hàng.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function order_complete()
    {
        $cate_product = DB::table('category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('brand_product')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();

        return view('pages.checkout.order_complete')
            ->with('category', $cate_product)
            ->with('brand', $brand_product);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/trang-chu')->with('success', 'Đăng xuất thành công!');
    }
}
