<header class="main-nav">

      <div class="sidebar-user text-center">
      <a class="setting-primary" href="#"><i data-feather="settings"></i></a>
        <img class="img-90 rounded-circle" src="{{ asset('/assets/images/dashboard/1.png') }}" alt="">
        <div class="badge-bottom">
        </div><a href="user-profile.html">
          <h6 class="mt-3 f-14 f-w-600"></h6>
        </a>
        <p class="mb-0 font-roboto">Jhondoe</p>
      </div>
      <nav>
        <div class="main-navbar">
          <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
          <div id="mainnav">
            <ul class="nav-menu custom-scrollbar">
              <li class="back-btn">
               
              </li>
              <li><a class="nav-link menu-title link-nav {{ Request::is('dashboard') ? 'actitve' : '' }}" href=""><i data-feather="home"></i><span>Dashboard</span></a></li>
              <li><a class="nav-link menu-title link-nav {{ Request::is('orders*') ? 'actitve' : '' }}" href="{{ url('social/connection') }}"><i data-feather="shopping-bag"></i><span>Social Integration</span></a></li>

              <li><a class="nav-link menu-title link-nav {{ Request::is('customers*')  ? ' actitve' : '' }}" href="{{ url('schedule/post/all') }}"><i data-feather="users"></i><span>Schedule Post</span></a></li>

              
             <li class="custom_hover"><a class="nav-link menu-title link-nav btn-primary-light" href=""><i data-feather="anchor"></i><span>Integration</span></a></li>
            </ul>
          </div>
          <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
      </nav>
    </header>