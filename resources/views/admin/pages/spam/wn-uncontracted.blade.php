@extends('admin.layouts.app')
@php
    $person = "wn-uncontracted";
    $data_person = route('report-spam.spam-wn-uncontracted.data');
    $update_value = route('update-daily.edit-value');
    $update_follow = route('update-daily.add-value');
    $get_person_data = route('api.dailyReport.get');
@endphp

@push('before-style')
@endpush
@push('after-style')
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
let TabelGlobalDaily = function(url){
    $('#FormTabelGlobalDaily').html(createSkeleton(1));
    $.ajax( {
        url: url,
        dataType: "json",
        success:function(json) {
            $('#FormTabelGlobalDaily').html("<table id='TabelGlobalDaily' class='table table-bordered table-striped table-hover'></table>");
            $('#TabelGlobalDaily').DataTable(json);
            let arr = [];
            for(let i=0;i<json.columns.length;i++){
                let title = json.columns[i].title;
                arr.push("<a class='btn btn-primary waves-effect toggle-vis' data-column='"+i+"'>"+title+"</a>");
            }
            let combine = arr.join();
            let fix = combine.replace(/,/g, '');
            $("#data-column").html(fix);
            /* ------------------------------
            / DATATABLES SEARCH BY COLUMN
            ------------------------------ */
            let table = $('#TabelGlobalDaily').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: ['copy', 'excel'],
                destroy: true,
                searching: true,
                order: [[0,'desc']]
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
    let url = "{{ $data_person }}";
    TabelGlobalDaily(url);
    $(document).on('click','#getDataDaily',function(){
        $(this).attr('disabled','disabled');
        $('#FormTabelGlobalDaily').html(createSkeleton(1));
        let url_dx = $(this).attr('data-href');
        $.ajax({
            url: url_dx,
            success:function(json) {
                $('#getDataDaily').removeAttr('disabled','disabled');
                if(json == "200"){
                    $("#alert_success").show();
                    $("#alert_danger").hide();
                }else if(json == "400"){
                    $("#alert_success").hide();
                    $("#alert_danger").show();
                }else{
                    $("#alert_success").show();
                    $("#alert_danger").hide();
                }
                TabelGlobalDaily(url);
            }
        });
    });

    $(document).on('click', '#BtnModalEdit', function(){
        $("#idModalEdit").html($(this).attr('data-id'));
        $("#date").val($(this).attr('data-date'));
        $("#reasons").val($(this).attr('data-reasons'));
        $("#editor").val($(this).attr('data-editor'));
        $("#cbid").val($(this).attr('data-cbid'));
        $("#book_title").val($(this).attr('data-book_title'));
        $("#author_name").val($(this).attr('data-author_name'));
        $("#discord_contact").val($(this).attr('data-discord_contact'));
        $("#other_contact_way").val($(this).attr('data-other_contact_way'));
        $("#genre").val($(this).attr('data-genre'));
        $("#total_chapter").val($(this).attr('data-total_chapter'));
        $("#chapter_within_7_days").val($(this).attr('data-chapter_within_7_days'));
        $("#collection").val($(this).attr('data-collection'));
        $("#status_ongoing").val($(this).attr('data-status_ongoing'));
        $("#FL_ML").val($(this).attr('data-FL_ML'));
        $("#feedback_from_author").val($(this).attr('data-feedback_from_author'));
        $("#note").val($(this).attr('data-note'));
    });
    $('#BtnSaveEditModal').on('click', function(){
        $(this).attr('disabled','disabled');
        let url_follow = "{{ $update_value }}";
        let id = $("#idModalEdit").html();
        $.ajax({
            type : 'PUT',
            url  : url_follow,
            data : {
                "_token" : $('meta[name="csrf-token"]').attr('content'),
                "id" : id,
                "p" : "{{ $person }}",
                "date" : $("#date").val(),
                "reasons" : $("#reasons").val(),
                "editor" : $("#editor").val(),
                "cbid" : $("#cbid").val(),
                "book_title" : $("#book_title").val(),
                "author_name" : $("#author_name").val(),
                "discord_contact" : $("#discord_contact").val(),
                "other_contact_way" : $("#other_contact_way").val(),
                "genre" : $("#genre").val(),
                "total_chapter" : $("#total_chapter").val(),
                "chapter_within_7_days" : $("#chapter_within_7_days").val(),
                "collection" : $("#collection").val(),
                "status_ongoing" : $("#status_ongoing").val(),
                "FL_ML" : $("#FL_ML").val(),
                "feedback_from_author" : $("#feedback_from_author").val(),
                "note" : $("#note").val(),
            },
            success : function(x){
                $('#BtnSaveEditModal').removeAttr('disabled','disabled');
                $('#editModal').modal('hide');
                TabelGlobalDaily(url);
            }
        });
    });

    $(document).on('click', '#BtnModalFollow', function(){
        let id = $(this).attr('data-id');
        $("#idModalFollow").html(id);
    });
    $('#BtnSaveFollowModal').on('click', function(){
        $(this).attr('disabled','disabled');
        let id = $('#idModalFollow').html();
        let row = $('#select_row').val();
        let date = $("row_date").val();
        let url_follow = "{{ $update_follow }}";
        $.ajax({
            type: 'PATCH',
            url : url_follow,
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "id" : id,
                "row" : row,
                "date" : date,
                "p" : "{{ $person }}"
            },
            success : function(x){
                $('#BtnSaveFollowModal').removeAttr('disabled','disabled');
                $("#select_row option").prop("selected", false).trigger( "change" );
                $('#followModal').modal('hide');
                TabelGlobalDaily(url);
            }
        });
    });
    $('#BtnSearchData').on('click', function(){
        $('#BtnSaveFollowModal').removeAttr('disabled','disabled');
        let search_author = $('#search_author_contact').val();
        let url = "{{ $data_person }}?where="+search_author;
        TabelGlobalDaily(url);
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
                    Spam WN Uncontracted
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <button id='getDataDaily' class="btn btn-primary" data-href="{{ $get_person_data }}?d={{$person}}" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">file_download</i> Import
                        </button>
                    </li>
                </ul>
            </div>
            <div class="body">
                <div class="alert alert-success alert-dismissible" role="alert" id="alert_success" style="display: none;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Success!</strong> Data has been updated!
                </div>
                <div class="alert alert-danger alert-dismissible" role="alert" id="alert_danger" style="display: none;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Oh snap!</strong> Can't get data, check your internet connection or contact the creator!.
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" class="form-control" id="search_author_contact" placeholder="Input Author Contact for Advanced Search.." />
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
                <div class="table-responsive" id="FormTabelGlobalDaily"></div>
            </div>
        </div>
    </div>
</div>
<!-- #END# Exportable Table -->


<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Update ID <span id="idModalEdit"></span></h4>
            </div>
            <div class="modal-body">
                @php
                    $array_input = [
                        "id" => [
                            "date",
                            "reasons",
                            "editor",
                            "cbid",
                            "book_title",
                            "author_name",
                            "discord_contact",
                            "other_contact_way",
                            "genre",
                            "total_chapter",
                            "chapter_within_7_days",
                            "collection",
                            "status_ongoing",
                            "FL_ML",
                            "feedback_from_author",
                            "note"
                        ],
                        "label" => [
                            "Date Invitation Sent",
                            "Reasons",
                            "Editor",
                            "CBID",
                            "Book Title",
                            "Author Name",
                            "Discord Contact",
                            "Other Contact Way",
                            "Genre",
                            "Total Chapter",
                            "Chapter within 7 days",
                            "Collection",
                            "Status Ongoing",
                            "FL/ML",
                            "Feedback From Author",
                            "Note",
                        ],
                    ];
                @endphp
                <div class="row">
                    <div class="col-sm-12">
                        @foreach ($array_input["id"] as $key => $value)
                        <div class="form-group form-float">
                            <div class="form-line">
                                    @if($value == 'date')
                                    <input type="date" class="form-control" id="{{$value}}" />
                                    @else
                                    <input type="text" class="form-control" id="{{$value}}" />
                                    @endif
                                    <label class="form-label">{{$array_input['label'][$key]}}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="BtnSaveEditModal" class="btn btn-link waves-effect">SAVE CHANGES</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@php
    $arr_values = [
        "value" => [
            "date_feedback_received",
        ],
        "text" => [
            "Date Feedback Received",
        ]
    ];
@endphp
<div class="modal fade" id="followModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Add Date for ID <span id="idModalFollow"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <select class="form-control show-tick" id="select_row">
                                    <option value="">-- Select row do you want add to report --</option>
                                    @foreach ($arr_values['value'] as $key => $value)
                                        <option value="{{ $value }}">{{$arr_values['text'][$key]}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="date" class="form-control" id="row_date" value="{{date('Y-m-d')}}" />
                                <label class="form-label">Date</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="BtnSaveFollowModal" class="btn btn-link waves-effect">ADD</button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endsection
