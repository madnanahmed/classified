@extends('admin.layout.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('admin_assets/css/custom.css') }}">

    <style>
        .add_more{float: right; margin-bottom: 5px; }
        .parent ul{
            /*display: none;*/
        }
    </style>

    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="col-xs-12">
                <div class="page-title-box">
                    <h4 class="page-title">{{ ( isset($city))? 'Edit City' : 'Add City' }}</h4>
                    <ol class="breadcrumb p-0 m-0">
                        <li> <a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="active">{{ ( isset($city))? 'Edit City' : 'Add City' }} </li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-xs-12">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="card-box">
                    <div class="row">
                        <div class="col-xs-7 bg-white product-listing">
                            <!--   LIST       -->
                            <!--@if(isset($city))
                                <a href="{{ url('region') }}" class="waves-effect pull-right btn btn-success"><i class="fa fa-plus"></i><span> City</span> </a>
                            @endif -->
                            <form id="add_region" method="post" role="form" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <input type="hidden" name="id" value="{{ ( isset($city) && $city->id != '')? $city->id: '' }}" id="id" >
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">City Title:</label>
                                                <input type="text" name="title" class="form-control" value="{{ ( isset($city) && $city->title != '')? $city->title: '' }}" id="title"  required >
                                            </div>
                                        </div>
                                    </div>

                                    <!--<div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Region:</label>
                                                <select name="region_id" id="region" class="form-control" required>
                                                    <option value=""></option>
                                                    @foreach($region as $value)
                                                        <option {{ ( isset($city) && $city->region_id == $value->id )? 'selected': '' }} value="{{ $value->id }}">{{ $value->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>-->

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-info waves-effect waves-light">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="c_info"></div>
                                </div>
                            </form>

                            <!-- End list  -->
                        </div>
                    </div>
                </div>
                    <div class="card-box">
                        <div class="row">
                            <div class="col-xs-7 bg-white product-listing">
                                <table id="load_datatable" class="table table-colored table-inverse table-hover table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Country</th>
                                        <th>Dated</th>
                                        <th>Status&nbsp;|&nbsp;Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="4">No Record found yet.</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <!-- End list  -->
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){

            $('#load_datatable').DataTable({
                "pageLength":25,
                "order": [[0, 'desc']],
                processing: true,
                serverSide: true,
                "initComplete": function (settings, json) {
                },

                ajax: "{!! Route('load-city') !!}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'country_code', name: 'country_code'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                    //{ data: 'updated_at', name: 'updated_at' }
                ]
            });

            //saving new user
            $("#add_region").submit(function(){
                $('#loading').show();
                var data = new FormData(this);
                $.ajax({
                    url: "<?php echo url('city'); ?>",
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(result){
                        $('#loading').hide();

                        if( result == 1 ){
                            swal("Success!", "Record added successfully.", "success");
                            $('input[name="title"]').val('');
                            refreshTable();
                        }else{
                            swal("Error!", 'Something went wrong!', "error");
                        }
                    }
                });
                return false;
            });
        });
    </script>
@endsection

