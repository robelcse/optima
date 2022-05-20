<div class="d-sm-flex align-items-center justify-content-between mb-4 alertMsg">
    @if(Session::has('success'))
    <h5 class="h5 mb-0 text-gray-800 alert alert-success">
        {{ Session::get('success') }}
    </h5>
    @endif
</div>

<div class="d-sm-flex align-items-center justify-content-between mb-4 alertMsg">
    @if(Session::has('error'))
    <h5 class="h5 mb-0 text-gray-800 alert alert-danger">
        {{ Session::get('error') }}
    </h5>
    @endif
</div>



<!-- @if(count($errors) > 0)
<div class="p-1">
    @foreach($errors->all() as $error)
    <p class="alert alert-danger">{{$error}}</p>
    @endforeach
</div>
@endif -->