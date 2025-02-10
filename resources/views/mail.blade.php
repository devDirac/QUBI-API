<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <body>
    <div style="text-align:center">
        <img src="{{ URL::to('/') }}/img/logo_correo.jpg" alt="" width="200">    
    </div>
    {!! $titulo !!}
    {!! $html !!}
    </body>
</html>