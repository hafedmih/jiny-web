@extends('layouts.app')

@section('content')

<div class="content">
     
<div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-Vehicle') }}</h5>
           
        </div>
    </div>

    <!-- Horizontal form modal -->
 
        <div class="modal-dialog">
            <div class="modal-content">
                
            <form action="{{route('vehicleSave')}}" method="post" enctype="multipart/form-data">
                              @csrf

                    <div class="modal-body">
                        
                        <div class="form-group row ">
                            <label class="col-form-label col-sm-3">{{ __('Vehicle Number') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="vehicle_name"  class="form-control"  placeholder="Vehicle Name " >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('Vehicle Image') }}</label>
                            <div class="col-sm-9">
                                <input type="file" placeholder="vehicle image"  class="form-control" name="image">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('capacity') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="capacity" class="form-control"  placeholder="capacity	" >
                            </div>
                        </div>
                        
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" class="btn btn-primary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /horizontal form modal -->
@endsection
