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
                        <h2 class="title-2 pull-left"><i class="icon-docs"></i> My Ads </h2>
                        <button class="btn btn-success pull-right m-b-5" data-toggle="tooltip" data-title="Refresh data" onclick="$('#load_datatable').DataTable().ajax.reload();"><i class="fa fa-refresh"></i></button>
                        <div class="clearfix"></div>
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
                                    <th>For Auction</th>
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

    <!-- Modal -->
    <div class="modal fade" id="set_auction_modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h3 class="modal-title text-center"><span class="_title text-info"></span> Set for auction</h3></center>
                </div>
                <div class="modal-body">
                    <div class="p-l-r-10">
                        <form action="" id="auction_form">

                            <input type="hidden" id="list_id" name="id">
                            <input type="hidden" id="auction_id" name="auction_id">
                            <div class="form-group">
                                <label for="auction_price">Min price</label>
                                <input id="auction_price" type="number" class="form-control" name="price" required>
                            </div>

                            <div class="form-group">
                                <label for="auction_price">Add Active Hour</label>
                                <input id="active_hour" type="number" class="form-control" name="hour" required>
                            </div>
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" checked name="now">
                                    Time start From Now
                                </label>
                            </div>

                            <div class="form-group">
                                <label> Description / Instructions </label>
                                <textarea name="description" id="" cols="30" rows="5" class="form-control _description"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="is_active">
                                    <input id="is_active" type="checkbox" name="status">
                                    Is Active For Auction
                                </label>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success"> <i class="icon-login"></i> Save </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        function set_auction(x){

           var id =  $(x).attr('data-id');
           var title =  $(x).attr('data-t');
           var price =  $(x).attr('data-price');

           $.post("{{route('check-auction')}}",
               {id:id},
               function (res) {
               if(res.success){
                   $('#auction_price').val(res.data.price);
                   $('#list_id').val(res.data.ad_id);
                   $('#auction_id').val(res.data.id);
                   $('._title').text(title);
                   $('._description').text(res.data.description);
                   $('#active_hour').val(res.data.hour);

                   if(res.data.status == 0){
                       $('#is_active').prop('checked', false);
                   }else{
                       $('#is_active').prop('checked', true);
                   }


               }else{
                   $('#auction_price').val(price);
                   $('#list_id').val(id);
                   $('._title').text(title);
               }
           });

            $('#set_auction_modal').modal('show');
        }

        $(document).ready(function(){
            // ajax submit form
            $("#auction_form").submit(function(){
                $('#loading').show();
                var data = new FormData(this);

                $.ajax({
                    url: "{{route('store-auction')}}",
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(res){

                        if(res.success == true){
                            $('#set_auction_modal').modal('hide');
                            swal("Success!", res.message, "success");
                            $('#load_datatable').DataTable().ajax.reload();
                        }
                        else{

                            swal("Error!", "Something went wrong.", "error");
                        }
                        $('#loading').hide();
                    }
                });
                return false;
            });

            $('#load_datatable').DataTable({
                "pageLength":25,
                "order": [[0, 'desc']],
                processing: true,
                serverSide: true,
                "initComplete": function (settings, json) {
                },
                ajax: "{!! url('load-my-ads') !!}?load_ads=all",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'ads.title'},
                    {data: 'category_title', name: 'categories.name'},
                    {data: 'country_code', name: 'ads.country_code'},
                    {data: 'city_title', name: 'city.title'},
                    {data: 'price', name: 'ads.price'},
                    {data: 'auction', name: 'auction', orderable: false, searchable: false},
                    {data: 'created_at', name: 'ads.created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                    //{ data: 'updated_at', name: 'updated_at' }
                ]
            });
        });
    </script>
@endsection
