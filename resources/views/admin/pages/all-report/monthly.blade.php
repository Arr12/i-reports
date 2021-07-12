@extends('admin.layouts.app')

@push('before-style')
@endpush
@push('after-style')
<!-- Bootstrap Select Css -->
<link href="/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
<!-- JQuery DataTable Css -->
<link href="/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
@endpush
@push('before-script')
@endpush
@push('after-script')
<!-- Jquery DataTable Plugin Js -->
<script src="/plugins/jquery-datatable/jquery.dataTables.js"></script>
<script src="/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
<script src="/plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

{{-- <script src="/js/pages/tables/jquery-datatable.js"></script> --}}
<script>
let Tabel = function(url){
    $('#FormTabel').html(createSkeleton(1));
    $.ajax( {
        url: url,
        dataType: "json",
        success:function(json) {
            $('#FormTabel').html("<table id='Tabel' class='table table-bordered table-striped table-hover'></table>");
            $('#Tabel').DataTable(json);
            let arr = [];
            let startdate = json.date[0];
            let enddate = json.date[1];
            $("#startdate").html("Start Date : "+startdate);
            $("#enddate").html("End Date : "+enddate);
            for(let i=0;i<json.columns.length;i++){
                let title = json.columns[i].title;
                arr.push("<a class='btn btn-primary waves-effect toggle-vis' data-column='"+i+"'>"+title+"</a>");
            }
            let combine = arr.join();
            let fix = combine.replace(/,/g, '');
            $("#data-column").html(fix);
            let table = $('#Tabel').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: ['copy', 'excel'],
                destroy: true,
                searching: true,
                // order: [[0,'desc']]
            });
            $('a.toggle-vis').on( 'click', function (e) {
                e.preventDefault();

                // Get the column API object
                let column = table.column( $(this).attr('data-column') );

                // Toggle the visibility
                column.visible( ! column.visible() );
            });
        },
    });
}
$(document).ready(function(){
    $('#FormTabel').html(createSkeleton(1));
    let url_dx = "{{route('all-report.monthly.data')}}?r=global&type=ready";
    Tabel(url_dx);
    $(document).on('click', '#setDataDaily', function(){
        $(this).attr('disabled','disabled');
        var url = "{{route('api.setAllTeam.monthly')}}";
        $.ajax({
            url: url,
            success:function(json) {
                $('#setDataDaily').removeAttr('disabled','disabled');
                $("#alert").html(
                `<div class="alert alert-success alert-dismissible" role="alert" id="alert_success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong> Data has been updated!
                </div>`
                );
            },
            error:function(){
                $('#setDataDaily').removeAttr('disabled','disabled');
            }
        });
    });
    $(document).on('click', '#setDataDailyB', function(){
        let a = $('#SDateA').val();
        let b = $('#SDateB').val();
        if(a != '' && b != ''){
            $(this).attr('disabled','disabled');
            let d = a+','+b;
            var url = "{{route('api.setAllTeam.monthly-periode')}}?type=periode&d="+d;
            $.ajax({
                url: url,
                success:function(json) {
                    $('#setDataDailyB').removeAttr('disabled','disabled');
                    $("#alert").html(
                    `<div class="alert alert-success alert-dismissible" role="alert" id="alert_success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> Data has been updated!
                    </div>`
                    );
                },
                error:function(){
                    $('#setDataDailyB').removeAttr('disabled','disabled');
                }
            });
        }else{
            $('#SDateA').focus();
        }
    });
    $('#ShowData').click(function(){
        if($("#SReport").val()!=''&&$('#SMonth').val()!=''){
            $('#FormTabel').html(createSkeleton(1));
            let a = $('#SReport').val();
            let b = 'month';
            let c = $("#SMonth").val();
            let url_dx = "{{route('all-report.monthly.data')}}?r="+a+"&type="+b+"&mon="+c;
            Tabel(url_dx);
        }
    });
    $(document).on('change','#InType',function(){
        let val = $(this).val();
        if(val == 'periode'){
            $("#setDataDaily").hide();
            $("#setDataDailyB").show();
            $("#SDateForm").show();
            $("#SMonthForm").hide();
            $("#ShowData").hide();
            $("#ShowDataB").show();
        }else{
            $("#setDataDaily").show();
            $("#setDataDailyB").hide();
            $("#SDateForm").hide();
            $("#SMonthForm").show();
            $("#ShowData").show();
            $("#ShowDataB").hide();
        }
        $('#ShowDataB').click(function(){
            if($("#SReport").val()!=''&&$('#SDateA').val()!=''&&$('#SDateB').val()!=''){
                $('#FormTabel').html(createSkeleton(1));
                let a = $('#SReport').val();
                let b = 'periode';
                let c = 'Periode,'+$('#SDateA').val()+','+$('#SDateB').val();
                let url_dx = "{{route('all-report.monthly.data')}}?r="+a+"&type="+b+"&mon="+c;
                Tabel(url_dx);
            }
        });
    });
});
</script>
@endpush

@section('content')
<!-- Exportable Table -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Lv.2 Monthly Report</h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <button id='setDataDaily' class="btn waves-effect btn-success" role="button" aria-haspopup="true" aria-expanded="false">
                            <i style="color:#fff;" class="material-icons">save</i> Export This Month Report
                        </button>
                        <button style="display:none;" id='setDataDailyB' class="btn waves-effect btn-primary" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">save</i> Export Periode Report
                        </button>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div id="alert"></div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <select class="form-control show-tick" id="SReport" name="report">
                            <option value="">Select Reports</option>
                            <option value="global">Global</option>
                            <option value="indo">Indo</option>
                            <option value="spam">Spam</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <select class="form-control show-tick" id="InType">
                            <option value="">Select Input Type</option>
                            <option value="month">Month</option>
                            <option value="periode">Periode</option>
                        </select>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="SDateForm" style="display:none;">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input type="date" id="SDateA" class="form-control" />
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <input type="date" id="SDateB" class="form-control" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="SMonthForm" style="display:none;">
                        <input type="month" id="SMonth" class="form-control" />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button class="btn btn-primary btn-block waves-effect" id="ShowData">
                            <i class="material-icons">done</i> Show Data
                        </button>
                        <button class="btn btn-primary btn-block waves-effect" id="ShowDataB" style="display:none;">
                            <i class="material-icons">done</i> Show Data Periode
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p id="startdate"></p>
                        <p id="enddate"></p>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabel"></div>
            </div>
        </div>
    </div>
</div>
<!-- #END# Exportable Table -->
@endsection
