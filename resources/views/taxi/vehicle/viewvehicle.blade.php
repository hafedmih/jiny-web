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
                
            <form action="" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('Vehicle Number') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="vehicle_name" readonly value="{{$vehicle->vehicle_name}}" class="form-control"  placeholder="Vehicle Name " >
                                <input type="hidden" name="id" value="{{$vehicle->id}}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('Vehicle Image') }}</label>
                            <div class="col-sm-9">
                                <input type="file" placeholder="vehicle image" readonly value="{{$vehicle->image}}"   class="form-control" name="image">
                                
                                @if($vehicle->image)
                                      <img src="{{ asset('storage/image/'.$vehicle->image)}}" height="80px" width="80px" >
                                      @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-3">{{ __('capacity') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="capacity" readonly value="{{$vehicle->capacity}}" class="form-control"  placeholder="capacity	" >
                            </div>
                        </div>
                        
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /horizontal form modal -->
@endsection
