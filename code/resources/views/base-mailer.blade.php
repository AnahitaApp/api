<html xmlns="http://www.w3.org/1999/html">

<body>
    @if($greeting)
        <h2>{{ $greeting }}</h2>
    @endif

    @yield('body')
</body>

</html>