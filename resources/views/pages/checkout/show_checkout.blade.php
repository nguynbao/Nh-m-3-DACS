@extends('welcome')
@section('content')

<section id="cart_items">
    <div class="container">
        <div class="breadcrumbs">
            <ol class="breadcrumb">
                <li><a href="{{ url('/') }}">Trang chủ</a></li>
                <li class="active">Thanh toán giỏ hàng</li>
            </ol>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="shopper-informations">
            <div class="row">
                <div class="col-sm-12 clearfix">
                    <div class="bill-to">
                        <p>Thông tin đặt hàng</p>
                        <div class="form-one">
                            <form method="POST" action="{{ url('/process-payment') }}">
                                @csrf
                                <input type="text" name="shipping_name" placeholder="Họ và tên" value="{{ $user->name ?? old('shipping_name') }}" required>
                                <input type="email" name="shipping_email" placeholder="Email" value="{{ $user->email ?? old('shipping_email') }}" required>
                                <input type="text" name="shipping_phone" placeholder="Số điện thoại" value="{{ $user->phone ?? old('shipping_phone') }}" required>
                                <input type="text" name="shipping_address" placeholder="Địa chỉ" value="{{ $user->address ?? old('shipping_address') }}" required>
                                <textarea name="shipping_notes" placeholder="Ghi chú đơn hàng của bạn" rows="5">{{ old('shipping_notes') }}</textarea>

                                <div class="payment-options">
                                    <span>
                                        <label><input type="radio" name="payment_method" value="cash" checked> Tiền mặt</label>
                                    </span>
                                    {{-- Thêm các phương thức thanh toán khác ở đây --}}
                                </div>

                                <button type="submit" class="btn btn-primary">Xác nhận đơn hàng</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="review-payment">
            <h2>Xem lại giỏ hàng</h2>
        </div>

        <div class="table-responsive cart_info">
            <table class="table table-condensed">
                <thead>
                    <tr class="cart_menu">
                        <td class="image">Hình ảnh</td>
                        <td class="description">Sản phẩm</td>
                        <td class="price">Giá</td>
                        <td class="quantity">Số lượng</td>
                        <td class="total">Tổng tiền</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                        $cart = Session::get('cart', []);
                    @endphp

                    @foreach($cart as $key => $item)
                    @php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td class="cart_product">
                            <a href=""><img src="{{ asset('uploads/products/'.$item['image']) }}" alt="{{ $item['image'] }}" width="50"></a>
                        </td>
                        <td class="cart_description">
                            <h4><a href="">{{ $item['name'] }}</a></h4>
                        </td>
                        <td class="cart_price">
                            <p>{{ number_format($item['price']) }}đ</p>
                        </td>
                        <td class="cart_quantity">
                            <div class="cart_quantity_button">
                                <p>{{ $item['quantity'] }}</p>
                            </div>
                        </td>
                        <td class="cart_total">
                            <p class="cart_total_price">{{ number_format($subtotal) }}đ</p>
                        </td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="4">&nbsp;</td>
                        <td colspan="1">
                            <table class="table table-condensed total-result">
                                <tr>
                                    <td>Tổng cộng</td>
                                    <td>{{ number_format($total) }}đ</td>
                                </tr>
                                <tr>
                                    <td>Phí vận chuyển</td>
                                    <td>Miễn phí</td>
                                </tr>
                                <tr class="shipping-cost">
                                    <td>Phí dịch vụ</td>
                                    <td>Miễn phí</td>
                                </tr>
                                <tr>
                                    <td>Thành tiền</td>
                                    <td><span>{{ number_format($total) }}đ</span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

@endsection
