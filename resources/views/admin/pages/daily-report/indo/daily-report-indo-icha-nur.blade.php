@extends('admin.layouts.app')
@php
    $person = "icha-nur";
    $data_person = route('daily-report-indo.icha-nurs.data');
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
                $("#alert_success").show();
                $("#alert_danger").hide();
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
        let contact_way = $(this).attr('data-contact_way');
        let author_contact = $(this).attr('data-author_contact');
        let platform = $(this).attr('data-platform');
        let status = $(this).attr('data-status');
        let inquiries = $(this).attr('data-inquiries');
        let new_cbid = $(this).attr('data-new_cbid');
        let old_cbid = $(this).attr('data-old_cbid');
        let author = $(this).attr('data-author');
        let title = $(this).attr('data-title');
        let genre = $(this).attr('data-genre');
        let k4 = $(this).attr('data-k4');
        let plot = $(this).attr('data-plot');
        let maintain_account = $(this).attr('data-maintain_account');
        let old_new_book = $(this).attr('data-old_new_book');

        $("#idModalEdit").html(id);
        $("#date").val(date);
        $("#contact_way").val(contact_way);
        $("#author_contact").val(author_contact);
        $("#platform").val(platform).change();
        $("#status").val(status);
        $("#inquiries").val(inquiries).change();
        $("#new_cbid").val(new_cbid);
        $("#old_cbid").val(old_cbid);
        $("#author").val(author);
        $("#title").val(title);
        $("#genre").val(genre);
        $("#k4").val(k4);
        $("#plot").val(plot);
        $("#maintain_account").val(maintain_account);
        $("#old_new_book").val(old_new_book);
    });
    $('#BtnSaveEditModal').on('click', function(){
        $(this).attr('disabled','disabled');
        let url_follow = "{{ $update_value }}";
        let id = $("#idModalEdit").html();
        let date = $("#date").val();
        let contact_way = $("#contact_way").val();
        let author_contact = $("#author_contact").val();
        let platform = $("#platform").val();
        let status = $("#status").val();
        let inquiries = $("#inquiries").val();
        let new_cbid = $("#new_cbid").val();
        let old_cbid = $("#old_cbid").val();
        let author = $("#author").val();
        let title = $("#title").val();
        let genre = $("#genre").val();
        let k4 = $("#k4").val();
        let plot = $("#plot").val();
        let maintain_account = $("#maintain_account").val();
        let old_new_book = $("#old_new_book").val();
        $.ajax({
            type : 'PUT',
            url  : url_follow,
            data : {
                "_token" : $('meta[name="csrf-token"]').attr('content'),
                "id" : id,
                "p" : "{{ $person }}",
                "date" : date,
                "contact_way" : contact_way,
                "author_contact" : author_contact,
                "platform" : platform,
                "status" : status,
                "inquiries" : inquiries,
                "new_cbid" : new_cbid,
                "old_cbid" : old_cbid,
                "author" : author,
                "title" : title,
                "genre" : genre,
                "k4" : k4,
                "plot" : plot,
                "maintain_account" : maintain_account,
                "old_new_book" : old_new_book
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
        let marker = $("#select_marker").val();
        let url = "{{ $data_person }}?where="+search_author+"&marker="+marker;
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
                    Daily Report {{ str_replace("-"," ",ucfirst($person)) }}
                </h2>
                <ul class="header-dropdown m-r--5">
                    <li class="dropdown">
                        <button id='getDataDaily' class="btn btn-primary" data-href="{{ $get_person_data }}?d=indo-ichanur" role="button" aria-haspopup="true" aria-expanded="false">
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
                        <div class="form-group">
                            <div class="form-line">
                                <select class="form-control show-tick" id="select_marker">
                                    <option value="">Select Marker</option>
                                    @for($i=0; $i<8; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
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
                            <label>Date</label>
                            <div class="form-line">
                                <input type="date" class="form-control" id="date" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Contact Way</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="contact_way" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Author Contact</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="author_contact" />
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
                            <label>Status</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="status" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <select class="form-control show-tick" id="inquiries">
                                <option value="">-- Select Inquiries --</option>
                                @foreach ($selects['inquiries'] as $key => $value)
                                    <option value="{{ $value->name }}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group form-float">
                            <label>New CBID</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="new_cbid" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Old CBID</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="old_cbid" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Author</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="author" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Title</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="title" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Genre</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="genre" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>4K + ?</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="k4" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Plot</label>
                            <div class="form-line">
                                <textarea id="plot" rows="4" class="form-control no-resize"></textarea>
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Maintain Account</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="maintain_account" />
                            </div>
                        </div>
                        <div class="form-group form-float">
                            <label>Old/New Book</label>
                            <div class="form-line">
                                <input type="text" class="form-control" id="old_new_book" />
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
            "fu",
            "data_sent",
        ],
        "text" => [
            "Follow Up",
            "Data Sent",
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
                            <label>Date</label>
                            <div class="form-line">
                                <input type="date" class="form-control" id="row_date" value="{{date('Y-m-d')}}" />
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
