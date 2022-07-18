<!-- partial:partials/_navbar.html -->
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row" aria-label="navbar">
    <div class="navbar-brand-wrapper d-flex justify-content-center">
        <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
            <a class="navbar-brand brand-logo" href="{{route('admin')}}"><img src="{{url('/api/image/logo')}}"
                                                                                alt="logo"/></a>
            <a class="navbar-brand brand-logo-mini" href="{{route('admin')}}"><img
                    src="{{url('/api/image/minilogo')}}" alt="logo"/></a>
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            
                <span class="mdi mdi-sort-variant"></span>
            </button>
            
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <ul class="navbar-nav navbar-nav-right">

            <li class="nav-item nav-profile dropdown">


                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img src="{{url('/api/image/users')}}" alt="profile">
                    <span class="nav-profile-name">{{{ Auth::user()->name}}}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">

                    <a class="dropdown-item" href="{{route('admin.account')}}">
                        <i class="mdi mdi-account text-primary"></i>Account Settings</a>

                    <a class="dropdown-item" href="{{route('admin.settings')}}">
                        <i class="mdi mdi-settings text-primary"></i>Setting</a>


                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout text-primary"></i>Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>


            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
<!-- partial -->
<div class="container-fluid page-body-wrapper">
    <!-- partial:partials/_sidebar.html -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar" aria-label="sidbar">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link" href="{{route('home')}}">
                    <i class="mdi mdi-home menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>



            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.featured')}}">
                <i class="mdi mdi-bookmark-remove menu-icon"></i>
                    <span class="menu-title">Featured</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.movies')}}">
                    <i class="mdi mdi-movie menu-icon"></i>
                    <span class="menu-title">Movies</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.series')}}">
                    <i class="mdi mdi-youtube-tv menu-icon"></i>
                    <span class="menu-title">Series</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.animes')}}">
                    <i class="mdi mdi-alpha-a-box menu-icon"></i>
                    <span class="menu-title">Animes</span>
                </a>
            </li>



            

            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.streaming')}}">
                    <i class="mdi mdi-access-point menu-icon"></i>
                    <span class="menu-title">Streaming</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.upcomings')}}">
                    <i class="mdi mdi-transfer-up menu-icon"></i>
                    <span class="menu-title">Upcoming</span>
                </a>
            </li>

    

            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.server')}}">
                    <i class="mdi mdi-quality-high menu-icon"></i>
                    <span class="menu-title">Servers & DRM</span>
                </a>
            </li>



            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.headers')}}">
                    <i class="mdi mdi-quality-high menu-icon"></i>
                    <span class="menu-title">Headers & User Agents</span>
                </a>
            </li>



            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.categories')}}">
                    <i class="mdi mdi-apps menu-icon"></i>
                    <span class="menu-title">Streaming Categories</span>
                </a>
            </li>


           

    

            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.genres')}}">
                    <i class="mdi mdi-apps menu-icon"></i>
                    <span class="menu-title">Genres</span>
                </a>
            </li>



            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.networks')}}">
                    <i class="mdi mdi mdi-access-point menu-icon"></i>
                    <span class="menu-title">Networks</span>
                </a>
            </li>



            
            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.casters')}}">
                    <i class="mdi mdi-apps menu-icon"></i>
                    <span class="menu-title">Casters</span>
                </a>
            </li>



            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.users')}}">
                    <i class="mdi mdi-account menu-icon"></i>
                    <span class="menu-title">Users</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.comments')}}">
                    <i class="mdi mdi-account menu-icon"></i>
                    <span class="menu-title">Comments</span>
                </a>
            </li>



            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.plans')}}">
                    <i class="mdi mdi-account menu-icon"></i>
                    <span class="menu-title">Plans & Subscriptions</span>
                </a>
            </li>




            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.ads')}}">
                    <i class="mdi mdi-currency-usd menu-icon"></i>
                    <span class="menu-title">Ad Manager</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.reports')}}">
                    <i class="mdi mdi-information-variant menu-icon"></i>
                    <span class="menu-title">Reports</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.suggestions')}}">
                <i class="mdi mdi-comment-alert menu-icon"></i>
                    <span class="menu-title">Suggestions</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.notifications')}}">
                    <i class="mdi mdi-bell-ring menu-icon"></i>
                    <span class="menu-title">Notifications</span>
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{route('admin.settings')}}">
                    <i class="mdi mdi-settings menu-icon"></i>
                    <span class="menu-title">Settings</span>
                </a>
            </li>

            
        </ul>



    </nav>


