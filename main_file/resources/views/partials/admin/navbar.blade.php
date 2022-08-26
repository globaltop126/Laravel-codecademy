<nav class="navbar navbar-main navbar-expand-lg navbar-border n-top-header" id="navbar-main">
    <div class="container-fluid">
        <button class="navbar-toggler responsive_none " type="button" data-toggle="collapse" data-target="#navbar-main-collapse" aria-controls="navbar-main-collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-user d-lg-none">
            <ul class="navbar-nav flex-row align-items-center navbar_msg_responsive">
                <li class="nav-item ">
                    <a href="#" class="nav-link nav-link-icon sidenav-toggler text-white" data-action="sidenav-pin" data-target="#sidenav-main"><i class="fas fa-bars"></i></a>
                </li>

                <li class="nav-item dropdown dropdown-animate">
                    <a class="nav-link pr-lg-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="avatar avatar-sm rounded-circle">
                        <img class="avatar avatar-sm rounded-circle" {{ Auth::user()->img_avatar }} />
                      </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right dropdown-menu-arrow">
                        <h6 class="dropdown-header px-0">{{__('Hi')}}, {{Auth::user()->name }}</h6>
                        <a href="{{route('profile')}}" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>{{__('My profile')}}</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>{{__('Logout')}}</span>
                        </a>
                        <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
                <li class="ml-2">
                    <div class="row{{!empty($userTask)?"mt-3":""}}">
                        <div class="col-auto tracking-clock">
                            @if(\Auth::user()->type != 'admin')
                            <a href="{{route('taskBoard.view')}}" class="text-white" @if(empty($trackerdata)) data-toggle="tooltip" data-original-title="{{__('No time tracking running')}}" @endif><i class="fa fa-clock"></i></a>
                        </div>
                        <div class="col-auto text-white">
                            <div class="timer-counter"></div>
                        </div>
                        <div class="col-auto text-white">
                            <p class="start-task"></p>
                        </div>
                    
                    </div>
                </li>
                <li>
                    <div>
                        <a href="{{ url('chats') }}" class="pt-2 text-white">
                            <span><i class="fas fa-comment" style="font-size: 21px"></i></span>
                            <span class="badge badge-danger badge-circle badge-btn custom_messanger_counter">
                                {{$unseenCounter}}
                            </span>
                        </a>
                        </div>
                </li>
                @endif
            </ul>
        </div>
        <div class="collapse navbar-collapse navbar-collapse-fade" id="navbar-main-collapse">
            <ul class="navbar-nav align-items-center d-none d-lg-flex">
              
                <li class="ml-2">
                    <div class="row{{!empty($userTask)?"mt-3":""}}">
                        <div class="col-auto tracking-clock">
                            @if(\Auth::user()->type != 'admin')
                            <a href="{{route('taskBoard.view')}}" class="text-white" @if(empty($trackerdata)) data-toggle="tooltip" data-original-title="{{__('No time tracking running')}}" @endif><i class="fa fa-clock"></i></a>
                        </div>
                        <div class="col-auto text-white">
                            <div class="timer-counter"></div>
                        </div>
                        <div class="col-auto text-white">
                            <p class="start-task"></p>
                        </div>
                    
                    </div>
                </li>
                {{-- @if(\Auth::user()->type=='company' || \Auth::user()->type=='employee') --}}
                <li>
                    <div>
                        <a href="{{ url('chats') }}" class="pt-2 ml-4 text-white">
                            <span><i class="fas fa-comment" style="font-size: 21px"></i></span>
                            <span class="badge badge-danger badge-circle badge-btn custom_messanger_counter">
                                {{$unseenCounter}}
                            </span>
                        </a>
                    </div>
                </li>
                @endif
            </ul>

            <ul class="navbar-nav ml-lg-auto align-items-center d-none d-lg-flex">
                <li class="nav-item">
                    <a href="#" class="nav-link nav-link-icon sidenav-toggler" data-action="sidenav-pin" data-target="#sidenav-main"><i class="fas fa-bars"></i></a>
                </li>
                @if(Auth::user()->type != 'admin')
                    <li class="nav-item text-white">
                        <a href="#" class="nav-link nav-link-icon" data-action="omnisearch-open" data-target="#omnisearch"><i class="fas fa-search"></i></a>
                    </li>
                @endif
                <li class="nav-item dropdown dropdown-animate">
                    <a class="nav-link pr-lg-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media media-pill align-items-center">
                        <span class="avatar rounded-circle">
                          <img class="avatar rounded-circle" {{ Auth::user()->img_avatar }}>
                        </span>
                            <div class="ml-2 d-none d-lg-block">
                                <span class="mb-0 text-sm  font-weight-bold text-white">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right dropdown-menu-arrow">
                        <h6 class="dropdown-header px-0">{{__('Hi,')}} {{ Auth::user()->name }}</h6>
                        <a href="{{ route('profile') }}" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>{{__('My profile')}}</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                    </div>
                </li>
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
           
        </div>
    </div>
</nav>