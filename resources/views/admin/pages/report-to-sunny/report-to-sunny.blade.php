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
let TabelAll = function(url){
    $('#FormTabelAll').html(createSkeleton(1));
    $.ajax( {
        url: url,
        dataType: "json",
        success:function(json) {
            $('#FormTabelAll').html("<table id='TabelAll' class='table table-bordered table-striped table-hover'></table>");
            $('#TabelAll').DataTable(json);
            let arr = [];
            for(let i=0;i<json.columns.length;i++){
                let title = json.columns[i].title;
                arr.push("<a class='btn btn-primary waves-effect toggle-vis' data-column-tabel-all='"+i+"'>"+title+"</a>");
            }
            let combine = arr.join();
            let fix = combine.replace(/,/g, '');
            $("#data-column-tabel-all").html(fix);
            let table = $('#TabelAll').DataTable({
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
                let column = table.column( $(this).attr('data-column-tabel-all') );

                // Toggle the visibility
                column.visible( ! column.visible() );
            });
        },
    });
}
let Tabel = function(url){
    $('#FormTabel').html(createSkeleton(1));
    $.ajax( {
        url: url,
        dataType: "json",
        success:function(json) {
            $('#FormTabel').html("<table id='Tabel' class='table table-bordered table-striped table-hover'></table>");
            $('#Tabel').DataTable(json);
            let arr = [];
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
    $('#FormTabelAll').html(createSkeleton(1));
    let url_dx = "{{route('report-to-sunny.report-to-sunny.data')}}?c=all";
    TabelAll(url_dx);
    $(document).on('change','#SMonth',function(){
        $.ajax({
            url:"{{route('all-report.date-weekly-friday')}}?m="+$(this).val(),
            type:'GET',
            success:function(json) {
                $("#SWeekForm").html("<select class='form-control show-tick' id='SWeek' name='week'><option value=''>Select Weeks</option>"+json.option+"</select>");
                $(document).on('click','#ShowData',function(){
                    if($("#SWeekForm").val()!=null&&$("#SReport").val()!=null){
                        $('#FormTabel').html(createSkeleton(1));
                        let a = $('#SReport').val();
                        let b = $('#SMonth').val();
                        let c = $('#SWeek').val();
                        let url_dx = "{{route('report-to-sunny.report-to-sunny.data')}}?r="+a+"&mon="+b+"&w="+c;
                        Tabel(url_dx);
                    }
                });
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
                <h2>Report To Sunny All Data</h2>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column-tabel-all"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabelAll"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Report To Sunny Weekly</h2>
            </div>
            <div class="body">
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <select class="form-control show-tick" id="SReport" name="person">
                            <option value="">Select Reports</option>
                            <option value="global">Global</option>
                            <option value="spam">Spam</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <input type="month" id="SMonth" class="form-control" />
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="SWeekForm">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <button class="btn btn-primary btn-block waves-effect" id="ShowData">
                            <i class="material-icons">done</i> Show Data
                        </button>
                    </div>
                </div>
                <div class="row">
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
