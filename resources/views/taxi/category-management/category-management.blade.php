@extends('layouts.app')

@section('content')

<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('category-management') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('new-category'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="tableDiv">
        
        <table class="table datatable-button-print-columns1" id="roletable">
            <thead>
                <tr>
                    <th>{{ __('sl') }}</th>
                    <th>{{ __('category_name') }}</th>
                    <th>{{ __('category_image') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category as $key => $categories)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{!! $categories->category_name!!}</td>
                        <td><img src="{{ $categories->category_image}}" height="40px" width="auto" alt="" /></td>
                        <td>@if($categories->status == 1)
                                <span class="badge badge-success">{{ __('active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('inactive') }}</span>
                            @endif
                        </td>  
                        <td>    
                            <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                            <div class="dropdown-menu dropdown-menu-right " x-placement="top-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-164px, -178px, 0px);">
                                @if(auth()->user()->can('edit-category'))
                                <a href="#" onclick="Javascript: return editAction(`{{ route('categoryEdit',$categories->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom"  class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                @endif
                                @if(auth()->user()->can('delete-category'))
                                <a href="#" onclick="Javascript: return deleteAction('$categories->slug', `{{ route('categoryDelete',$categories->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                @endif
                                @if(auth()->user()->can('active-category'))
                                <a href="#" onclick="Javascript: return activeAction(`{{ route('categoryChangeStatus',$categories->slug) }}`)" class="dropdown-item"><i class="icon-checkmark-circle2"></i>Status</a>
                                @endif
                            </div>          
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
                            <label class="col-form-label col-sm-3">{{ __('category_name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="category_name" id="category_name" class="form-control" >
                                <input type="hidden" name="category_id" id="category_id">
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('category_image') }}</label>
                            <div class="col-sm-9">
                                <input type="file" id="category_image" class="form-control" name="category_image">
                                <img src="" id="view_image" width="100px" >
                            </div>
                        </div>

                        
                        
                        

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                        <button type="submit" id="saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<!-- /horizontal form modal -->
<script>
    var message = "{{session()->get('message')}}";
    var status = "{{session()->get('status')}}";

    if(message && status == true){
        swal({
            title: "{{ __('success') }}",
            text: message,
            icon: "success",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

    if(message && status == false){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }
</script>
<script type="text/javascript">
    $("#view_image").hide();
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               console.log(data);
                $('#modelHeading').html("{{ __('edit-category') }}");
                $('#errorbox').hide();
                $('#saveBtn').val("edit_category");
                $('#saveBtn').show();
                $('#roleModel').modal('show');
                $('#category_id').val(data.category.slug);
                $('#category_name').val(data.category.category_name);
                $('#view_image').attr('src',data.category.category_image);
                $('#view_image').show();
            },
            error: function (data) {
                console.log('Error:', data);
            }
         });
        return false;
    }

  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $('#category_image').change(function(){
        $("#view_image").show();
          let reader = new FileReader();
          reader.onload = (e) => { 
            $("#view_image").attr('src',e.target.result);
          }
          reader.readAsDataURL(this.files[0]); 
    });


    $('#add_new_btn').click(function () {
        $('#category_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-category') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_category");
        $('#errorbox').hide();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('category_image',$('#category_image').prop('files')[0]);
        formData.append('category_name',$('#category_name').val());
        formData.append('category_id',$('#category_id').val());
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_category'){
            $.ajax({
                data: formData,
                url: "{{ route('categoryUpdate') }}",
                type: "POST",
                dataType: 'json',
                contentType : false,
                processData: false,
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-updated') }}",
                            text: "{{ __('data-updated-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                                // $("#reloadDiv").load("{{ route('category') }}");
                                location.reload();
                            });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $('#errorbox').show();
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.errors);
                    $('#errorContent').html('');
                    $.each(err.errors, function(key, value) {
                        $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    });
                    $('#saveBtn').html("{{ __('save-changes') }}");
                }
            });
        }else{
            $.ajax({
                data: formData,
                url: "{{ route('categorySave') }}",
                type: "POST",
                dataType: 'json',
                contentType : false,
                processData: false,
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-added') }}",
                            text: "{{ __('data-added-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                            
                                // $("#reloadDiv").load("{{ route('category') }}");
                                location.reload();
                            });
                        
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#errorbox').show();
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err);
                        $('#errorContent').html('');
                        $.each(err.errors, function(key, value) {
                            $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                        });
                        $('#saveBtn').html("{{ __('save-changes') }}");
                    }
                });
        }
    });



  });
</script>

@endsection
