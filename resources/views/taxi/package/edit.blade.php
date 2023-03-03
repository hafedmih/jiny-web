@extends('layouts.app')

@section('content')
<style>
    .form-group.required .require:after{
                content:" *";
                color: red;
                weight:100px;
            }
            /* .require:after{
                content:" *";
                color: red;
                weight:100px;
            } */

</style>
<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">



<div class="content">
<div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('package-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a href="{{ route('packagelist') }}" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-arrow-left7 mr-2"></i> back</a>
                </div>
            </div>
        </div>
    </div>
<!-- Form inputs -->
 <div class="card">
                @if (Session::has('success'))
                    <div class="alert alert-danger">
                        <ul>
                            <li>{{ Session::get('success') }}</li>
                        </ul>
                    </div>
                @endif
					
                    <!-- @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif -->

	                <div class="card-body">
                    <form action="{{ route('packageupdate',$packagelist->slug) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class="row">
                            <div class="form-group row col-md-6 form-group row">
                                <label class="col-form-label col-lg-3">{{ __('package_name') }}</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control"   id="name" name="name"  placeholder="{{ __('package_name')}}" value="{{$packagelist->name}}">
                                    <input type="hidden" class="form-control" id="id" name="id" value="{{$packagelist->slug}}">
                                </div>
                            </div>


                            <div class="form-group row col-md-6 form-group row">
                                <label class="col-form-label col-lg-3">{{ __('km') }}</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control"   id="km" name="km"  placeholder="{{ __('km')}}" value="{{$packagelist->km}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group row col-md-6 form-group row">
                                <label class="col-form-label col-lg-3">{{ __('hours') }}</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control"   id="hours" name="hours"  placeholder="{{ __('hours')}}"  value="{{$packagelist->hours}}">
                                </div>
                            </div>

                            <div class="form-group row col-md-6 form-group row">
                                <label class="col-form-label col-lg-3">{{ __('admin_commission_type') }}</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="admin_commission_type" id="admin_commission_type" required>
                                        <option value="1" {{(old('admin_commission_type',$packagelist->admin_commission_type) == 1 )?'selected':''}}>Percentage</option>
                                        <option value="0" {{(old('admin_commission_type',$packagelist->admin_commission_type) == 0 )?'selected':''}}>Fixed </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                         
                        <div class="row">
                            <div class="form-group row col-md-6 form-group row">
                                <label class="col-form-label col-lg-3">{{ __('admin_commission') }}</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control"   id="admin_commission" name="admin_commission"  placeholder="{{ __('admin_commission')}}" value="{{$packagelist->admin_commission}}">
                                    <div class="input-group-append">
                                    <input type="hidden" class="form-control" id="time_cast_type" name="time_cast_type"  value="{{$packagelist->time_cast_type}}">
                                    <!-- <button type="button" class="btn btn-light dropdown-toggle legitRipple time_cast" data-toggle="dropdown">{{$packagelist->time_cast_type == 'hr' ? '1 Hour' : '1 Min'}}</button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <label class="dropdown-item" data-text="1 Hour" data-value="hr">1 Hour</label>
                                        <label class="dropdown-item" data-text="1 Min" data-value="mi">1 Min</label>
                                    </div> -->
                                </div>
                                </div>
                            </div>

                            <div class="form-group row col-md-6 form-group row">
                                <label class="col-form-label col-lg-3">{{ __('base_fare') }}</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control"   id="driver_price" name="driver_price"  placeholder="{{ __('base_fare')}}" value="{{$packagelist->driver_price}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        
                            <div class="form-group row col-md-6">
                                <label class="col-form-label col-lg-3 font-weight-bold">{{ __('base_package') }}</label>
                                <div class="form-check mb-0">
                                    <label class="form-check-label">
                                        <input type="radio" name="package" @if(($packagelist->is_base_package == "YES")) checked @endif class="form-check-input-styled " data-fouc value="YES" @if(($packagelist->is_base_package == "YES")) checked @endif>
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check mb-0">
                                    <label class="form-check-label">
                                        <input type="radio" name="package" @if(($packagelist->is_base_package == "NO")) checked @endif class="form-check-input-styled" data-fouc value="NO" @if(($packagelist->is_base_package == "NO")) checked @endif>
                                        No
                                    </label>
                                </div>
                            </div>

                        </div>
                        

                        <!-- <div class="form-group row">
                            <label class="col-form-label col-lg-2">{{ __('time_cast') }}</label>
                            <div class="input-group col-lg-10">
                                <input type="text" class="form-control" placeholder="{{ __('time_cast') }}" name="time_cast" id="time_cast" value="{{$packagelist->time_cast}}">
                                <div class="input-group-append">
                                    <input type="hidden" class="form-control" id="time_cast_type" name="time_cast_type"  value="{{$packagelist->time_cast_type}}">
                                    <button type="button" class="btn btn-light dropdown-toggle legitRipple time_cast" data-toggle="dropdown">{{$packagelist->time_cast_type == 'hr' ? '1 Hour' : '1 Min'}}</button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <label class="dropdown-item" data-text="1 Hour" data-value="hr">1 Hour</label>
                                        <label class="dropdown-item" data-text="1 Min" data-value="mi">1 Min</label>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <!-- <div class="form-group row">
                            <label class="col-form-label col-lg-2">{{ __('distance_cast') }}</label>
                            <div class="input-group col-lg-10">
                                <input type="text" class="form-control" placeholder="{{ __('distance_cast') }}" name="distance_cast" id="distance_cast" value="{{$packagelist->distance_cast}}">
                                <span class="input-group-append">
                                    <span class="input-group-text">/ 1 Km</span>
                                </span>
                            </div>
                        </div> -->

<hr>
<br>
                         <div class="card" id="tableDiv">
        
        <table class="table button-print-columns1">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('type') }}</th>
                    <th>{{ __('price') }}</th>
                </tr>
            </thead>
            <tbody>
                    @foreach($vehicle as $key => $list)  

                     <tr>

                       <td>{{ ++$key}}</td>
                      
                            <td>
                            {{$list->vehicle_name}}<input type="hidden" class="form-control" name="type_id[]" value="{{$list->id}}">
                            </td>
                        <td><input type="text" class="form-control" name="price[]" value="{{$list->amount}}"></td>
                    </tr>
                     @endforeach
            </tbody>
        </table>    
    </div>

                       
                <div class="text-right">
                <button type="submit" class="btn btn-primary legitRipple">Submit <i class="icon-paperplane ml-2"></i></button>
            </div>
        </form>
<!-- /form inputs -->

</div>

<script>
    $(document).on('click','.dropdown-item',function(){
        var text = $(this).attr('data-text');
        var value = $(this).attr('data-value');

        $(".time_cast").text(text);
        $("#time_cast_type").val(value);
    })
</script>

@endsection
