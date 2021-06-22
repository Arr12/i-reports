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
let TabelIndo = function(url){
    $('#FormTabelIndo').html(createSkeleton(1));
    $.ajax( {
        url: url,
        dataType: "json",
        success:function(json) {
            $('#FormTabelIndo').html("<table id='TabelIndo' class='table table-bordered table-striped table-hover'></table>");
            $('#TabelIndo').DataTable(json);
            let arr = [];
            for(let i=0;i<json.columns.length;i++){
                let title = json.columns[i].title;
                arr.push("<a class='btn btn-primary waves-effect toggle-vis2' data-column-indo='"+i+"'>"+title+"</a>");
            }
            let combine = arr.join();
            let fix = combine.replace(/,/g, '');
            $("#data-column-indo").html(fix);
            let table = $('#TabelIndo').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: ['copy'],
                destroy: true,
                searching: true,
                // order: [[0,'desc']]
            });
            $('a.toggle-vis2').on( 'click', function (e) {
                e.preventDefault();
                if($(this).hasClass('btn-warning')){
                    $(this).addClass('btn-primary');
                    $(this).removeClass('btn-warning');
                }else{
                    $(this).addClass('btn-warning');
                    $(this).removeClass('btn-primary');
                }
                // Get the column API object
                let column = table.column( $(this).attr('data-column-indo') );

                // Toggle the visibility
                column.visible( ! column.visible() );
            });
        },
    });
}
$(document).ready(function(){
    let url_dx = "{{route('daily-report-marker.marker.data')}}?d=global";
    Tabel(url_dx);
    let url_dx2 = "{{route('daily-report-marker.marker.data')}}?d=indo";
    TabelIndo(url_dx2);
});
$('#BtnSearchData').on('click',function(){
    let person = $('#select_person').val();
    let url_dx = "{{route('daily-report-marker.marker.data')}}?d=global&p="+person;
    Tabel(url_dx);
});
</script>
@endpush

@section('content')
<!-- Exportable Table -->
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="body">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <a href="#scroll-global" class="btn btn-block btn-primary waves-effect">
                            Scroll to Global
                        </a>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <a href="#scroll-indo" class="btn btn-block btn-primary waves-effect">
                            Scroll to Indo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header" id="scroll-global">
                <h2>
                    Daily Report Person Global With Marker 7
                </h2>
            </div>
            <div class="body">
                <div class="row custom-row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="form-line">
                                <select class="form-control show-tick" id="select_person">
                                    <option value="">Select Person Global</option>
                                    @foreach ($personGlobal as $key => $data)
                                        <option value="{{ $data }}">{{ $data }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="button" id="BtnSearchData" class="btn btn-block btn-primary waves-effect"><i class="material-icons">search</i> Search Author Contact</button>
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
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header" id="scroll-indo">
                <h2>
                    Daily Report Person Indo With Marker 7
                </h2>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p>Hide column:</p>
                        <div id="data-column-indo"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabelIndo"></div>
            </div>
        </div>
    </div>
</div>
<!-- #END# Exportable Table -->
@endsection
