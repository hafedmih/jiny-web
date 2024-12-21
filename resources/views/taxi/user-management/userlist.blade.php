@extends('layouts.app')

@section('content')


<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('manage-user') }}</h5>
            <div class="header-elements">
            <div class="list-icons">
                    @if(auth()->user()->can('new-user'))
                        <button type="button" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">User list</h6>
                    <div class="header-elements">
                        <div class="list-icons">
                            <a class="list-icons-item" data-action="collapse"></a>
                            <a class="list-icons-item" data-action="reload"></a>
                            <a class="list-icons-item" data-action="remove"></a>
                        </div>
                    </div>
                </div>
                <div class="card-body row">
                        <div class="col-lg-11">
                            <form action="{{ url()->current() }}" method="get">
                                <div class="form-group row">
                                    <div class="col-lg-6">
                                        <div class=" form-group row ">
                                            <div class="col-sm-3">
                                                <span>Filter:</span>
                                            </div>
                                            <div class="col-sm-5">
                                                <input type="text" name="search" class="form-control"
                                                    value="{{ $result['request']->search }}" placeholder="Type phone number or name">
                                            </div>
                                            <div class="col-sm-4">
                                                <button type="submit" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i
                                                        class="icon-search4 mr-2"></i> Search</button>
                                            </div>
                                        </div>
                                    </div>
                                    

                                </div>
                            </form>
                        </div>
                        <div class="col-lg-1">
                            <a href="{{ url()->current() }}" class="nav-link " style="padding-top: 0px; padding-bottom: 0px;">
                                <button class="btn btn-outline bg-danger-400 text-dark-600 border-danger-300 legitRipple">
                                    <i class="icon-reset"></i></button>
                            </a>
                        </div>
                    </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                    @php $route = Request::route()->getName(); @endphp
                        @if(auth()->user()->can('active-users'))
                         <li class="nav-item "><a href="#right-icon-tab1" onclick="routeCall(`{{route('userManage') }}`)"  class="nav-link @if (in_array($route, ['userManage'])) active  @endif" data-toggle="tab"><i class="icon-user-check ml-2 text-success-800"></i><span class="text-success-400"> Active </span></a></li>
                        @endif
                        @if(auth()->user()->can('inactive-users'))
                         <li class="nav-item"><a href="#right-icon-tab2" onclick="routeCall(`{{route('user_block') }}`)"  class="nav-link @if (in_array($route, ['user_block'])) active  @endif" data-toggle="tab"><i class="icon-user-block ml-2 text-danger-800"></i> <span class="text-danger-800"> Inactive </span> </a></li>
                        @endif
                    </ul>

                    <div class="tab-content">
                    @php $route = Request::route()->getName(); @endphp
                        <div class="tab-pane fade  @if (in_array($route, ['userManage'])) show active @endif" " id="right-icon-tab1">
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('sl') }}</th>
                                        <th>Details</th>
                                        <th>Wallet</th>
                                        <th>Rating</th>
                                        <th>{{ __('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['users_active'] as $key => $user)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        @if($user->profile_pic == NUll)
                                                            <a href="#" class="btn bg-violet rounded-round btn-icon btn-sm legitRipple">
                                                                <span class="letter-icon"></span>
                                                            </a>
                                                        @else
                                                            <a href="#" >
                                                                <img src="{{ $user->profile_pic }}" class="rounded-circle" value="$user->profile_pic" width="32" height="32" alt="">
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('userView',$user->slug)}}" class="text-default font-weight-semibold letter-icon-title">{!! $user->firstname !!} {!! $user->lastname !!}
                                                            <br>
                                                            {!! $user->phone_number !!}
                                                        </a>
                                                    </div>
                                                </div>                                            
                                            </td>
                                            <td>{!! $user->getCountry ? $user->getCountry->currency_symbol : '' !!} {!! $user->wallet_balance !!} </td>
                                            <td>
                                                {{ $user->rating }} <i style="color:#fdde00" class="icon-star-full2"></i>
                                            </td>
                                            <td>   
                                                <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                                  @if(auth()->user()->can('edit-users'))
                                                  <a href="#" onclick="Javascript: return editAction(`{{ route('usermanagementEdit',$user->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit" class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                                  @endif
                                                  @if(auth()->user()->can('view-users'))
                                                        <a href="{{ route('userView',$user->slug) }}" class="dropdown-item"><i class="icon-eye"></i> View User</a>
                                                    @endif
                                                    @if(auth()->user()->can('active-users'))
                                                        @if($user->active == 1)
                                                            <a href="#" onclick="Javascript: return activeAction(`{{ route('usermanagementActive',$user->slug) }}`)" class="dropdown-item"><i class="icon-user-block"></i>Block User</a>
                                                        @else
                                                            <a href="#"  onclick="Javascript: return activeAction(`{{ route('usermanagementActive',$user->slug) }}`)" class="dropdown-item"><i class="icon-user-check"></i>Unblock User</a>
                                                        @endif
                                                    @endif
                                                    @if(auth()->user()->can('delete-users'))
                                                        <div class="dropdown-divider"></div>
                                                        <a href="#" onclick="Javascript: return deleteAction('$user->slug', `{{ route('usermanagementDelete',$user->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete User</a>
                                                    @endif  
                                                
                                                </div>       
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(!empty($result['users_active']))
                                {{ $result['users_active']->links('vendor.pagination.bootstrap-4')}}
                            @endif
                        </div>

                        <div class="tab-pane fade @if (in_array($route, ['user_block'])) show active @endif" " id="right-icon-tab2">
                            <table class="table datatable-button-print-columns1 table-bordered" id="roletable">
                                <thead>
                                    <tr>
                                        <th>{{ __('sl') }}</th>
                                        <th>Details</th>
                                        <!-- <th>{{ __('Wallet') }}</th> -->
                                        <th>Rating</th>
                                        <th>{{ __('block_reson') }}</th>
                                        <th>{{ __('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['users_inactive'] as $key => $user)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3">
                                                        @if($user->profile_pic == NUll)
                                                            <a href="#" class="btn bg-violet rounded-round btn-icon btn-sm legitRipple">
                                                                <span class="letter-icon">R</span>
                                                            </a>
                                                        @else
                                                            <a href="#" >
                                                                <img src="{{ $user->profile_pic }}" class="rounded-circle" width="32" height="32" alt="">
                                                            </a>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('userView',$user->slug)}}" class="text-default font-weight-semibold letter-icon-title">{!! $user->firstname !!} {!! $user->lastname !!}
                                                            <br>
                                                            {!! $user->phone_number !!}
                                                        </a>
                                                        <!-- @if($user->online_by)
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-success mr-1"></span> Online</div>
                                                        @else
                                                            <div class="text-muted font-size-sm"><span class="badge badge-mark border-danger mr-1"></span> Offline</div>
                                                        @endif -->
                                                    </div>
                                                </div>                                            
                                            </td>
                                            <td>
                                                {{ $user->rating }} <i style="color:#fdde00" class="icon-star-full2"></i>
                                            </td>
                                            <td>{!! $user->block_reson !!} </td>
                                            <td>   
                                                <a href="#" class="list-icons-item dropdown-toggle caret-0" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(16px, 19px, 0px);">
                                                  @if(auth()->user()->can('view-users'))
                                                  <a href="#" onclick="Javascript: return editAction(`{{ route('usermanagementEdit',$user->slug) }}`)" data-popup="tooltip" title="" data-placement="bottom" data-original-title="Edit" class="dropdown-item"><i class="icon-pencil"></i> Edit </a>
                                                        <a href="{{ route('userView',$user->slug) }}" class="dropdown-item"><i class="icon-eye"></i> View User</a>
                                                    @endif
                                                    @if(auth()->user()->can('active-users'))
                                                        @if($user->active == 1)
                                                            <a href="#" onclick="Javascript: return activeAction(`{{ route('usermanagementActive',$user->slug) }}`)" class="dropdown-item"><i class="icon-user-block"></i>Block User</a>
                                                        @else
                                                            <a href="#"  onclick="Javascript: return activeAction(`{{ route('usermanagementActive',$user->slug) }}`)" class="dropdown-item"><i class="icon-user-check"></i>Unblock User</a>
                                                        @endif
                                                    @endif
                                                    @if(auth()->user()->can('delete-users'))
                                                        <div class="dropdown-divider"></div>
                                                        <a href="#" onclick="Javascript: return deleteAction('$user->slug', `{{ route('usermanagementDelete',$user->slug) }}`)" class="dropdown-item"><i class="icon-trash"></i> Delete User</a>
                                                    @endif  
                                                
                                                </div>       
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(!empty($result['users_inactive']))
                                {{ $result['users_inactive']->links('vendor.pagination.bootstrap-4')}}
                            @endif
                        </div>

                      
                    </div>
                </div>
            </div>
        </div>
      
    </div>
</div>
<!-- /horizontal form modal -->

    <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">{{ __('add-new') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="roleForm" name="roleForm" class="form-horizontal">
                    @csrf

                    <div class="modal-body">
                        <div class="alert alert-danger" id="errorbox">
                            <button type="button" class="close"><span>×</span></button>
                            <span id="errorContent"></span>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3"><b>{{ __('first-name') }}</b></label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('first-name') }}" id="first_name" class="form-control" name="first_name">
                                <input type="hidden" name="user_id" id="user_id">
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label class="col-form-label col-sm-3">{{ __('last-name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('last-name') }}" id="last_name" class="form-control" name="last_name">
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label class="col-form-label col-sm-3">{{ __('email') }}</label>
                            <div class="col-sm-9">
                                <input type="email" placeholder="{{ __('email') }}" id="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('phone_number') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ __('phone_number') }}" id="phone_number" class="form-control" name="phone_number">
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('country') }}</label>
                            <div class="col-sm-9">
                                <select id="country_code" class="form-control" name="country_code">
                                    <option value="">Select Country</option>
                                    @foreach($result['country'] as $key => $value)
                                        <option value="{!! $value->id !!}">{!! $value->name !!}</option>
                                    @endforeach
			                    </select>
                            </div>
                        </div>


                        <div class="form-group row required">
                            <label class="col-form-label col-sm-3">{{ __('language') }}</label>
                            <div class="col-sm-9">
                                <select id="language" class="form-control" name="language">
                                    <option value="">Select Type</option>
                                    @foreach($result['languages'] as $key => $value)
                                        <option value="{!! $value->code !!}">{!! $value->name !!}</option>
                                    @endforeach
			                    </select>
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

<script type="text/javascript">
    function editAction(actionUrl){
        $.ajax({
            url: actionUrl,
            type: "GET",
            dataType: 'json',
            success: function (data) {
               //console.log(data);
                $('#modelHeading').html("{{ __('edit-user') }}");
                $('#errorbox').hide();
                // $('#saveBtn').html("Edit Complaint");
                // console.log(data.user.roles[0].name);
                $('#saveBtn').val("edit_user");
                $('#roleModel').modal('show');
                $('#user_id').val(data.user.slug);
                $('#first_name').val(data.user.firstname);
                $('#last_name').val(data.user.lastname);
                $('#email').val(data.user.email);
                $('#phone_number').val(data.user.phone_number);
                $('#language').val(data.user.language);
                $('#country_code').val(data.user.country_code);

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


    $('#add_new_btn').click(function () {
        $('#complaint_id').val('');
        $('#roleForm').trigger("reset");
        $('#modelHeading').html("{{ __('create-new-user') }}");
        $('#roleModel').modal('show');
        $('#saveBtn').val("add_user");
        // $('#saveBtn').html("Save Complaint");
        $('#errorbox').hide();
        $(".password").show();
    });

    

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#errorbox').hide();
       
        if(btnVal == 'edit_user'){
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('usermanagementUpdate') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-updated') }}",
                            text: "{{ __('data-updated-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                                // $("#reloadDiv").load("{{ route('user') }}");
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
        }else{
            $.ajax({
                data: $('#roleForm').serialize(),
                url: "{{ route('usermanagementSave') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                        $('#roleForm').trigger("reset");
                        $('#roleModel').modal('hide');
                        swal({
                            title: "{{ __('data-added') }}",
                            text: "{{ __('data-added-successfully') }}",
                            icon: "success",
                            }).then((value) => {
                            
                                // $("#reloadDiv").load("{{ route('user') }}");
                                location.reload();
                            });
                        
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        $('#errorbox').show();
                        var err = eval("(" + xhr.responseText + ")");
                        $('#errorContent').html('');
                        $.each(err.errors, function(key, value) {
                            $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                        });
                        $('#saveBtn').html("{{ __('save-changes') }}");
                    }
                });
        }
    });



    $(".close").click(function(){
        $('#errorbox').hide();
    })
    


  });

  function activeStatus(actionUrl){
    swal({
        title: "Are you sure?",
        text: "You will change status for this admin",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            window.location.href = actionUrl;
            swal("Poof! Your admin status has been changed!", {
                icon: "success",
            });

          
        } else {
          swal("Your Data status is not chenged!");
        }
      });

    return false;
}
</script>
<script>
function routeCall(url){
    document.location.href=url;
}
</script>
@endsection

