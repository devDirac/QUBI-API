<!DOCTYPE html>
<html >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
        <link rel="stylesheet" href="https://getbootstrap.com/2.3.2/assets/css/bootstrap.css">
        <style type="text/css">
          html,body {height: 100%;}
          #wrap {
            min-height: 100%;
            height: auto !important;
            height: 100%;
            margin: 0 auto -60px;
          }
          #push, #footer { height: 60px; }
          #footer { background-color: #f5f5f5; }
          .container { width: auto; max-width: 680px; }
          .container .credit { margin: 20px 0; }
        </style>
    </head>
    <body>
        <div id="app">
          <div id="wrap">
            <div class="container">
              <div class="page-header">
                <h1>Laravel + Websockets + React.js</h1>
              </div>
              <div class="col-md-6 table-bordered">1</div>
              <div class="col-md-6 table-bordered">1</div>
              <h3> Message </h3>
              <!-- component react -->
              <div id="example"></div>
            </div>
          </div>
        </div>
        <!-- importar component vue -->
        <script src="{{ asset('js/app.js') }}" ></script>
    </body>
</html>