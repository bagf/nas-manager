<!DOCTYPE html>
<html>
    <head>
        <title>Laravel File Search</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3" style="margin-top: 5em">
                    <ul class="list-group">
                        <li class="list-group-item">
                            @unless(!$useCpuTemp)
                            <span class="badge">{{ $cpuTemp }}</span>
                            CPU
                            @endunless
                        </li>
                    </ul>
                </div>
                <div class="col-lg-1">&nbsp;</div>
                <div class="col-lg-7" style="margin-top: 1em">
                    <input type="text" class="form-control input-lg" style="width:100%";
                </div>
            </div>
        </div>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </body>
</html>
