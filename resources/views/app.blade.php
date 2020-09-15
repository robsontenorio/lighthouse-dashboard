<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Lighthouse Dashboard</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>               
     <link rel="shortcut icon" href="{{ asset('/vendor/lighthouse-dashboard/images/favicon.png') }}" />
    <link href="{{ mix('css/app.css', '/vendor/lighthouse-dashboard') }}" rel="stylesheet" />    
    <script src="{{ mix('js/app.js', '/vendor/lighthouse-dashboard') }}" defer></script>
</head>
<body>    
    @inertia
</body>
</html>