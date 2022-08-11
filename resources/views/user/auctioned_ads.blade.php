@extends('layouts.app')
@section('content')
    <style>
        .main-container{
            padding: 30px 0;
        }
        #preview_profile{
            display: block;
            height: 60px;
            margin-bottom: 10px;
        }
        .userImg{ height: 55px; }
    </style>
    <section id="main" class="clearfix myads-page">
        <div class="container">
            <div class="row">
                @include('user.sidebar')
                    <div class="inner-box section">
                        <h2 class="title-2"><i class="icon-docs"></i> My Auctioned Ads </h2>
                        <div class="table-responsive">
                            <table id="load_datatable" class="table table-striped table-bordered add-manage-table table demo footable-loaded footable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Location</th>
                                    <th>Price</th>
                                    <th>Top Bid</th>
                                    <th>Dated</th>
                                    <th width="80">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="4">No Record found yet.</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <div class="modal fade top-80" id="bid-history-modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title">  Bid History </h3>
                </div>
                <div class="modal-body">
                    <div>
                        <button onclick="refreshTbl()" data-toggle="tooltip" data-title="Refresh data" class="pull-right btn btn-sm m-b-10"><i class="fa fa-refresh"></i></button>
                        <div class="clearfix"></div>
                        <div class="table-responsive">
                            <table id="load_bid_datatable" class="table table-striped table-bordered add-manage-table table demo footable-loaded footable dataTable no-footer">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" class="hidden_id" value="">

    <script>

        function refreshTbl() {
            var id =$('.hidden_id').val();
            bid_history(id)
        }

        function bid_history(id){

            $('.hidden_id').val(id);

           var ur = "{!! url('bidhistory') !!}?id="+id;

            var table = $('#load_bid_datatable').DataTable();
            table.destroy();
            table = $('#load_bid_datatable').DataTable(
                {
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'amount', name: 'amount'},
                        {data: 'time', name: 'time', searchable:false, orderable: false },
                        {data: 'action', name: 'action', searchable:false, orderable: false },
                    ]
                }
            );

            table.ajax.url( ur ).load();

            $('#bid-history-modal').modal('show');
        }

        function accept_bid(id){

            swal({
                    title: "Are you sure?",
                    text: "You are going to accept this bid",
                    type: "error",
                    showCancelButton: true,
                    cancelButtonClass: 'btn-default btn-md waves-effect',
                    confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                    confirmButtonText: 'Confirm!'
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $('#loading').show();
                        $.post('{{route('accept-bid')}}', {id: id}, function (result)
                            {
                                if(result.success == true){

                                    $('#bid-history-modal').hide();

                                    var id =$('.hidden_id').val();
                                    bid_history(id);
                                    $('#load_datatable').DataTable().ajax.reload();
                                    $('#loading').hide();
                                    swal({
                                            title: "Success!",
                                            text: result.msg,
                                            type: "success"
                                        },
                                        function(isConfirm) {
                                            $('#bid-history-modal').show();
                                    });


                                }else{
                                    $('#loading').hide();
                                    swal("Success!", result.msg, "success");
                                }
                            } );

                    } else {
                        swal("Cancelled", "Your action has been cancelled!", "success");
                    }
                }
            );
        }function

        reject_bid(id){

            swal({
                    title: "Are you sure?",
                    text: "You are going to reject this bid",
                    type: "error",
                    showCancelButton: true,
                    cancelButtonClass: 'btn-default btn-md waves-effect',
                    confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                    confirmButtonText: 'Confirm!'
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $('#loading').show();
                        $.post('{{route('reject-bid')}}', {id: id}, function (result)
                            {
                                if(result.success == true){

                                    $('#bid-history-modal').hide();

                                    var id =$('.hidden_id').val();
                                    bid_history(id);
                                    $('#load_datatable').DataTable().ajax.reload();
                                    $('#loading').hide();
                                    swal({
                                            title: "Success!",
                                            text: result.msg,
                                            type: "success"
                                        },
                                        function(isConfirm) {
                                            $('#bid-history-modal').show();
                                    });

                                }else{
                                    $('#loading').hide();
                                    swal("Success!", result.msg, "success");
                                }
                            } );

                    } else {
                        swal("Cancelled", "Your action has been cancelled!", "success");
                    }
                }
            );
        }

        $(document).ready(function(){

            $('#load_datatable').DataTable({
                "pageLength":25,
                "order": [[0, 'desc']],
                processing: true,
                serverSide: true,
                "initComplete": function (settings, json) {
                },
                ajax: "{!! url('load-auction-ads') !!}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'ads.title'},
                    {data: 'category_title', name: 'categories.name'},
                    {data: 'region_title', name: 'region.title'},
                    {data: 'price', name: 'ads.price'},
                    {data: 'top_bid', name: 'top_bid'},
                    {data: 'created_at', name: 'ads.created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                    //{ data: 'updated_at', name: 'updated_at' }
                ]
            });
        });
    </script>
@endsection

