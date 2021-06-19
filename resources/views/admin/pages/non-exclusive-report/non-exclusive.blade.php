@extends('admin.layouts.app')
@php
    $person = "non-exclusive";
    $data_person = route('non-exclusive-report.non-exclusive.data');
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
                buttons: ['copy'],
                destroy: true,
                searching: true,
                order: [[0,'desc']]
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
        let id = $(this).attr('data-id');
        let date = $(this).attr('data-date');
        let global_editor = $(this).attr('data-global_editor');
        let author_contact = $(this).attr('data-author_contact');
        let platform = $(this).attr('data-platform');
        let username = $(this).attr('data-username');
        let title = $(this).attr('data-title');
        let book_status = $(this).attr('data-book_status');
        let latest_update = $(this).attr('data-latest_update');
        let book_id = $(this).attr('data-book_id');
        let sent_e_contract = $(this).attr('data-sent_e_contract');
        let officer = $(this).attr('data-officer');
        let date_sent = $(this).attr('data-date_sent');
        let and_notes = $(this).attr('data-and_notes');
        let global_editor_notes = $(this).attr('data-global_editor_notes');
        let pdf_evidence = $(this).attr('data-pdf_evidence');
        let and_evidence = $(this).attr('data-and_evidence');
        let global_evidence = $(this).attr('data-global_evidence');

        $("#idModalEdit").html(id);
        $("#date").val(date);
        $("#global_editor").val(global_editor);
        $("#author_contact").val(author_contact);
        $("#platform").val(platform).change();
        $("#username").val(username);
        $("#title").val(title);
        $("#book_status").val(book_status);
        $("#latest_update").val(latest_update);
        $("#book_id").val(book_id);
        $("#sent_e_contract").val(sent_e_contract);
        $("#officer").val(officer);
        $("#date_sent").val(date_sent);
        $("#and_notes").val(and_notes);
        $("#global_editor_notes").val(global_editor_notes);
        $("#pdf_evidence").val(pdf_evidence);
        $("#and_evidence").val(and_evidence);
        $("#global_evidence").val(global_evidence);
    });
    $('#BtnSaveEditModal').on('click', function(){
        $(this).attr('disabled','disabled');
        let url_follow = "{{ $update_value }}";
        let id = $("#idModalEdit").html();
        let date = $("#date").val();
        let global_editor = $("#global_editor").val();
        let author_contact = $("#author_contact").val();
        let platform = $("#platform").val();
        let username = $("#username").val();
        let title = $("#title").val();
        let book_status = $("#book_status").val();
        let latest_update = $("#latest_update").val();
        let book_id = $("#book_id").val();
        let sent_e_contract = $("#sent_e_contract").val();
        let officer = $("#officer").val();
        let date_sent = $("#date_sent").val();
        let and_notes = $("#and_notes").val();
        let global_editor_notes = $("#global_editor_notes").val();
        let pdf_evidence = $("#pdf_evidence").val();
        let and_evidence = $("#and_evidence").val();
        let global_evidence = $("#global_evidence").val();
        $.ajax({
            type : 'PUT',
            url  : url_follow,
            data : {
                "_token" : $('meta[name="csrf-token"]').attr('content'),
                "id" : id,
                "p" : "{{ $person }}",
                "date" : date,
                "global_editor" : global_editor,
                "author_contact" : author_contact,
                "platform" : platform,
                "username" : username,
                "title" : title,
                "book_status" : book_status,
                "latest_update" : latest_update,
                "book_id" : book_id,
                "sent_e_contract" : sent_e_contract,
                "officer" : officer,
                "date_sent" : date_sent,
                "and_notes" : and_notes,
                "global_editor_notes" : global_editor_notes,
                "pdf_evidence" : pdf_evidence,
                "and_evidence" : and_evidence,
                "global_evidence" : global_evidence,
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
        let date = $("#row_date").val();
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
                    Non Exclusive Report
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
                <div class="row custom-row">
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="date" class="form-control" id="date" />
                                <label class="form-label">Date</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="global_editor" />
                                <label class="form-label">Global Editor</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="author_contact" />
                                <label class="form-label">Author Contact</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <select class="form-control show-tick" id="platform">
                                <option value="">-- Select Platform --</option>
                                @foreach ($selects['platform'] as $key => $value)
                                    <option value="{{ $value->name }}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="username" />
                                <label class="form-label">Username</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="title" />
                                <label class="form-label">Title</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="book_status" />
                                <label class="form-label">Book Status</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="latest_update" />
                                <label class="form-label">Latest Update</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="book_id" />
                                <label class="form-label">Book ID</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="sent_e_contract" />
                                <label class="form-label">Sent E Contract</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="officer" />
                                <label class="form-label">Officer</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="date" class="form-control" id="date_sent" />
                                <label class="form-label">Date Sent</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="and_notes" />
                                <label class="form-label">AND Notes</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="global_editor_notes" />
                                <label class="form-label">Global Editor Notes</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="pdf_evidence" />
                                <label class="form-label">PDF Evidence</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="and_evidence" />
                                <label class="form-label">AND Evidence</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="global_evidence" />
                                <label class="form-label">Global Evidence</label>
                            </div>
                        </div>
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
            "first_touch",
            "sent_e_contract",
            "data_sent",
            "solved_date",
            "rec_e_contract",
            "fu",
            "email_sent",
            "batch_date",
        ],
        "text" => [
            "First Touch",
            "Sent E Contract",
            "Data Sent",
            "Solved Date",
            "Rec E Contract",
            "Follow Up",
            "Email Sent",
            "Batch Date",
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
