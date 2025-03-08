<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Models\Banner;
class BannerController extends Controller
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
    public function add_banner()
    {
        $this->check();
        return view('admin.add_banner');

    }
    public function show_banner()
    {
        $this->check();
        $all_banner = DB::table('tbl_banner')
            ->orderBy('banner_id', 'desc')
            ->get();


        return view('admin.manage_banner', compact('all_banner'));

    }
    public function save_banner(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'banner_name' => 'required|string|max:255',
            'banner_desc' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
            'banner_status' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return Redirect::to('add-banner')->withErrors($validator)->withInput();
        }

        $data = [
            'banner_name' => $request->banner_name,
            'banner_desc' => $request->banner_desc,
            'banner_status' => (int) $request->banner_status,
        ];


        // Xử lý upload hình ảnh
        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/banners'), $image_name);
            $data['banner_image'] = $image_name;
        } else {
            $data['banner_image'] = ""; // Nếu không có ảnh
        }

        // Thêm dữ liệu vào database
        DB::table('tbl_banner')->insert($data);
        Session::put('message', "Bạn đã thêm sản phẩm thành công");

        return Redirect::to('add-banner');
    }
    public function edit_banner($banner_id)
    {
        $this->check();
        $edit_banner = DB::table('tbl_banner')->where('banner_id', $banner_id)->get();
        return view('admin.edit_banner')
            ->with('edit_banner', $edit_banner);
    }

    public function update_banner(Request $request, $banner_id)
    {
        $data = array();
        $data['banner_name'] = $request->banner_name;
        $data['banner_desc'] = $request->banner_desc;
        $data['banner_status'] = $request->banner_status;

        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/banners'), $image_name);
            $data['banner_image'] = $image_name;
        }

        DB::table('tbl_banner')->where('banner_id', $banner_id)->update($data);

        Session::put('message', 'Cập nhật sản phẩm thành công!');
        return Redirect::to('/manage-banner');
    }
    public function delete_banner($banner_id)
    {
        // Kiểm tra xem danh mục có tồn tại không
        $banner = DB::table('tbl_banner')->where('banner_id', $banner_id)->first();
        if (!$banner) {
            return redirect()->back()->with('error', 'Danh mục không tồn tại.');
        }
        // Xóa danh mục
        DB::table('tbl_banner')->where('banner_id', $banner_id)->delete();

        return redirect()->back()->with('success', 'Xóa danh mục thành công.');
    }

}
