@extends('welcome')
@section('content')
    <section id="cart_items">
        <div class="container">
            <div class="breadcrumbs">
                <ol class="breadcrumb">
                    <li><a href="{{URL::to('/trang-chu') }}">Home</a></li>
                    <li class="active">Shopping Cart</li>
                </ol>
            </div>
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
                                    <p>{{ number_format($value["price"], 0, ',', '.') }} VNĐ</p>
                                </td>
                                <td class="cart_quantity">
                                    <div class="cart_quantity_button">
                                        <a class="cart_quantity_up"
                                            href="{{ URL::to('/update-cart/' . $value['id'] . '/increase') }}"> + </a>
                                        <input class="cart_quantity_input" type="text" name="quantity"
                                            value="{{ $value['quantity'] }}" autocomplete="off" size="2" readonly>
                                        <a class="cart_quantity_down"
                                            href="{{ URL::to('/update-cart/' . $value['id'] . '/decrease') }}"> - </a>
                                    </div>
                                </td>

                                <td class="cart_total">
                                    <p class="cart_total_price">
                                        {{ number_format($value["price"] * $value["quantity"], 0, ',', '.') }} VNĐ
                                    </p>

                                </td>
                                <td class="cart_delete">
                                    <a class="cart_quantity_delete" href="{{ URL::to('/xoa-cart/' . $value["id"]) }}"><i
                                            class="fa fa-times"></i></a>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section> <!--/#cart_items-->
    <section id="do_action">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="total_area">
                        <ul>
                            <li>Shipping Cost <span>Free</span></li>
                            <li>Total
                                <span>
                                    {{ number_format(collect($cart)->sum(fn($item) => $item["price"] * $item["quantity"]), 0, ',', '.') }}
                                    VNĐ
                                </span>
                            </li>
                        </ul>
                        <div style="display: flex; justify-content: center;">
                            <form action="{{ URL::to('/login-checkout') }}" method="get">
                                <button type="submit" class="btn btn-default check_out">Buy</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--/#do_action-->


@endsection