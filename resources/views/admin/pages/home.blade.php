@extends('admin.layouts.app')

@push('before-style')
@endpush
@push('after-style')
<!-- Morris Chart Css-->
<link href="/plugins/morrisjs/morris.css" rel="stylesheet" />
@endpush
@push('before-script')
@endpush
@push('after-script')
<!-- ChartJs -->
<script src="/plugins/chartjs/Chart.bundle.js"></script>

<!-- Flot Charts Plugin Js -->
<script src="/plugins/flot-charts/jquery.flot.js"></script>
<script src="/plugins/flot-charts/jquery.flot.resize.js"></script>
<script src="/plugins/flot-charts/jquery.flot.pie.js"></script>
<script src="/plugins/flot-charts/jquery.flot.categories.js"></script>
<script src="/plugins/flot-charts/jquery.flot.time.js"></script>

<!-- Sparkline Chart Plugin Js -->
<script src="/plugins/jquery-sparkline/jquery.sparkline.js"></script>
@endpush

@section('content')
<div class="block-header">
    <h2>HOME</h2>
</div>
<!-- Widgets -->
<div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <a href="https://drive.google.com/drive/u/0/folders/1XQ1pLnuAUjCgAGkShZ9eI5CrDVWD2oDU" target="_blank">
            <div class="info-box bg-pink hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">arrow_forward</i>
                </div>
                <div class="content">
                    <div class="text">CLICK HERE !</div>
                    <div class="text">GO TO FOLDER BACKUP</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <a href="https://drive.google.com/drive/u/0/folders/1YNLpKJdrhZiSocb5VGu3TUueqckP4MsM" target="_blank">
            <div class="info-box bg-cyan hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">help</i>
                </div>
                <div class="content">
                    <div class="text">CLICK HERE !</div>
                    <div class="text">GO TO FOLDER REPORT LV 1</div>
                </div>
            </div>
        </a>
    </div>
</div>
<!-- #END# Widgets -->
<div class="row clearfix">
    <!-- Answered Tickets -->
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="body bg-teal">
                <div class="font-bold m-b--35">DAILY IMPORTED DATA OFFICER</div>
                <ul class="dashboard-stat-list">
                    @foreach ($datas['data'] as $key => $data)
                        @if ($data == 0)
                            <li style="color:yellow">
                               ! Today Daily Report {{ucwords($datas['person'][$key])}} Not Imported
                                <span class="pull-right"><b>{{$data}}</b> <small>DATA</small></span>
                            </li>
                        @else
                            <li>
                                Today Daily Report {{ucwords($datas['person'][$key])}} Imported
                                <span class="pull-right"><b>{{$data}}</b> <small>DATA</small></span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <!-- #END# Answered Tickets -->
</div>

{{-- <div class="row clearfix">
    <!-- Task Info -->
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="header">
                <h2>TASK SCHEDULAR</h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="javascript:void(0);">Action</a></li>
                            <li><a href="javascript:void(0);">Another action</a></li>
                            <li><a href="javascript:void(0);">Something else here</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover dashboard-task-infos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Task</th>
                                <th>Interval</th>
                                <th>Description</th>
                                <th>Next Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Task Daily Update</td>
                                <td><span class="label bg-green">Off</span></td>
                                <td>The Task will otomatic update daily report every officer</td>
                                <td>{{date('Y-m-d 01:00:00', strtotime('+1 day'))}}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Task Team Monitoring Global</td>
                                <td><span class="label bg-blue">Active</span></td>
                                <td>John Doe</td>
                                <td>{{date('Y-m-d 01:00:00', strtotime('-1 days +1 weeks'))}}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Task C</td>
                                <td><span class="label bg-light-blue">On Hold</span></td>
                                <td>John Doe</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-light-blue" role="progressbar" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100" style="width: 72%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Task D</td>
                                <td><span class="label bg-orange">Wait Approvel</span></td>
                                <td>John Doe</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-orange" role="progressbar" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100" style="width: 95%"></div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Task E</td>
                                <td>
                                    <span class="label bg-red">Suspended</span>
                                </td>
                                <td>John Doe</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-red" role="progressbar" aria-valuenow="87" aria-valuemin="0" aria-valuemax="100" style="width: 87%"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- #END# Task Info -->
</div> --}}
@endsection
