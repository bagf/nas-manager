<!DOCTYPE html>
<html>
    <head>
        <title>Laravel File Search</title>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-7 col-lg-offset-4" style="margin-top: 1em;margin-bottom: 1em;">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="js-category">All</span> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a class="js-change-category" href="#"><span class="js-category-text">All</span></a></li>
                                <li role="separator" class="divider"></li>
                                @foreach($categories as $category)
                                <li>
                                    <a class="js-change-category" href="#"><span class="js-category-text">{{ $category->name }}</span>&nbsp;<span class="badge">{{ $category->items()->count() }}</span></a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <input type="text" class="form-control input-lg js-search-term" style="width:100%" autofocus="true">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3">
                    <ul class="list-group">
                        @unless(!$useCpuTemp)
                        <li class="list-group-item">
                            <span class="badge">{{ $cpuTemp }}</span>
                            <i class="glyphicon glyphicon-tasks"></i> CPU
                        </li>
                        @endunless
                        @foreach ($hddUsage as $stat)
                        <li class="list-group-item">
                            <span class="badge">{{ $stat['avail'] }}/{{ $stat['total'] }}</span>
                            <i class="glyphicon glyphicon-hdd"></i> {{ $stat['source'] }}
                            <div class="progress" style="margin-top: 1em">
                                <div class="progress-bar" role="progressbar" aria-valuenow="{{ str_replace('%', '', $stat['pcent']) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $stat['pcent'] }};">
                                    {{ $stat['pcent'] }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-9">
                    <table class="table table-hover js-search-table" style="display:none;font-size: 17px">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>Path</th>
                                <th>Size</th>
                            </tr>
                        </thead>
                        <tbody class="js-search-results">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Latest compiled and minified JavaScript -->
        <script src="js/jquery-2.1.4.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
