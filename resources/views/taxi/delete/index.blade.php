<!DOCTYPE html>
<html>
<head>
    <title>Delete Account Lahagni</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-4">
  @if(session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
  @endif
  @if(session('fail'))
    <div class="alert alert-warning">
        {{ session('fail') }}
    </div>
  @endif
  <div class="card">
    <div class="card-header text-center font-weight-bold">
      User Delete using Phone Number
    </div>
    <div class="card-body">
      <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{url('delete-accounts')}}">
       @csrf
        <div class="form-group">
          <label for="exampleInputEmail1">Phone Number</label>
          <input type="number" id="phone_number" name="phone_number" class="form-control" required="">
        </div>
        <div class="form-group">
          <label for="exampleInputEmail1">Otp</label>
          <input type="text" id="otp" name="otp" class="form-control" required="" placeholder="">
        </div>
        <button type="submit" class="btn btn-primary">Delete Account</button>
      </form>
    </div>
  </div>
</div>  
</body>
</html>
