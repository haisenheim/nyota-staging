@extends('layouts.app')

@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="{{ URL::to('') }}"><img src="{{ URL::to('public/images/noyta-logo.png') }}" class=""></a>
    </div>
</div>
    <div class="">
        <h3 style="text-align: center;">{{trans('emails.activateSuccessmsg')}}</h3>
    </div>

@endsection
