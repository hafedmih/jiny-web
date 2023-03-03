@extends('layouts.app')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js" charset="utf-8"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.179.0.min.js"></script>





<div class="content">


   

<div class="content">
     
     <div class="card">
         <div class="card-header header-elements-inline">
             <h5 class="card-title">{{ __('invoice_questions') }}</h5>
             
         </div>
     </div>
 
     <div class="card" id="tableDiv">
         
         <table class="table datatable-button-print-columns1" id="roletable">
             <thead>
                 <tr>
                     <th>{{ __('sl') }}</th>
                     <th>{{ __('driver') }}</th>
                     <th>{{ __('request_id') }}</th>
                     <th>{{ __('answer') }}</th>    
                 </tr>
             </thead>
             <tbody>
 
                 @foreach($drivers as $key => $value)
                     <tr>
                         <td>{{ ++$key }}</td> 
                         <td> 
                             <a href="{{ route('driverDetails',$value->requestquestions ?  ($value->requestquestions->driverDetail ? $value->requestquestions->driverDetail->slug : '') : '')}}" class="text-default font-weight-semibold letter-icon-title">{!! $value->requestquestions ? ($value->requestquestions->driverDetail ? $value->requestquestions->driverDetail->firstname :'') :'' !!} {!! $value->requestquestions ? ($value->requestquestions->driverDetail ? $value->requestquestions->driverDetail->lastname :'') :''!!}
                                <br>
                                {!! $value->requestquestions ? ($value->requestquestions->driverDetail ? $value->requestquestions->driverDetail->phone_number:'') :'' !!}
                            </a>
                        </td>
                         <td><div><a style="color:#000" href="{{ route('requestView',$value->requestquestions ? $value->requestquestions->id :'') }}">{!! $value->requestquestions ? $value->requestquestions->request_number : '' !!}</a><br></td>
                         <!-- <td>{!! $value->answer!!}</td>   -->
                         <td>
                            @if($value['answer'] != "NO")
                                <span  class="icon-thumbs-up3 text-success ml-auto"></span>
                            @else
                                <span  class="icon-thumbs-down3 text-danger ml-auto"></span>
                            @endif
                        </td>                        
                     </tr>
                 @endforeach
             </tbody>
         </table>
     </div>
 
     
     <!-- Horizontal form modal -->
     <div id="roleModel" class="modal fade" tabindex="-1">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header bg-primary">
                     <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                 </div>
                 <form id="roleForm" name="roleForm" class="form-horizontal" enctype="multipart/form-data">
                               @csrf
 
                     <div class="modal-body">
                         <div class="alert alert-danger alert-dismissible" id="errorbox">
                             <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                             <span id="errorContent"></span>
                         </div>
 
                         <div class="form-group row required">
                             <label class="col-form-label col-sm-3">{{ __('questions') }}</label>
                             <div class="col-sm-9">
                                 <input type="text" name="questions" id="questions" class="form-control"  placeholder="{{ __('questions') }}" />
                                 <input type="hidden" name="questions_id" id="questions_id" /> 
                             </div>
                         </div>
                     </div>
                         
                     <div class="modal-footer">
                         <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                         <button type="submit" id="saveBtn" class="btn btn-primary" >Submit</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 
     
 </div>
</div>
<!-- /horizontal form modal -->

<script type="text/javascript">
    var i =1;
    var message = "{{session()->get('message')}}";

    if(message){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

</script>

@endsection
