<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;


class AdminController extends Controller
{
    public function check()
    {
        $admin_id = Session::get('admin_id');
        if ($admin_id) {
            return Redirect::to('dashboard');
        } else {
            return Redirect::to('admin')->send();
        }
    }
    public function index()
    {
        return view('admin_login');
    }

    public function show_dashboard()
    {
        $this->check();
        if (!Session::has('admin_id')) {
            return Redirect::to('/admin')->with('error', 'Bạn chưa đăng nhập');
        }
        return view('admin.dashboard');
    }

    public function login(Request $request)
    {
        $admin_email = $request->admin_email;
        $admin_password = md5($request->admin_password);

        $admin = DB::table('tbl_admin')
            ->where('admin_email', $admin_email)
            ->where('admin_password', $admin_password)
            ->first();

        if ($admin == true) {
            Session::put('admin_name', $admin->admin_name);
            Session::put('admin_id', $admin->admin_id);
            return Redirect::to('/dashboard'); // Chuyển hướng đến dashboard
        } else {
            Session::put('message', 'Email hoặc mật khẩu không đúng!');
            return Redirect::to('/admin'); // Quay lại trang đăng nhập
        }

    }
    public function logout()
    {
        $this->check();
        Session::put('admin_name', null);
        Session::put('admin_id', null);
        return view('admin');
    }
}
