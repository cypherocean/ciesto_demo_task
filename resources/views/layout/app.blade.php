<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    @include('layout.meta')
    <title>@yield('title') - Ciesto Shopping Mart</title>
    @include('layout.styles')
</head>

<body>

    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

        @include('layout.header')
  
        @include('layout.sidebar')
      
        <div class="page-wrapper">
           
            

            @yield('content')
      
            @include('layout.footer')
         
        </div>
     
    </div>

    @include('layout.scripts')    
</body>

</html>