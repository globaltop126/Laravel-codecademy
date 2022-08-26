{{-- @php
  $user= \App\Models\User::user_contactss();
  dd($user);
@endphp --}}
<!-- Sidenav header -->
<div class="sidenav-header d-flex align-items-center">
    <a class="navbar-brand" href="#">
        <img src="{{ asset(Storage::url('logo/logo.png')) }}" class="navbar-brand-img" alt="...">
    </a>
    <div class="ml-auto">
        <!-- Sidenav toggler -->
        <div class="sidenav-toggler sidenav-toggler-dark d-md-none" data-action="sidenav-unpin" data-target="#sidenav-main">
            <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
            </div>
        </div>
    </div>
</div>
<!-- User mini profile -->
<div class="sidenav-user d-flex flex-column align-items-center justify-content-between text-center">
    <!-- Avatar -->
    <div>
        <a href="#" class="avatar rounded-circle avatar-xl">
            <img class="avatar rounded-circle avatar-xl" {{ Auth::user()->img_avatar }} />
        </a>
        <div class="mt-4">
            <h5 class="mb-0 text-white">{{ Auth::user()->name }}</h5>
            <span class="d-block text-sm text-white opacity-8 mb-3">{{ Auth::user()->email }}</span>
            @if(Auth::user()->type != 'admin')
                <a href="{{ route('users.info',Auth::user()->id) }}" class="btn btn-sm btn-white btn-icon rounded-pill shadow hover-translate-y-n3">
                    <span class="btn-inner--icon"><i class="fas fa-coins"></i></span>
                    <span class="btn-inner--text">{{__('My Overview')}}</span>
                </a>
            @endif
        </div>
    </div>
    <!-- User info -->
    <!-- Actions -->
    @if(Auth::user()->type != 'admin')
        <div class="w-100 mt-4 actions">
            <a href="{{ route('profile') }}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{__('Profile')}}" class="action-item action-item-lg text-white pl-0 mx-3">
                <i class="fas fa-user"></i>
            </a>
            <a href="{{ route('expense.list') }}" data-toggle="tooltip" data-placement="bottom" data-original-title="{{__('Expense')}}" class="action-item action-item-lg text-white mx-3">
                <i class="fas fa-receipt"></i>
            </a>
        </div>
    @endif
</div>
<!-- Application nav -->
<div class="nav-application clearfix">
    <a href="{{ route('home') }}" class="btn btn-square text-sm {{ (Request::route()->getName() == 'home') ? 'active' : '' }}">
        <span class="btn-inner--icon d-block"><i class="fas fa-home fa-2x"></i></span>
        <span class="btn-inner--icon d-block pt-2">{{__('Home')}}</span>
    </a>
    @if(Auth::user()->type != 'admin')
        <a href="{{ route('projects.index') }}" class="btn btn-square text-sm {{ request()->is('project*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-project-diagram fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Projects')}}</span>
        </a>


        
        <a href="{{ route('taskBoard.view') }}" class="btn btn-square text-sm {{ request()->is('taskboard*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-tasks fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Tasks')}}</span>
        </a>
        <a href="{{ route('users') }}" class="btn btn-square text-sm {{ request()->is('users*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-users fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Members')}}</span>
        </a>  
          
       
        @if ( Auth::user()->type == 'owner' || \App\Models\User::GetusrRole()['role'] == 'client')
        <a href="{{ route('contractclient.index') }}" class="btn btn-square text-sm {{ request()->is('contractclient*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-receipt fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Contract')}}</span>
        </a>
        @endif
       
       

        <a href="{{ route('invoices.index') }}" class="btn btn-square text-sm {{ request()->is('invoices*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-receipt fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Invoices')}}</span>
        </a>
        <a href="{{ route('task.calendar',['all']) }}" class="btn btn-square text-sm {{ request()->is('calendar*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-calendar-week fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Calendar')}}</span>
        </a>
        <a href="{{ route('timesheet.list') }}" class="btn btn-square text-sm {{ request()->is('timesheet-list') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-clock fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Timesheet')}}</span>
        </a>
        <a href="{{ route('time.tracker') }}" class="btn btn-square text-sm {{ request()->is('time-tracker') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-stopwatch fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Tracker')}}</span>
        </a>
        <a href="{{ url('chats') }}" class="btn btn-square text-sm {{ request()->is('chats') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-comments fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Messenger')}}</span>
        </a>
           
         @if(Auth::user()->type != 'admin')
            <a href="{{ route('report_project.index') }}" class="btn btn-square text-sm  {{ request()->is('report_project*') ? 'active' : '' }}">
                <span class="btn-inner--icon d-block"><i class="fas fa-chart-line fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Project Report')}}</span>
                
            </a>
            @endif
       

        <a href="{{ route('zoommeeting.index') }}" class="btn btn-square text-sm {{ request()->is('zoommeeting*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-video fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Zoom Meeting')}}</span>
        </a>
    @endif
    @if(\Auth::user()->type == 'admin')
        <a href="{{route('lang',basename(App::getLocale()))}}" class="btn btn-square text-sm {{ request()->is('lang*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-language fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Language')}}</span>
        </a>
        <a href="{{route('email_template.index')}}" class="btn btn-square text-sm {{ request()->is('email_template*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-envelope fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Email Template')}}</span>
        </a>
        <a href="{{ route('plans.index') }}" class="btn btn-square text-sm {{ request()->is('plans*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-award fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Plans')}}</span>
        </a>
        <a href="{{ route('plan_request.index') }}" class="btn btn-square text-sm {{ request()->is('plan_request*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-paper-plane fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Plan Request')}}</span>
        </a>
        <a href="{{ route('order_list') }}" class="btn btn-square text-sm {{ request()->is('orders') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-file-invoice fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Orders')}}</span>
        </a>
        <a href="{{ route('coupons.index') }}" class="btn btn-square text-sm {{ request()->is('coupons*') ? 'active' : '' }}">
            <span class="btn-inner--icon d-block"><i class="fas fa-ticket-alt fa-2x"></i></span>
            <span class="btn-inner--icon d-block pt-2">{{__('Coupons')}}</span>
        </a>
      
    @endif
    
    <a href="{{ route('settings') }}" class="btn btn-square text-sm {{ request()->is('settings*') ? 'active' : '' }}">
        <span class="btn-inner--icon d-block"><i class="fas fa-cogs fa-2x"></i></span>
        <span class="btn-inner--icon d-block pt-2">{{__('Settings')}}</span>
    </a>
</div>
