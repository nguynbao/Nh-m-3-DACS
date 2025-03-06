@extends('welcome')
@section('content')
    <section id="cart_items">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="breadcrumbs">
                <ol class="breadcrumb">
                    <li><a href="{{URL::to('/trang-chu') }}">Home</a></li>
                    <li class="active">checkout</li>
                </ol>
            </div>
            <div class="shopper-informations">
                <div class="row">
                    <div class="col-sm-5 clearfix">
                        <div class="bill-to">
                            <p>Bill To</p>
                            <div class="form-one">
                                <form>
                                    <input type="text" placeholder="Company Name">
                                    <input type="text" placeholder="Email*">
                                    <input type="text" placeholder="Title">
                                    <input type="text" placeholder="First Name *">
                                    <input type="text" placeholder="Middle Name">
                                    <input type="text" placeholder="Last Name *">
                                    <input type="text" placeholder="Address 1 *">
                                    <input type="text" placeholder="Address 2">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="review-payment">
                <h2>Review & Payment</h2>
            </div>

            <div class="table-responsive cart_info">
                <table class="table table-condensed">
                    <thead>
                        <tr class="cart_menu">
                            <td class="image">Item</td>
                            <td class="description"></td>
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
                            <tr>
                                <td colspan="2" style="text-align: left; padding-left: 0;">
                                    <table class="table table-condensed total-result">
                                        <tr class="shipping-cost">
                                            <td>Shipping Cost</td>
                                            <td>Free</td>
                                        </tr>
                                        <tr>
                                            <td>Total</td>
                                            <td><span>{{ number_format(collect($cart)->sum(fn($item) => $item["price"] * $item["quantity"]), 0, ',', '.') }}
                                                    VNĐ</span></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="payment-options">
                <span>
                    <label><input type="radio" name="payment_method" value="banking"> Banking</label>
                </span>
                <span>
                    <label><input type="radio" name="payment_method" value="cash"> Tiền mặt</label>
                </span>
            </div>
        </div>
    </section> <!--/#cart_items-->


@endsection