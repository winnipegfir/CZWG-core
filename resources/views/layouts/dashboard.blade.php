<!DOCTYPE HTML>
<html lang="en">
    <head>
        <!--
        {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->sys_name}}
        {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->release}} ({{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->sys_build}})
        Built on Bootstrap 4 and Laravel 6

        Written by Liesel D... edited by a hundred Winnipegers.

        For Flight Simulation Use Only - Not to be used for real-world navigation. All content on this web site may not be shared, copied, reproduced or used in any way without prior express written consent of Gander Oceanic. © Copyright {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->copyright_year}} Gander Oceanic, All Rights Reserved.
        -->
        <!--Metadata-->
            
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="{{ asset('winnipeg.ico') }}" type="image/x-icon">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!--Rich Preview Meta-->
        <title>@yield('title', 'Winnipeg FIR')</title>
        <meta name="description" content="@yield('description', '')">
        <meta name="theme-color" content="#013162">
        <meta name="og:title" content="@yield('title', 'Winnipeg FIR')">
        <meta name="og:description" content="@yield('description', '')">
        <meta name="og:image" content="@yield('image','https://winnipegfir.ca/storage/files/uploads/1612961752.png')">
        <link rel="shortcut icon" href="{{ asset('winnipeg.ico') }}" type="image/x-icon">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
        <!-- Bootstrap core CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.1.3/materia/bootstrap.min.css" rel="stylesheet" integrity="sha384-5bFGNjwF8onKXzNbIcKR8ABhxicw+SC1sjTh6vhSbIbtVgUuVTm2qBZ4AaHc7Xr9" crossorigin="anonymous">        <!-- Material Design Bootstrap -->
        <!-- Material Design Bootstrap -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.11/css/mdb.min.css" rel="stylesheet">
        <!--SimpleMDE Editor-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
        <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
        <!-- JQuery -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <!-- Bootstrap tooltips -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js"></script>
        <!-- Bootstrap core JavaScript -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <!-- MDB core JavaScript -->
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.8.11/js/mdb.min.js"></script>
        <!--CZQO specific CSS-->
        @if (Auth::check())
        @switch (Auth::user()->preferences)
            @case("default")
            <link href="{{ asset('css/czqomd.css') }}" rel="stylesheet">
            @break
            @default
            <link href="{{ asset('css/czqomd.css') }}" rel="stylesheet">
        @endswitch
        @else
        <link href="{{ asset('css/czqomd.css') }}" rel="stylesheet">
        @endif
        <!--Leaflet-->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
        <script src="{{asset('/js/leaflet.rotatedMarker.js')}}"></script>
        <!--TinyMCE-->
        <script src='https://cloud.tinymce.com/5/tinymce.min.js?apiKey=k2zv68a3b4m423op71lnifx4a9lm0a2ee96o58zafhrdnddb'></script>
        <!--DataTables-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css"/>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js">
        <!--CSS Emoticons-->
        <link href="{{asset('css/jquery.cssemoticons.css')}}" media="screen" rel="stylesheet" type="text/css" />
        <script src="{{asset('/js/jquery.cssemoticons.js')}}" type="text/javascript"></script>
        <!--Fullcalendar-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.0.2/main.css">
        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
        <!--IntroJS-->
        <link rel="stylesheet" href="{{asset('introjs/introjs.min.css')}}">
        <script src="{{asset('introjs/intro.min.js')}}"></script>
        <!--Date picker-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <!--SimpleMDE-->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
        <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
        <!--Dropzone-->
        <script src="{{asset('js/dropzone.js')}}"></script>
        <!--JqueryValidate-->
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
    </head>
    <body>
    <!--Header-->
    @include('maintenancemode::notification')
    @if (\App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->banner)
        <div class="alert alert-{{\App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->bannerMode}}" style="margin: 0; border-radius: 0; border: none;">
            <div class="text-center align-self-center">
                <a href="{{\App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->bannerLink}}" target="_blank"><span style="margin: 0;">{{\App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->banner}}</span></a>
            </div>
        </div>
    @endif

    <header>
        <nav id="czwgHeader" class="navbar navbar-expand-lg navbar-dark p-0" style="min-height:59px; background-color: #fff">
            <div class="container">
                <a class="navbar-brand" href="{{route('index')}}"><img style="height: 40px; width:auto;" src="https://winnipegfir.ca/storage/files/uploads/1612961752.png" alt=""></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <i class="fas fa-bars"  style="color:#013162"></i>
                    </span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        {{-- <li class="nav-item">
                            <a href="{{route('controllerbookings.public')}}" class="nav-link {{ Request::is('bookings/*') || Request::is('bookings') ? 'active' : '' }}">Bookings</a>
                        </li> --}}
                        <li class="nav-item {{ Request::is('news/*') || Request::is('news') ? 'active' : '' }}">
                            @if(Auth::check() && Auth::user()->permissions >= 4)
                            <li class="nav-item dropdown {{ Request::is('news') || Request::is('news/*') || Request::is('news') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer; color: #013162" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">News</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="{{route('news')}}">News</a>
                                <a class="dropdown-item {{ Request::is('news') ? 'active white-text' : '' }}" href="{{route('news.index')}}">Manage News</a>
                            @else
                                <a href="{{route('news')}}" class="nav-link">News</a>
                            @endif
                        </li>
                        <li class="nav-item {{ Request::is('events/*') || Request::is('events') ? 'active' : '' }}">
                            @if(Auth::check() && Auth::user()->permissions >= 4)
                            <li class="nav-item dropdown {{ Request::is('events') || Request::is('events/*') || Request::is('events') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer; color: #013162" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Events</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="{{route('events.index')}}">Events</a>
                                <a class="dropdown-item {{ Request::is('events') ? 'active white-text' : '' }}" href="{{route('events.admin.index')}}">Manage Events</a>
                            @else
                                <a href="{{route('events.index')}}" class="nav-link">Events</a>
                            @endif
                        </li>
                        <li class="nav-item dropdown {{ Request::is('dashboard/applicationdashboard/application') || Request::is('dashboard/application/*') || Request::is('atcresources') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer; color: #013162" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">ATC</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="{{route('roster.public')}}">Roster</a>
                            @if(Auth::check() && Auth::user()->permissions >= 4)
                                <a class="dropdown-item {{ Request::is('roster') ? 'active white-text' : '' }}" href="{{route('roster.index')}}">Manage Roster</a>
                            @endif
                            @if(!Auth::check() || Auth::user()->permissions == 0)
                                <a class="dropdown-item {{ Request::is('join') ? 'active white-text' : '' }}" href="{{url ('/join')}}">How to Become a Winnipeg Controller</a>
                                @auth
                                <a class="dropdown-item {{ Request::is('dashboard/application/list') ? 'active white-text' : '' }}" href="{{url ('/dashboard/application/list')}}">Your Applications</a>
                                @endauth
                                <a class="dropdown-item" href="{{route('training')}}">Training</a>
                            @endif
                            </div>
                        </li>
                        <li class="nav-item dropdown {{ Request::is('airports') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer; color: #013162" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pilots</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <a class="dropdown-item" href="{{route('airports')}}">Airports</a>
                                <a class="dropdown-item" href="https://www.vatsim.net/pilots/resources" target="_blank">VATSIM Resources</a>
                                <a class="dropdown-item" href="https://simaware.ca" target="_blank">Live Map</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer; color: #013162" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Publications</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <a class="dropdown-item {{ Request::is('policies') ? 'active white-text' : '' }}" href="{{route('policies')}}">Policies</a>
                                <a class="dropdown-item {{ Request::is('meetingminutes') ? 'active white-text' : '' }}" href="{{route('meetingminutes')}}">Meeting Minutes</a>
                                <a class="dropdown-item {{ Request::is('privacy') ? 'active white-text' : '' }}" href="{{route('privacy')}}">Privacy Policy</a>
                            </div>
                        </li>
                        <li class="nav-item  {{ Request::is('staff') ? 'active' : '' }}">
                            <a style="color: #013162" class="nav-link" href="{{url ('/staff')}}" aria-expanded="false">Staff</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" style="cursor:pointer; color: #013162" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Feedback</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <a class="dropdown-item {{ Request::is('feedback') ? 'active white-text' : '' }}" href="{{route('feedback.create')}}">Submit Feedback</a>
                                <a class="dropdown-item {{ Request::is('yourfeedback') ? 'active white-text' : '' }}" href="{{route('yourfeedback')}}">Your Feedback</a>
                            </div>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto nav-flex-icons">
                        @unless (Auth::check())
                        <li class="nav-item d-flex align-items-center">
                            {{-- <a href="{{route('auth.connect.login')}}" class="nav-link waves-effect waves-light">
                                <i class="fas fa-sign-in-alt"></i>&nbsp;Login
                            </a> --}}
                            <a href="{{route('auth.connect.login')}}" class="nav-link waves-effect waves-light">
                                <i class="fas fa-sign-in-alt" style="color: #013162"></i>&nbsp;Login
                            </a>
                        </li>
                        @endunless
                        @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-333" data-toggle="dropdown" style="color: #013162" aria-haspopup="true" aria-expanded="false">
                                <img src="{{Auth::user()->avatar()}}" style="height: 27px; width: 27px; margin-right: 7px; margin-bottom: 3px; border-radius: 50%;">&nbsp;<span class="font-weight-bold">{{Auth::user()->fullName("F")}}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right dropdown-default py-0" aria-labelledby="navbarDropdownMenuLink-333">
                                <a class="dropdown-item {{ Request::is('dashboard') || Request::is('dashboard/*')}}" href="{{route('dashboard.index')}}">
                                    <i class="fa fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                @if(!Auth::check() || Auth::user()->permissions >= 2)
                                <a class="dropdown-item {{ Request::is('training.index') || Request::is('training.index/*')}}" "dropdown-item" href="{{route('training.index')}}">
                                    <i class="fa fa-graduation-cap mr-2" style="margin-left: -1px"></i>1Winnipeg
                                </a>
                                @endif
                                <a class="dropdown-item red-text" href="{{route('auth.logout')}}">
                                    <i class="fa fa-sign-out-alt mr-2"></i>&nbsp;Logout
                                </a>
                            </div>
                        </li>
                        @endauth
                        <li class="nav-item d-flex align-items-center">
                            <a href="https://www.facebook.com/CZWGFIR" class="nav-link waves-effect waves-light" target="_BLANK" >
                                <i style="font-size: 1.7em; color: #013162" class="fab fa-facebook"></i>
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a href="https://twitter.com/CZWGFIR" class="nav-link waves-effect waves-light" target="_BLANK" >
                                <i style="font-size: 1.7em; color: #013162" class="fab fa-twitter"></i>
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a href="https://www.instagram.com/CZWGFIR" class="nav-link waves-effect waves-light" target="_BLANK" >
                                <i style="font-size: 1.7em; color: #013162" class="fab fa-instagram"></i>
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link waves-effect waves-light" data-toggle="modal" data-target="#discordTopModal" target="_BLANK" >
                                <i style="height: 22px; font-size: 1.7em; width: 28px; padding-left: 5px; padding-top: 2px; color: #013162" class="fab fa-discord"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    @if ($errors->any())
        <div class="alert alert-danger" style="margin: 0; border-radius: 0; border: none;">
            <div class="container">
                @foreach ($errors->all() as $error)
                    {{ $error }} <br>
                @endforeach
            </div>
        </div>
    @endif
    @if (\Session::has('success'))
        <div class="alert alert-success" style="margin: 0; border-radius: 0; border: none;">
            <div class="container">
                {!! \Session::get('success') !!}
            </div>
        </div>
    @endif
    @if (\Session::has('error'))
        <div class="alert alert-danger" style="margin: 0; border-radius: 0; border: none;">
            <div class="container">
                {!! \Session::get('error') !!}
            </div>
        </div>
    @endif
    @if (\Session::has('info'))
        <div class="alert alert-info" style="margin: 0; border-radius: 0; border: none;">
            <div class="container">
                {!! \Session::get('info') !!}
            </div>
        </div>
    @endif
    <!--End header-->
    <!--SIDEBAR-->
    <div class="sidebar" id="cywgSidebar">
      @yield('sidebar')
    </div>
    <div id="czqoContent">
        @yield('content')
    </div>
    <!-- Footer -->
    <!-- Footer -->
    <footer class="page-footer text-light font-small py-4 bg-dark {{Request::is('/dashboard') ? 'mt-5' : ''}}">
        <div class="container">
            <p style="color:white">For Flight Simulation Use Only - Not to be used for real-world navigation. By using this site, you agree to hold harmless and indemnify the owners and authors of these web pages, those listed on these pages, and all pages that this site that may be pointed to (i.e. external links).</p>
            <p style="color:white">Copyright © {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->copyright_year}} Winnipeg FIR | All Rights Reserved.</p>
            <div class="flex-left mt-3">
            <a href="{{route('about')}}">Github</a>
                &nbsp;
                •
                &nbsp;
                <a href="{{route('feedback.create')}}">Feedback</a>
                &nbsp;
                •
                &nbsp;
                <a href="{{route('privacy')}}">Privacy Policy</a>
                &nbsp;
                •
                &nbsp;
                <a href="{{route('branding')}}">Branding</a>
                &nbsp;
                •
                &nbsp;
                <a href="https://www.vatcan.ca">VATCAN</a>
                &nbsp;
                •
                &nbsp;
                <a href="https://www.vatsim.net">VATSIM</a>
            </div>

            <div class="mt-3">
                <p>The Winnipeg FIR stands with the LGBTQIA+ community on VATSIM.</p>
                <a href="{{route('about')}}"><small class="text-muted">{{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->sys_name}} {{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->release}} ({{App\Models\Settings\CoreSettings::where('id', 1)->firstOrFail()->sys_build}})</small></a> <small>- <a target="_blank" href="https://blog.winnipegfir.ca" class="text-muted">The Winnipeg FIR Blog</a></small>
            </div>
        </div>
    </footer>
    <!-- Footer -->
    @if (Auth::check() && Auth::user()->init == 0 && Request::is('privacy') == false)
    <!--Privacy welcome modal-->
    <div class="modal fade" id="welcomeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Welcome to CZWG!</b></h5>
                </div>
                <div class="modal-body">
                    Welcome to the Winnipeg FIR Core system. Here you can apply to become a visiting controller (if not a home controller), organize your
                    training, and access important pilot and controller resources! Before
                    we allow you to use the system, we require you to accept our Privacy Policy. The Policy is available
                    <a target="_blank" href="{{url('/privacy')}}">here.</a>
                    By default, you are <b>not</b> subscribed to non-essential email notifications. Head to the Dashboard and click on "Manage my preferences" to
                    subscribe - we highly recommend it!
                </div>
                <div class="modal-footer">
                    <a role="button" href="{{ URL('/privacydeny') }}" class="btn btn-outline-danger">I Disagree</a>
                    <a href="{{url('/privacyaccept')}}" role="button" class="btn btn-success">I Agree</a>
                </div>
            </div>
        </div>
    </div>
        <script>
            $('#welcomeModal').modal({backdrop: 'static'});
            $('#welcomeModal').modal('show');
        </script>
    <!-- End privacy welcome modal-->
    @endif
    <!-- Contact us modal-->
    <div class="modal fade" id="contactUsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Contact CZWG</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    To contact us, please do one of the following:
                    <ol>
                        <li>Login and open a <a href="{{route('tickets.index')}}">support ticket.</a></li>
                        <li>Head to the <a href="{{route('staff')}}">staff page</a> and email the relevant staff member.</li>
                        <li>Join our <a href="https://discord.gg/WYQjbxv">Discord server</a> and ask in the #general channel.</li>
                    </ol>
                    <b>If your query is related to ATC coverage for your event, please visit <a href="{{route('events.index')}}">this page.</a></b>
                </div>
            </div>
        </div>
    </div>
    <!-- End contact us modal-->
    @if (\Session::has('error-modal'))
    <!-- Error modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><span class="font-weight-bold red-text"><i class="fas fa-exclamation-circle"></i> An error occurred...</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{\Session::get('error-modal')}}
                    <div class="alert black-text bg-czqo-blue-light mt-4">
                        If you believe this is a mistake, please create a <a target="_blank" class="black-text" href="{{route('tickets.index')}}">support ticket.</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    $("#errorModal").modal();
    </script>
    <!-- End error modal -->
    @endif
    <!-- Start Discord (top nav) modal -->
    <div class="modal fade" id="discordTopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Join the Winnipeg Discord!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <img style="height: 90px;" src="{{asset('/img/discord/winnipegdiscord.png')}}" class="img-fluid mb-2" alt=""></img>
                    <p>To link your Discord account and join our Discord community, please head to your <a href="{{route('dashboard.index')}}">dashboard.</a></p>
                    <p>VATCAN has a Discord too! You can join the VATCAN discord by clicking <a href="https://vatcan.ca/my/discord/join" rel="noopener noreferrer" target="_blank">here!</a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Discord (top nav) modal -->
    <!-- Start Connect modal -->
    <div class="modal fade" id="connectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Login with VATSIM</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>The Winnipeg FIR uses VATSIM Connect (auth.vatsim.net) for authentication. This is similar to SSO, but allows you to select specific data to share with us. Click 'Login' below to continue.</p>
                    <p><small>If you are having issues with Connect, please send an email to the Webmaster and use <a href="{{route('auth.sso.login')}}">SSO to login.</a></small></p>
                </div>
                <div class="modal-footer">
                    <a href="{{route('auth.connect.login')}}" role="button" class="btn bg-czqo-blue-light">Login</a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Connect modal -->
    <script type="text/javascript">
        Dropzone.options.dropzone =
            {
                maxFilesize: 12,
                renameFile: function (file) {
                    var dt = new Date();
                    var time = dt.getTime();
                    return time + file.name;
                },
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                addRemoveLinks: true,
                timeout: 5000,
                success: function (file, response) {
                    console.log(response);
                },
                error: function (file, response) {
                    return false;
                }
            };
    </script>
    <script>
        $("blockquote").addClass('blockquote');
    </script>
    </body>
</html>
