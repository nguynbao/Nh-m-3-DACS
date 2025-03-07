@extends('welcome')
@section('content')
    <div class="container">
        <h2>Kết quả tìm kiếm cho: "{{ $query }}"</h2>

        @if ($products->isEmpty())
            <p>Không tìm thấy sản phẩm nào.</p>
        @else
            <ul>
                @foreach ($products as $product)
                    <li>
                        <a href="{{ route('product.show', $product->product_id) }}">
                            {{ $product->product_name }} - {{ number_format($product->product_price) }} VND
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection