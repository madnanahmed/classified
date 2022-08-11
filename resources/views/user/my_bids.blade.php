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
                        <h2 class="title-2"><i class="icon-docs"></i> My Bids </h2>
                        <div class="table-responsive">
                            <table id="load_datatable" class="table table-striped table-bordered add-manage-table table demo footable-loaded footable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Location</th>
                                    <th>Price</th>
                                    <th>Bid Price</th>
                                    <th>Dated</th>
                                    <th width="80">Status</th>
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

    <script>

        $(document).ready(function(){

            $('#load_datatable').DataTable({
                "pageLength":25,
                "order": [[0, 'desc']],
                processing: true,
                serverSide: true,
                "initComplete": function (settings, json) {
                },
                ajax: "{!! url('get-my-bids') !!}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'ads.title'},
                    {data: 'category_title', name: 'categories.name'},
                    {data: 'country_code', name: 'ads.country_code'},
                    {data: 'price', name: 'ads.price'},
                    {data: 'amount', name: 'user_bids.amount'},
                    {data: 'created_at', name: 'user_bids.created_at'},
                    {data: 'status', name: 'status', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endsection

