@extends('layout.app')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">
  <div class="row">

    <div class="col-xl-3 col-sm-6 box-col-3 chart_data_right">
      <div class="card income-card card-secondary">
        <div class="card-body align-items-center">
          <div class="round-progress knob-block text-center">
            <i class="fa fa-shopping-cart text-success" style="font-size: 60px;"></i>
            <h5 style="font-weight: 700; margin-top: 15px;">Total Shop: <span class="text-success" style="font-weight: 700;">12</span></h5>
            <p style="font-weight:400; color: #0d0d0d; font-size: 12px;"><a href="#">View all Shops</a></p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 box-col-3 chart_data_right">
      <div class="card income-card card-secondary">
        <div class="card-body align-items-center">
          <div class="round-progress knob-block text-center">
            <i class="fa fa-cubes text-success" style="font-size: 60px;"></i>
            <h5 style="font-weight: 700; margin-top: 15px;">Total Products: <span class="text-success" style="font-weight: 700;">12</span></h5>
            <p style="font-weight:400; color: #0d0d0d; font-size: 12px;"><a href="#">View all Products</a></p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 box-col-3 chart_data_right">
      <div class="card income-card card-secondary">
        <div class="card-body align-items-center">
          <div class="round-progress knob-block text-center">
            <i class="fa fa-shopping-bag text-success" style="font-size: 60px;"></i>
            <h5 style="font-weight: 700; margin-top: 15px;">Total Order: <span class="text-success" style="font-weight: 700;">12</span></h5>
            <p style="font-weight:400; color: #0d0d0d; font-size: 12px;"><a href="#">View all Orders</a></p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 box-col-3 chart_data_right">
      <div class="card income-card card-secondary">
        <div class="card-body align-items-center">
          <div class="round-progress knob-block text-center">
            <i class="fa fa-users text-success" style="font-size: 60px;"></i>
            <h5 style="font-weight: 700; margin-top: 15px;">Total Customer: <span class="text-success" style="font-weight: 700;">12 </span></h5>
            <p style="font-weight:400; color: #0d0d0d; font-size: 12px;">View all Customers</p>
          </div>
        </div>
      </div>
    </div>

  </div>
  <div class="row">
    <div class="col-xl-4 col-50 box-col-6 des-xl-50">
      <div class="card">
        <div class="card-header">
          <div class="header-top d-sm-flex align-items-center">
            <h5>Sales Overview</h5>
            <div class="center-content">
              <p class="d-flex align-items-center"><i class="toprightarrow-primary fa fa-arrow-up me-2"></i>80% Growth</p>
            </div>
            <div class="setting-list">
              <ul class="list-unstyled setting-option">
                <li>
                  <div class="setting-primary"><i class="icon-settings"> </i></div>
                </li>
                <li><i class="view-html fa fa-code font-primary"></i></li>
                <li><i class="icofont icofont-maximize full-card font-primary"></i></li>
                <li><i class="icofont icofont-minus minimize-card font-primary"></i></li>
                <li><i class="icofont icofont-refresh reload-card font-primary"></i></li>
                <li><i class="icofont icofont-error close-card font-primary"></i></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div id="chart-dashbord"></div>
        </div>
      </div>
    </div>
    <div class="col-xl-8 recent-order-sec">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <h5>Recent Orders</h5>

            <table class="table table-bordernone">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Date</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Status</th>
                  <th>
                    <div class="setting-list">
                      <ul class="list-unstyled setting-option">
                        <li>
                          <div class="setting-primary"><i class="icon-settings"> </i></div>
                        </li>
                        <li><i class="view-html fa fa-code font-primary"></i></li>
                        <li><i class="icofont icofont-maximize full-card font-primary"></i></li>
                        <li><i class="icofont icofont-minus minimize-card font-primary"></i></li>
                        <li><i class="icofont icofont-refresh reload-card font-primary"></i></li>
                        <li><i class="icofont icofont-error close-card font-primary"></i></li>
                      </ul>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="media"><img height="80" width="80" class="img-fluid rounded-circle" src="https://cdn.pixabay.com/photo/2020/05/18/16/17/social-media-5187243__340.png" alt="" data-original-title="" title="">
                      <div class="media-body"><a href="product-page.html"><span>Jhondoe</span></a></div>
                    </div>
                  </td>
                  <td>
                    <p>12 dasys ago</p>
                  </td>
                  <td>12</td>

                  <td>
                    <p>12</p>
                  </td>
                  <td>
                    <p>Completed</p>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-12 col-50 box-col-12 des-xl-50">
      <div class="card">
        <div class="card-header">
          <div class="header-top align-items-center">
            <h5>Connect your social media</h5>

            <div class="social-media">
              <ul>
                <a href="#" style="background:#4267B2;">
                  <li>
                    Facebook
                  </li>
                </a>
                <a href="#" style="background:#DD2A7B;">
                  <li>
                    Instagram
                  </li>
                </a>
                <a href="#" style="background:#0e76a8;">
                  <li>
                    Linked
                  </li>
                </a>
              </ul>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div id="chart-dashbord"></div>
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