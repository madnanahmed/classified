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
                <div class="section">
                    <div class="inner-box">
                        <h2 class="title-2"><i class="icon-docs"></i> My Saved Ads </h2>

                        <div class="table-responsive">
                            <table id="load_datatable" class="table table-striped table-bordered add-manage-table table demo footable-loaded footable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Country</th>
                                    <th>City</th>
                                    <th>Price</th>
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
        </div>
    </section>
    <script>
        $(document).ready(function(){
            $('#load_datatable').DataTable({
                "pageLength":25,
                "order": [[0, 'desc']],
                processing: true,
                serverSide: true,
                "initComplete": function (settings, json) {
                },
                ajax: "{!! url('load-save-ads') !!}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'ads.title'},
                    {data: 'category_title', name: 'categories.name'},
                    {data: 'country_code', name: 'ads.country_code'},
                    {data: 'city_title', name: 'city.title'},
                    {data: 'price', name: 'ads.price'},
                    {data: 'created_at', name: 'ads.created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                    //{ data: 'updated_at', name: 'updated_at' }
                ]
            });
        });

        // delete saved ads
        function del_saved(id){
            var action = 'del';
            //
            swal({
                title: "Are you sure?",
                text: "Un save ad",
                type: "error",
                showCancelButton: true,
                cancelButtonClass: 'btn-default btn-md waves-effect',
                confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                confirmButtonText: 'Confirm!'
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $("#loading").show();
                        $.get('{{ url('save-add') }}', {id:id, action:action}, function(data){
                            if(data!=''){
                                refreshTable();
                            }
                            $("#loading").hide();
                        });
                    }
                }
            );
        }
    </script>
@endsection