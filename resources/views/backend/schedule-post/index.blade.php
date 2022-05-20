@extends('layout.app')
@section('content')
<link rel="stylesheet" href="{{asset('public/assets/autocomplete/typeahed.css') }}">
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="customer-table-search product_table_search">
                                <form>
                                    <div class="d-flex flex-wrap justify-content-end align-items-center">
                                        <div class="pro_name">
                                            <input type="search" name="" placeholder="Name..">
                                        </div>
                                        <div class="autocomplete">
                                            <input type="search" id="batch" name="" placeholder="Products..">
                                        </div>
                                        <button type="submit" class="btn btn-success">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="schedule-post-btn">
                    <a href="{{ url('schedule/post/create') }}">Add Schedule Post</a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="product-table table-responsive">
                                <table class="table" id="table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Post Image</th>
                                            <th>Post Body</th>
                                            <th>Published Time</th>
                                            <th>Created at</th>
                                            <th style="text-align: center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $x=0 @endphp
                                        @foreach($scheduleposts as $key=>$value)
                                        @php $x++ @endphp
                                        <tr>
                                            <td>{{ $x }}</td>
                                            <td><img src="{{ $value->image }}" height="50px" width="50px" alt=""></td>
                                            <td>{{ \Illuminate\Support\Str::limit($value->body, 15, $end='...') }}</td>
                                            <td>{{ $value->date_time }}</td>
                                            <td>{{ $value->created_at }}</td>
                                            <td style="text-align: center;">
                                                <a href="">
                                                    <i class="fa fa-info-circle"></i>
                                                </a>
                                                <button onclick="deleteSchedulePost({{ $value->id }})" type="button" style="border: none;background:none">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <form id="delete-data-{{ $value->id }}" action="{{ route('schedule.post.delete',$value->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .schedule-post-btn {

        padding: 35px;
    }

    .schedule-post-btn a {
        background: #198754;
        color: #fff;
        padding: 15px 25px;
        border-radius: 5px;
    }
</style>
@endsection
@section('script')


{{--autocompete--}}
<script src="{{asset('public/assets/autocomplete/typeahead.bundle.min.js')}}"></script>
<script src="{{asset('public/assets/autocomplete/babylontypeahed.js')}}"></script>
<script src="{{asset('public/assets/autocomplete/custom.js')}}"></script>

<script>
    function deleteSchedulePost(id) {

        event.preventDefault();
        let form = document.getElementById('delete-data-' + id).submit();
    }
</script>

<script>
    //callback function to fetch data using api from database
    let import_product = document.getElementById('import_product')
    let status = document.getElementById('order_status')
    import_product.addEventListener('click', importOrder)


    async function importOrder() {

        status.innerHTML = 'Product Importing, Pleas Wait';

        let data = await fetch(`http://localhost:8080/logistable/api/orders/1/import`)
            .then(response => response.json())
            .then(json => {

                return json;
            })

        status.innerHTML = 'Product Imported Successfully';

        console.log(data)
    }
</script>


@endsection