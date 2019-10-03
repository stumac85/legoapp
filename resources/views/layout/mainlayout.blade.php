<!DOCTYPE html>
 
<html lang="en">
 
 <head>
 
   @include('layout.partials.head')
 
 </head>
 
 <body>
 
 
@include('layout.partials.nav')
 
<main role="main">

@include('layout.partials.header')
 
@yield('content')
 
</main>

@include('layout.partials.footer')
 
@include('layout.partials.footer-scripts')
 
 </body>
 
</html>