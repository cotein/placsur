
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{config('app.name')}} - Usuario no autenticado</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
    <meta name="description" content="Elephant is an admin template that helps you build modern Admin Applications, professionally fast! Built on top of Bootstrap, it includes a large collection of HTML, CSS and JS components that are simple to use and easy to customize.">
    <meta property="og:url" content="http://demo.madebytilde.com/elephant">
    <meta property="og:type" content="website">
    <meta property="og:title" content="The fastest way to build Modern Admin APPS for any platform, browser, or device.">
    <meta property="og:description" content="Elephant is an admin template that helps you build modern Admin Applications, professionally fast! Built on top of Bootstrap, it includes a large collection of HTML, CSS and JS components that are simple to use and easy to customize.">
    <meta property="og:image" content="http://demo.madebytilde.com/elephant.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@madebytilde">
    <meta name="twitter:creator" content="@madebytilde">
    <meta name="twitter:title" content="The fastest way to build Modern Admin APPS for any platform, browser, or device.">
    <meta name="twitter:description" content="Elephant is an admin template that helps you build modern Admin Applications, professionally fast! Built on top of Bootstrap, it includes a large collection of HTML, CSS and JS components that are simple to use and easy to customize.">
    <meta name="twitter:image" content="http://demo.madebytilde.com/elephant.jpg">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="manifest.json">
    <link rel="mask-icon" href="safari-pinned-tab.svg" color="#1c90fb">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,500,700">
    <link rel="stylesheet" href="/css/app/vendor.min.css">
    <link rel="stylesheet" href="/css/app/elephant.min.css">
    <link rel="stylesheet" href="/css/app/errors.min.css">
  </head>
  <body>
    <div class="error">
      <div class="error-body">
        <h1 class="error-heading">Usuario no autenticado</h1>
        <h4 class="error-subheading">Para acceder al sistema debe esta autenticado en el mismo.</h4>
        <p>
          <small>Haga click en el botón y luego ingrese sus credenciales.</small>
        </p>
        <p><a class="btn btn-primary btn-pill btn-thick" href="/login">Ingresar al sistema</a></p>
      </div>
      <div class="error-footer">
        {{--  <ul class="list-inline">
          <li>
            <a class="link-muted" href="#">
              <span class="icon icon-twitter icon-2x"></span>
            </a>
          </li>
          <li>
            <a class="link-muted" href="#">
              <span class="icon icon-facebook-official icon-2x"></span>
            </a>
          </li>
          <li>
            <a class="link-muted" href="#">
              <span class="icon icon-linkedin icon-2x"></span>
            </a>
          </li>
        </ul>  --}}
        <p>
          <small>Diego Barrueta</small>
        </p>
      </div>
    </div>
    <script src="/js/vendor.min.js"></script>
    <script src="/js/elephant.min.js"></script>
    <script src="/js/app.js"></script>

  </body>
</html>