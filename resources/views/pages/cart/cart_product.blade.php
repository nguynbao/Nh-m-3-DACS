@extends('welcome')
@section('content')
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs">
                <ol class="breadcrumb">
                    <li><a href="{{ URL::to('/trang-chu') }}">Home</a></li>
                    <li class="active">Shopping Cart</li>
                </ol>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (!empty($cart) && count($cart) > 0)
                <div class="table-responsive cart_info">
                    <table class="table table-condensed">
                        <thead>
                            <tr class="cart_menu">
                                <td class="image">Item</td>
                                <td class="price">Price</td>
                                <td class="quantity">Quantity</td>
                                <td class="total">Total</td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $key => $value)
                                <tr>
                                    <td class="cart_product">
                                        <a href="{{ asset('uploads/products/' . $value['image']) }}" target="_blank">
                                            <img src="{{ asset('uploads/products/' . $value['image']) }}" width="100">
                                        </a>
                                    </td>
                                    <td class="cart_price">
                                        <p>{{ number_format($value['price'], 0, ',', '.') }} VNĐ</p>
                                    </td>
                                    <td class="cart_quantity">
                                        <div class="cart_quantity_button">
                                            <a class="cart_quantity_up"
                                                href="{{ URL::to('/update-cart/' . $value['id'] . '/increase') }}"> + </a>
                                            <input class="cart_quantity_input" type="text" name="quantity"
                                                value="{{ $value['quantity'] }}" readonly size="2">
                                            <a class="cart_quantity_down"
                                                href="{{ URL::to('/update-cart/' . $value['id'] . '/decrease') }}"> - </a>
                                        </div>
                                    </td>
                                    <td class="cart_total">
                                        <p class="cart_total_price">
                                            {{ number_format($value['price'] * $value['quantity'], 0, ',', '.') }} VNĐ
                                        </p>
                                    </td>
                                    <td class="cart_delete">
                                        <a class="cart_quantity_delete" href="{{ URL::to('/xoa-cart/' . $value['id']) }}"><i
                                                class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center">Giỏ hàng của bạn đang trống.</p>
            @endif
        </div>
    </section> <!--/#cart_items-->

    <section id="do_action">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="total_area">
                        <ul>
                            <li>Tổng tiền <span>{{ number_format($total, 0, ',', '.') }} VNĐ</span></li>

                            @if(Session::has('coupon'))
                                <li>Mã giảm giá: {{ Session::get('coupon')['name'] }}
                                    <a href="{{ url('/remove-coupon') }}" class="btn btn-danger btn-sm">Xóa</a>
                                </li>
                                <li>Số tiền giảm <span>{{ number_format(Session::get('coupon')['discount'], 0, ',', '.') }} VNĐ</span></li>
                                <li>Thành tiền <span>{{ number_format($final_total, 0, ',', '.') }} VNĐ</span></li>
                            @endif

                            <li>Phí vận chuyển <span>Free</span></li>
                        </ul>
                        <div style="display: flex; justify-content: space-around;">
                            @if(!empty($cart) && count($cart) > 0)
                                <form action="{{ URL::to('/check-coupon') }}" method="post" style="display: flex;">
                                    @csrf
                                    <input style="width: 150px;" type="text" name="coupon" class="form-control"
                                        placeholder="Nhập mã giảm giá">
                                    <input type="submit" class="btn btn-default check_coupon" name="check_coupon"
                                        value="Áp dụng">
                                </form>
                                <a class="btn btn-default check_out" href="{{ URL::to('/checkout') }}">Thanh toán</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--/#do_action-->
@endsection
