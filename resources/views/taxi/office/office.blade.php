@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('OTP details') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <!-- @if(auth()->user()->can('otp'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                    @endif -->
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('phone_number') }}</th>                    
                    <th>{{ __('otp') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($otp as $key => $otps)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $otps->phone_number!!}</td>
                        <td>{!! $otps->otp!!}</td>  
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


@endsection