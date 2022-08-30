@extends('layouts.app')

@section('content')
    <h1>Created Sales</h1>
    @if ($salesData != '' && count($salesData) > 0)
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Time</th>
            <th>Sale Number</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Currency</th>
            <th>Payment Link</th>
        </tr>
        </thead>
        @foreach($salesData as $row)
            <tr>
                <td nowrap>{{ $row->created_at}}</td>
                <td>{{ $row->payme_sale_code}}</td>
                <td>{{ $row->product_name}}</td>
                <td>{{ $row->sale_price}}</td>
                <td>{{ $row->currency}}</td>
                <td nowrap>{{ $row->sale_url}}</td>
            </tr>
        @endforeach
    </table>
    @else
        <br>
        <h5>Sales table is empty</h5>
    @endif

@stop
