@extends('layout.app')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Create Schedule Post</h5>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="connection-section">
                                    <div class="connection-form">
                                        <form action="{{ url('schedule/post/save') }}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="row mb-4">
                                                        <div class="col-md-3 text-right">
                                                            <label>Post Description</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <textarea class="form-control @error('body') is-invalid @enderror" name="body" placeholder="Enter post description" rows="10">{{old('body')}}</textarea>
                                                            <label class="error_msg">{{ $errors->first('body')}}</label>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="row mb-4">
                                                        <div class="col-md-3 text-right">
                                                            <label>Post Image</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="file" name="image" id="image" />
                                                            <label class="error_msg">{{ $errors->first('image') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                        <div class="col-md-3 text-right">
                                                            <label>Schedule Post</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="checkbox" name="is_schedule" id="is_schedule" value="1" />
                                                            <label class="invalid-feedback">{{ $errors->first('is_schedule') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="submit-section">
                                                            <button class="btn btn-primary" type="submit">Save</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="social-feed form-control">
                                                        <h3>Share on social feed</h3>
                                                        <div class="single-social-feed">
                                                            @foreach($fb_pages as $page)
                                                            <label class="container">{{ $page->page_name}}
                                                                <input type="checkbox" name="facebook[]" value="{{ $page->page_id }}" />
                                                                <span class="checkmark"></span>
                                                            </label>
                                                            @endforeach
                                                            <label class="error_msg">{{ $errors->first('facebook')}}</label>
                                                            @if($linkedin_id)
                                                            <label class="container">Linkedin
                                                                <input type="checkbox" name="linkedin[]" value="{{ $linkedin_id }}" />
                                                                <span class="checkmark"></span>
                                                            </label>
                                                            <label class="error_msg">{{ $errors->first('linkedin')}}</label>
                                                            @endif

                                                            <label class="container">Instagram
                                                                <input type="checkbox" name="instagram[]" value="1" />
                                                                <span class="checkmark"></span>
                                                            </label>
                                                            <label class="error_msg">{{ $errors->first('instagram')}}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .social-feed {
        padding: 25px;
    }

    .social-feed h3 {
        font-size: 18px;
        padding-bottom: 18px;
    }

    /* Customize the label (the container) */
    .container {
        display: block;
        position: relative;
        padding-left: 35px;
        margin-bottom: 12px;
        cursor: pointer;
        font-size: 14px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 2px;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: #eee;
    }

    /* On mouse-over, add a grey background color */
    .container:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .container input:checked~.checkmark {
        background-color: #24695c;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .container input:checked~.checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .container .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .error_msg{
        color: red;
        font-weight: normal;
        font-size: 14px;
    }
    .success_msg{

    }
</style>
@endsection