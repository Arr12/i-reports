@extends('admin.layouts.app')
@php
    $person = "mangatoon";
    $data_person = route('report-spam.spam-mangatoon.data');
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
            },
            error:function(){
                $('#getDataDaily').removeAttr('disabled','disabled');
                $("#alert_success").hide();
                $("#alert_danger").show();
            }
        });
    });

    $(document).on('click', '#BtnModalEdit', function(){
        let id = $(this).attr('data-id');
        let date = $(this).attr('data-date');
        let reasons = $(this).attr('data-reasons');
        let book_name = $(this).attr('data-book_name');
        let author_name = $(this).attr('data-author_name');
        let views = $(this).attr('data-views');
        let likes = $(this).attr('data-likes');
        let ratings = $(this).attr('data-ratings');
        let update_status = $(this).attr('data-update_status');
        let tags = $(this).attr('data-tags');
        let episodes = $(this).attr('data-episodes');
        let link = $(this).attr('data-link');
        let screenshot_from_wave = $(this).attr('data-screenshot_from_wave');
        let author_feedback = $(this).attr('data-author_feedback');
        let comment_from_wave = $(this).attr('data-comment_from_wave');

        $("#idModalEdit").html(id);
        $("#date").val(date);
        $("#reasons").val(reasons);
        $("#book_name").val(book_name);
        $("#author_name").val(author_name);
        $("#views").val(views);
        $("#likes").val(likes);
        $("#ratings").val(ratings);
        $("#update_status").val(update_status);
        $("#tags").val(tags);
        $("#episodes").val(episodes);
        $("#link").val(link);
        $("#screenshot_from_wave").val(screenshot_from_wave);
        $("#author_feedback").val(author_feedback);
        $("#comment_from_wave").val(comment_from_wave);
    });
    $('#BtnSaveEditModal').on('click', function(){
        let url_follow = "{{ $update_value }}";
        let id = $("#idModalEdit").html();
        let date = $("#date").val();
        let reasons = $("#reasons").val();
        let book_name = $("#book_name").val();
        let author_name = $("#author_name").val();
        let views = $("#views").val();
        let likes = $("#likes").val();
        let ratings = $("#ratings").val();
        let update_status = $("#update_status").val();
        let tags = $("#tags").val();
        let episodes = $("#episodes").val();
        let link = $("#link").val();
        let screenshot_from_wave = $("#screenshot_from_wave").val();
        let auhtor_feedback = $("#auhtor_feedback").val();
        let comment_from_wave = $("#comment_from_wave").val();

        $.ajax({
            type : 'PUT',
            url  : url_follow,
            data : {
                "_token" : $('meta[name="csrf-token"]').attr('content'),
                "id" : id,
                "p" : "{{ $person }}",
                "date" : date,
                "reasons" : reasons,
                "book_name" : book_name,
                "author_name" : author_name,
                "views" : views,
                "likes" : likes,
                "ratings" : ratings,
                "update_status" : update_status,
                "tags" : tags,
                "episodes" : episodes,
                "link" : link,
                "screenshot_from_wave" : screenshot_from_wave,
                "auhtor_feedback" : author_feedback,
                "comment_from_wave" : comment_from_wave
            },
            success : function(x){
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
                    Spam Mangatoon Report
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
                                <input type="text" class="form-control" id="search_author_contact" placeholder="Input Author Name for Advanced Search.." />
                            </div>
                        </div>
                        <button type="button" id="BtnSearchData" class="btn btn-block btn-primary waves-effect"><i class="material-icons">search</i> Search Author Name</button>
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
                                <input type="text" class="form-control" id="reasons" />
                                <label class="form-label">Reasons</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="book_name" />
                                <label class="form-label">Book Name</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="author_name" />
                                <label class="form-label">Author Name</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="views" />
                                <label class="form-label">Views</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="likes" />
                                <label class="form-label">Likes</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="ratings" />
                                <label class="form-label">Ratings</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="update_status" />
                                <label class="form-label">Update Status</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="tags" />
                                <label class="form-label">Tags</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="episodes" />
                                <label class="form-label">Episodes</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="link" />
                                <label class="form-label">Link</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="screenshot_from_wave" />
                                <label class="form-label">Screenshot from Wave</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="author_feedback" />
                                <label class="form-label">Author Feedback</label>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <div class="form-line">
                                <input type="text" class="form-control" id="comment_from_wave" />
                                <label class="form-label">Comment from Wave</label>
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
