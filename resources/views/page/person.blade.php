@extends('layouts.app')

@push('before-style')
@endpush
@push('after-style')
<!-- JQuery DataTable Css -->
<link href="../../plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
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
let TabelIndoTeam = function(url){
    $.ajax( {
        url: url,
        dataType: "json",
        success:function(json) {
            $('#FormTabelIndoTeam').html("<table id='TabelIndoTeam' class='table table-bordered table-striped table-hover'></table>");
            $('#TabelIndoTeam').DataTable(json);
            let arr = [];
            for(let i=0;i<json.columns.length;i++){
                let title = json.columns[i].title;
                arr.push("<a class='btn btn-primary waves-effect toggle-vis' data-column='"+i+"'>"+title+"</a>");
            }
            let combine = arr.join();
            let fix = combine.replace(/,/g, '');
            $("#data-column").html(fix);
            let table = $('#TabelIndoTeam').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: ['copy', 'excel'],
                destroy: true,
                searching: true,
            });
            $('a.toggle-vis').on( 'click', function (e) {
                e.preventDefault();

                // Get the column API object
                let column = table.column( $(this).attr('data-column') );

                // Toggle the visibility
                column.visible( ! column.visible() );
            } );
        },
    });
}
$(document).ready(function(){
    let url = "{{route('data.person')}}";
    TabelIndoTeam(url);
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
                    INDO TEAM
                </h2>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        Hide column:
                        <div id="data-column"></div>
                    </div>
                </div>
                <div class="table-responsive" id="FormTabelIndoTeam"></div>
            </div>
        </div>
    </div>
</div>
<!-- #END# Exportable Table -->
@endsection
