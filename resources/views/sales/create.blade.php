@extends('layouts.app')

@section('content')

    <h1>New Sale Creation</h1>
    <br>
    {!! Form::open(['route' => 'sales.store']) !!}
        {{ Form::token() }}

        <div class="form-group row" >
            {{ Form::label('productName', 'Product name ',['class' =>'col-sm-2 col-form-label']) }}
            <div class="col-sm-3">
                {{ Form::text('productName', null, ['class' =>'form-control']) }}
            </div>
        </div>

        <br>
        <div class="form-group row" >
            {{ Form::label('price', 'Price ',['class' =>'col-sm-2 col-form-label']) }}
            <div class="col-sm-3">
                {{ Form::text('price', null, ['class' =>'form-control']) }}
                @if ($errMessage ?? '')
                    <p class="text-primary">{{ $errMessage }}</p>
                @endif
            </div>
        </div>

        <br>
        <div class="form-group row">
            {{ Form::label('currency', 'Currency ',['class' =>'col-sm-2 col-form-label']) }}
            <div class="col-sm-3">
                {{ Form::select('currency', ['ILS' => 'ILS', 'USD' => 'USD', 'EUR' => 'EUR'], 'ILS', ['class'=>'custom-select custom-select-sm'])}}
            </div>
        </div>

        <br>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ Form::submit('Insert payment details',['class'=>'btn btn-primary']) }}

    {!! Form::close() !!}

    @if ($saleUrl ?? '')
        <br>
        <div class="form-group row">
            <div class="col-sm-3">
                <button type="button" class="btn btn-secondary" onclick="window.location='{{ route("sales.index") }}'">Show Sales data</button>
            </div>

            <iframe src="{{$saleUrl}}" title="description" style="width: 500px; height: 430px" ></iframe>
        </div>

    @endif

@stop
