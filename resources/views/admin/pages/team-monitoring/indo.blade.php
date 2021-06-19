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
                buttons: ['copy'],
                destroy: true,
                searching: true,
                // order: [[0,'desc']]
            });
            $('a.toggle-vis').on( 'click', function (e) {
                e.preventDefault();
                if($(this).hasClass('btn-warning')){
                    $(this).addClass('btn-primary');
                    $(this).removeClass('btn-warning');
                }else{
                    $(this).addClass('btn-warning');
                    $(this).removeClass('btn-primary');
                }
                // Get the column API object
                let column = table.column( $(this).attr('data-column') );

                // Toggle the visibility
                column.visible( ! column.visible() );
            });
        },
    });
}
$(document).ready(function(){
    $(document).on('click','#ShowData',function(){
        $('#FormTabel').html(createSkeleton(1));
        let a = $('#SPerson').val();
        let b = $('#SMonth').val();
        let url_dx = "{{route('team-monitoring.indo.data')}}?mod="+a+"&mon="+b;
        Tabel(url_dx);
    });
    $(document).on('click', '#setDataDaily', function(){
        window.open('https://drive.google.com/drive/u/4/folders/1_HV9_g9bXz2zi1efjstfF5LMF93ePWIH','_blank');
        $(this).attr('disabled','disabled');
        var url = "{{route('api.setTeamMonitoringsetIndo')}}";
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
                $("#alert").html(
                `<div class="alert alert-success alert-dismissible" role="alert" id="alert_success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong> Data has been updated!
                </div>`
                );
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
                <h2>
                    Lv.1 Indo Team Monitoring
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <button id='setDataDaily' class="btn waves-effect btn-success" role="button" aria-haspopup="true" aria-expanded="false">
                            <i style="color:#fff;" class="material-icons">save</i> Export
                        </button>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="row clearfix custom-row">
                    <div id="alert"></div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <select class="form-control show-tick" id="SPerson" name="person">
                            <option value="">Select person</option>
                            @foreach ($person as $key => $ditems)
                                <option value="{{$ditems}}">{{$ditems}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <input type="month" id="SMonth" class="form-control" />
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
