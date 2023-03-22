@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('country-master') }}</h5>
            
        </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('country') }}</th>
                    <th>{{ __('country-code') }}</th>
                    <th>{{ __('currency-code') }}</th>
                    <th>{{ __('currency-symbol') }}</th>
                    <th>{{ __('flag') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($country as $key => $countrys)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $countrys->name !!}</td>
                        <td>{!! $countrys->code !!}</td> 
                        <td>{!! $countrys->currency_code !!}</td>
                        <td>{!! $countrys->currency_symbol !!}</td>
                        <td>{!! $countrys->flag !!}</td>
                        <td>@if($countrys->status == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td> 
                        <td>                     
                            @if(auth()->user()->can('status-change-country'))
                                <a  href="" class="btn bg-brown-400 btn-icon rounded-round legitRipple" onclick="Javascript: return activeAction(`{{ route('activeCountry',$countrys->id) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="change status" ><i class="icon-user-check"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>


@endsection
