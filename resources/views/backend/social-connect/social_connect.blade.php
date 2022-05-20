@extends('layout.app')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">

  <div class="row">
    <div class="col-xl-12 col-50 box-col-12 des-xl-50">
      <div class="card">
        <div class="card-header">
          <div class="header-top align-items-center">
            <div class="social-media">
              <ul>
                <a href="{{ $facebook_connection_url }}" style="background:#4267B2;">
                  <li>
                    Facebook
                  </li>
                </a>
                <a href="#" style="background:#DD2A7B;">
                  <li>
                    Instagram
                  </li>
                </a>
                <a href="{{ $linkedin_connection_url }}" style="background:#0e76a8;">
                  <li>
                    Linked
                  </li>
                </a>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<style>
  .social-media {}

  .social-media ul {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px;
  }

  .social-media ul li {}

  .social-media a {
    color: #dadada;
    padding: 8px 80px;
    margin: 10px;
    border-radius: 5px;
    font-size: 18px;
  }
</style>
@endsection
@section('script')
<script type="application/javascript">
  console.log('you can add your custom script here!')
  console.log($('a.nav-link.menu-title.active').offset())
</script>
@endsection