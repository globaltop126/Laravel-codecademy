@php
// $color = App\Models\Utility::color();

$setting = App\Models\Utility::colorset();
$color = (!empty($setting['color'])) ? $setting['color'] : '#6fd943';
// dd($color);

@endphp

<style>
    .application-offset .container-application:before {
        background-color: {{ $color }} !important;
    }

    .nav-application>.btn:hover:not(.active) {
        color: {{ $color }} !important;
    }

    .nav-application>.btn.active {
        background-color: {{ $color }} !important;
    }

    .custom-control-input:checked~.custom-control-label::before {
        border-color: {{ $color }} !important;
        background-color: {{ $color }} !important;
    }

    .btn-primary {

        background-color: {{ $color }} !important;
        border-color: {{ $color }} !important;
    }

    .btn-primary:hover {
        /* color: #FFF; */
        background-color: {{ $color }} !important;
        border-color: {{ $color }} !important;
    }

    .bg-primary {
        background-color: {{ $color }} !important;
    }

    .text-primary {
        color: {{ $color }} !important;
    }

    span.mb-0.text-sm.font-weight-bold.hover:hover {
        color: {{ $color }} !important;
    }

    .nav-link:hover,
    .nav-link.active {
        color: {{ $color }} !important;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        color: {{ $color }} !important;

    }
    
    .dropdown-item.active,
    .dropdown-item:active {
        color: {{ $color }} !important;

    }


    .btn-outline-primary {

    color:{{ $color }} !important;
    border-color: {{ $color }} !important;
}


    .btn-outline-primary:hover{
 
    color: #fff !important;
    background-color:{{ $color }} !important;
    border-color:{{ $color }} !important;

}


   .badge-primary {
    color: #fff;
    background-color:{{ $color }} !important;
}


    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
    color: #FFF !important;
    background-color: {{ $color }} !important;
}
</style>
