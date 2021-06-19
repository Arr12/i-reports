<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{!! csrf_token() !!}">
    <title>Welcome To | Internal Report</title>
    <!-- Favicon-->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    @stack('before-stack')
    <!-- Bootstrap Core Css -->
    <link href="/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="/plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="/plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="/plugins/morrisjs/morris.css" rel="stylesheet" />

    {{-- Skeleton Load --}}
    <link rel="stylesheet" href="/css/skeleton.css" rel="stylesheet" />

    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />

    <!-- Bootstrap DatePicker Css -->
    <link href="/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />

    <!-- Wait Me Css -->
    <link href="/plugins/waitme/waitMe.css" rel="stylesheet" />

    <!-- Bootstrap Select Css -->
    <link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="/css/style.css" rel="stylesheet">

    <!-- Customize themes -->
    <link href="/css/themes/all-themes.css" rel="stylesheet" />
    @stack('after-style')

    <style>
    #data-column .btn-primary{
        margin: 0.5rem;
    }
    .btn-primary .material-icons{
        color: #fff !important;
    }
    section.content{
        margin: 100px 15px 0 15px;
    }
    .desktop-toggle-hide{
        margin-left: -300px;
    }
    .desktop-toggle-show{
        margin-left: 0;
    }
    .sidebar {
        z-index: 99999999;
    }
    .desktop-toggle{
        position: absolute;
        left: 20px;
        top: 23px;
        color: #fff;
        cursor: pointer;
    }
    .desktop-toggle:hover{
        color: #fff;
    }
    .dropdown-menu.pull-right{
        border-radius: 10px;
        padding: 10px 0;
    }
    button.sign-out {
        border: 0;
        padding: 5px 42px;
        color: #000;
    }
    .btn{
        border-radius: 30px !important;
    }
    @media only screen and (min-width: 998px) {
        .custom-row{
            padding: 1rem 15rem;
        }
        .navbar > .container .navbar-brand, .navbar > .container-fluid .navbar-brand{
            margin-left:20px;
        }
    }
    @media only screen and (max-width: 998px) {
        .custom-row{
            padding: 0;
        }
        .desktop-toggle{
            display:none;
        }
        .ls-closed section.content{
            margin-left:0;
            margin-right:0;
        }
    }
    </style>
</head>
<body class="theme-red ls-closed">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    @include('admin.components.header')
    @include('admin.components.navbar')

    <section class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </section>

    @stack('before-script')
    <!-- Jquery Core Js -->
    <script src="/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="/plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="/plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="/plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="/plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="/plugins/raphael/raphael.min.js"></script>
    <script src="/plugins/morrisjs/morris.js"></script>

    {{-- Custom JS --}}
    <script src="/plugins/momentjs/moment.js"></script>
    <script src="/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    <script src="/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="/plugins/bootstrap-select/js/bootstrap-select.js"></script>
    <script src="/plugins/autosize/autosize.js"></script>
    <script src="/js/pages/forms/basic-form-elements.js"></script>

    @stack('after-script')

    <script>
        function createSkeleton(limit){
            var skeletonHTML = '';
            for(var i = 0; i < limit; i++){
                skeletonHTML += '<div class="skeletonWrapper">';
                skeletonHTML += '<span class="react-skeleton-load animated">&zwnj;</span>';
                skeletonHTML += '<span class="react-skeleton-load animated">&zwnj;</span>';
                skeletonHTML += '<span class="react-skeleton-load animated">&zwnj;</span>';
                skeletonHTML += '<span class="react-skeleton-load animated">&zwnj;</span>';
                skeletonHTML += '<span class="react-skeleton-load animated">&zwnj;</span>';
                skeletonHTML += '<span class="react-skeleton-load animated">&zwnj;</span>';
                skeletonHTML += '</div>';
            }
            return skeletonHTML;
        }
        $(".desktop-toggle").on('click',function(){
            if($(".sidebar").hasClass("desktop-toggle-show")){
                $(".sidebar").addClass("desktop-toggle-hide");
                $(".sidebar").removeClass("desktop-toggle-show");
            }else{
                $(".sidebar").addClass("desktop-toggle-show");
                $(".sidebar").removeClass("desktop-toggle-hide");
            }
        });
    </script>
    <!-- Custom Js -->
    <script src="/js/admin.js"></script>

    <!-- Demo Js -->
    <script src="/js/demo.js"></script>
</body>
</html>
