@extends('layouts.app')
@section('content')
    <?php $setting = \App\Setting::first();   ?>
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
        .useradmin img:last-child{
            height:30px;
            margin-top: -11px;
        }
        .vfy_phone_number{
            border: 2px solid #02c102 !important;
        }
    </style>
    <?php
    // check user is company
    $avator = asset('assets/images/users/male.png');
    if($user->image ==''){
        if($user->type == 'c' ){
         $avator = asset('assets/images/users/company.png');
        }
        if($user->type == 'u' ){
            if($user->gender == 'm'){
                $avator = asset('assets/images/users/male.png');
            }
            if($user->gender == 'f'){
                $avator = asset('assets/images/users/female.png');
            }
        }
    }else{
        $avator = asset('assets/images/users/'.$user->image.'');
    }
    ?>
    <section id="main" class="clearfix myads-page">
        <div class="container">
            <div class="row">
                @include('user.sidebar')
                <div class="section">
                    @if (session('success'))
                        <div class="inner-box">
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="inner-box">
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif
                        <div class="inner-box">
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-xxs-12">
                                    <div id="container"></div>
                                </div>
                            </div>
                        </div>
                    <div class="inner-box">
                        <div class="clearfix"></div>
                        <div id="accordion" class="panel-group">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><a href="#identicollapse" data-toggle="collapse"> User Identification </a>
                                    </h4>
                                </div>
                                <div class="panel-collapse collapse" id="identicollapse">
                                    <div class="panel-body">
                                        @if($user->is_verified == 0 || $user->is_verified == 3)
                                            @if($user->is_verified == 3)
                                                <div class="alert alert-danger"><strong><i class="fa fa-exclamation-triangle" area-hidden="true"></i> Provided documents for verification are not verified! </strong>
                                                    <p>Still you can submit verification documents.</p>
                                                </div>
                                            @endif
                                            <div id="card-container">
                                                <h3> <i class="fa fa-exclamation-triangle" area-hidden="true"></i>
                                                    Follow following instruction to be come verified user ! </h3>
                                                <p>For identity You can send both the identity card, driving license or the passport. </p>
                                                <p>While as proof of residence can be send, telephone bill, luca bill, water or gas or bank statement or residence certificate from the municipality.<p>
                                                <p>Both documents must have the same name and same address, otherwise the verification will not be validated.</p>
                                                <button onclick="$('#upId').click();"   class="btn btn-danger m-b-10"><i class="fa fa-id-card " aria-hidden="true"></i> upload file</button>
                                                <a href="javascript:void(0)" data-toggle="modal" data-target="#showExp"><small>Check card example.</small></a>
                                                <form id="myIdForm" action="" enctype="multipart/form-data">
                                                    <input type="file" id="upId" class="hidden" name="image[]" multiple onchange=" $('#myIdForm').submit(); " accept="image/*" >
                                                </form>
                                            </div>
                                        @endif
                                        @if($user->is_verified == 1)
                                            <h3 class="text-success"> Your data is under verification process.</h3> <p>We will inform you via email notification!</p>
                                        @endif
                                        @if($user->is_verified == 2)
                                            <p class="alert alert-success"> <strong><i class="fa fa-check" area-hidden="true"></i> Verified! </strong> Now you are ticked as verified user! </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><a href="#collapseB1" data-toggle="collapse">{{ ($user->type == 'c')? 'Showroom basic information': 'My details' }} </a></h4>
                                </div>
                                <div class="panel-collapse collapse" id="collapseB1">
                                    <div class="panel-body">
                                        <form class="form-horizontal" id="profile_setting" role="form">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">{{ ($user->type == 'c')? 'Showroom Logo': 'Profile Image' }}*</label>
                                                <div class="col-sm-6">
                                                    <button type="button" id="browse_profile" class="btn btn-success"> <i class="fa fa-folder-open" aria-hidden="true"></i> Browse</button>
                                                    <input type="file" name="image" class="form-control file_hidden hidden">
                                                </div>
                                                <div class="col-md-3">
                                                    <a href="#">
                                                        <img id="preview_profile" class="userImg" src="{{ $avator }}" alt="user">
                                                    </a>
                                                </div>
                                            </div>
                                            @if($user->type == 'c')
                                                <div class="form-group"><label
                                                            class="col-sm-3 control-label">Showroom Banner*</label>
                                                    <div class="col-sm-6">
                                                        <button type="button" id="browse_banner" class="btn btn-success"><i class="fa fa-folder-open" aria-hidden="true"></i> Browse</button>
                                                        <input type="file" name="banner"
                                                               class="form-control file_banner_hidden hidden"></div>
                                                    <div class="col-md-3"><a
                                                                href="#">
                                                            @if($user->banner!='')
                                                                <img id="preview_banner" class="userImg"
                                                                     src="{{ asset('assets/images/banner/'.$user->banner)  }}"
                                                                     alt="banner">
                                                            @else
                                                                <img id="preview_banner" class="userImg"
                                                                     src="{{ asset('assets/images/banner/empty.png') }}"
                                                                     alt="banner">
                                                            @endif
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Name*</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" placeholder="User name" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email*</label>
                                                <div class="col-sm-9">
                                                    <input id="seller-email" class="form-control" disabled value="{{ $user->email }}" placeholder="example@domain.com" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Phone*</label>
                                                <div class="col-sm-9">
                                                    <input  type="text" name="phone" @if(Auth::user()->mobile_verify ==1)  title="Verified number" @endif class="{{ Auth::user()->mobile_verify ==1 ? 'vfy_phone_number' : '' }} form-control" value="{{ $user->phone }}" minlength="10" maxlength="15" id="user_phone" placeholder="880 124 9820" required>
                                                    <small class="phone_number_only">Only phone number allowed!</small>
                                                </div>
                                            </div>
                                            <div class="form-group  hidden send_code_btn">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-9 bg-primary" style="padding: 10px">
                                                    <p> To change your number, press bellow button.</p>
                                                    <button id="send_code" class="btn btn-success" type="button">Send Code <i class="fa fa-spin fa-spinner hidden"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-group clearfix m-b-10 hidden mobile_code">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-9">
                                                    <p> <i class="fa fa-phone"></i> Enter Four digits code below, send to your phone.</p>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <input id="code" name="phone_code" placeholder="Enter code here" type="text" class="form-control valid" aria-invalid="false">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <i class="fa fa-spin fa-spinner v_code_loader hidden"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--<div class="form-group">
                                                <label class="col-sm-3 control-label">Region</label>
                                                <div class="col-sm-9">
                                                    <select id="region" name="region_id" class="form-control" >
                                                        <option value="" selected>Select Region</option>
                                                        @foreach($region as $value)
                                                            <option {{ ($user->region_id == $value->id)? 'selected' : '' }}  value="{{$value->id}}"> {{ $value->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>-->
                                            @if($user->type == 'c')
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Telephone*</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="telephone" value="{{ $user->telephone }}" class="form-control" placeholder="street address" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">VAT</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="vat" value="{{ $user->vat }}" class="form-control" placeholder="VAT number">
                                                </div>
                                            </div>
                                           <div class="form-group">
                                                <label class="col-sm-3 control-label">Fax</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="fax" value="{{ $user->fax }}" class="form-control" placeholder="street address" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Address*</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="address" value="{{ $user->address }}" class="form-control" placeholder="street address" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Zip*</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="zip" class="form-control" value="{{ $user->zip }}" placeholder="38000" required>
                                                </div>
                                            </div>
                                                <div class="form-group"><label class="col-sm-3 control-label"></label>
                                                    <div class="col-sm-9">
                                                        <label for="req"> <input type="checkbox" name="preview" id="req" value="2"> Activate company request </label>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-9"></div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-9">
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><a href="#collapseB2" data-toggle="collapse"> Change password </a>
                                    </h4>
                                </div>
                                <div class="panel-collapse collapse" id="collapseB2">
                                    <div class="panel-body">
                                        <form class="form-horizontal" id="changePassword" autocomplete="off">
                                           <div class="form-group">
                                                <label class="col-sm-3 control-label">Old Password*</label>
                                                <div class="col-sm-9">
                                                    <input type="password" name="old" class="form-control" placeholder="Enter old password" required autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">New Password*</label>
                                                <div class="col-sm-9">
                                                    <input type="password" name="new" class="form-control" placeholder="Enter new password" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Confirm Password*</label>
                                                <div class="col-sm-9">
                                                    <input type="password" name="cpass" class="form-control" placeholder="Confirm password" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-9">
                                                    <button type="submit" class="btn btn-success">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Button trigger modal -->
    <!-- Modal -->
    <div class="modal fade" id="showExp" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modelTitleId">Identification document example </h4>
                </div>
                <div lass="modal-body">
                    <div class="col-md-6">
                        <h4 class="m-t-10">Id card</h4>
                        <img src="{{ asset('assets/images/card_example.jpg') }}" alt=""  class="img-responsive">
                    </div>
                    <div class="col-md-6">
                        <h4 class="m-t-10"> Telephone bill </h4>
                        <img src="{{ asset('assets/images/bill_example.jpg') }}" alt="" class="img-responsive">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script>
        // high chart
        Highcharts.chart('container', {
            chart: {
                height: 250,
            },
            title: {
                text: 'Profile & ads views in the last 12 months',
                align: 'center'
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    month: '%b',
                    year: '%Y'
                }
            },
            exporting: {
                enabled: false
            },
            credits: {
                enabled: false
            },
            series: [{
                data: [{{$ads_view}}],
                name: 'Ads views',
                color: '#1B53B7',
                pointStart: Date.UTC({{date("Y")}},0),
                pointIntervalUnit: 'month'
            },
                {
                    data: [{{$profile_view}}],
                    name: 'Profile views',
                    color: '#f9423a',
                    pointStart: Date.UTC({{date("Y")}},0),
                    pointIntervalUnit: 'month'
                }
            ],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 1000,
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });
        $(document).ready(function() {
            /* user phone verify */
        @if($setting->mobile_verify == 1)
            var veryfy = true;
            $('.steps ul li').click(function () {
                if($('#steps-uid-0-t-2 span:first-child').hasClass('current-info')){
                    if(veryfy === true){
                        $('.actions ul li:nth-child(2)').hide();
                    }
                }
            });
            $('#user_phone').on('keyup blur', function(){
                var vl =  $('#user_phone').val();
                if (/^[+()\d]+$/.test(vl)) {
                    /**/
                    $('.phone_number_only').addClass('text-success').removeClass('text-danger');
                } else {
                    $('.phone_number_only').addClass('text-danger').removeClass('text-success');
                    return false;
                }
            });
            /* send code to user mobile*/
            $('#send_code').click(function(){
                var phone = $('#user_phone').val();
                var email = $('#seller-email').val();
                if(email ==''){
                    swal('Warning', 'Email field is required', 'warning');
                    $('#seller-email').focus();
                    return false;
                }
                $(this).find('.fa-spinner').removeClass('hidden');
                $.post('{{ route('phone-verify-code') }}',
                    {email:email, phone:phone, _token:'{{csrf_token()}}'},
                    function (result) {
                        if(result.msg==1){
                            $('#user_phone').val(result.number);
                            $('.mobile_code').removeClass('hidden');
                            $('#send_code, .send_code_btn').addClass('hidden');
                        }else if( result.msg == 2 ){
                            swal('Error', result.error, 'error');
                            $('#user_phone').focus();
                        }
                        $('#send_code').find('.fa-spinner').addClass('hidden');
                    });
            });
            $('#code').keyup(function () {
                var vl =  $('#code').val();
                if(vl.length ==4) {
                    var phone = $('#user_phone').val();
                    var email = $('#seller-email').val();
                    $('.v_code_loader').removeClass('hidden');
                    $.post('{{ route('verify-code') }}',
                        { code:vl, email: email, phone: phone, _token: '{{csrf_token()}}'},
                        function (result) {
                            if (result.msg == 1) {
                                $('#user_phone').attr('style', 'border: 2px solid #02c102 !important');
                                $('.mobile_code').html('<center class="text-success"> Phone number verified successfully! </center>');
                                $('.actions ul li:nth-child(2)').show();
                                veryfy = false;
                            } else if (result.msg == 2) {
                                swal('Error', result.error, 'error');
                            }
                            $('.v_code_loader').addClass('hidden');
                        });
                }
            });
            @endif
            // upload id card
            $('#myIdForm').submit(function(){
                $('#loading').show();
                var data = new FormData(this);
                $.ajax({
                    url: "<?php  echo url('user-id-card'); ?>",
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(result){
                        if(result.msg == 1){
                            // success
                            $('#card-container').html( '<h3 class="text-success"> Your data is under verification process.</h3> <p>We will inform you via email notification!</p>');
                            swal("Sucess!", "Your file is uploaded successfully!", "success");
                            $('#loading').hide();
                        }else if(result.msg == 2){
                            swal("Error!", result.error, "error");
                        }
                        $('#loading').hide();
                    }
                });
                return  false;
            });
            $('#region').on('change', function () {
                $('#load_city, #load_comune').html('');
                var id = $(this).val();
                if(id != 0) {
                    $('#loading').show();
                    $.post('{{url('load-city')}}', {id: id, pro: 'pro'}, function (data) {
                        $('#load_city').html(data);
                        $('#loading').hide();
                    });
                }
            });
            // ajax submit form
            $("#profile_setting").submit(function(){
                $('#loading').show();
                var data = new FormData(this);
                $.ajax({
                    url: "<?php  echo url('user-profile-settings'); ?>",
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(result){
                        if(result.msg == 2){
                            // error
                            swal("Error!", "Something went wrong!", "error");
                            $('#loading').hide();
                        }else if (result.msg == 1) {
                            // success
                            $('#loading').hide();
                            var user_name =  $('#profile_setting input[name="name"]').val();
                            $('.user_name').text(user_name);
                            $('.userImg').attr('src','<?= asset('assets/images/users') ?>/' + result.file_name);
                            // swal("Good job!", "Subject has been saved successfully.", "success")
                            swal({
                                title: "Good job!",
                                text: "Profile has been saved successfully!",
                                type: "success",
                                confirmButtonText: "OK"
                            });
                        }else if(result.msg == 3){
                            $('.send_code_btn').removeClass('hidden');
                            $('#loading').hide();
                        }
                    }
                });
                return false;
            });
            $("#changePassword").submit(function(){
                $('input').removeClass('bg-danger');
                $('#loading').show();
                var data = new FormData(this);
                $.ajax({
                    url: "<?php  echo url('change-password'); ?>",
                    data: data,
                    contentType: false,
                    processData: false,
                    type: 'POST',
                    success: function(result){
                        if(result == '2'){
                            // error
                            $('input[name="old"]').addClass('bg-danger');
                            swal("Error!", "The old password you enter is not correct!", "error");
                        }else if(result == '3'){
                            $('input[name="new"],input[name="cpass"]').addClass('bg-danger');
                            swal("Error!", "Please confirm password!", "error");
                        }else if(result == '1'){
                            // success
                            var user_name =  $('#changePassword input').val('');
                            // swal("Good job!", "Subject has been saved successfully.", "success")
                            swal({
                                title: "Good job!",
                                text: "Password has been changed successfully!",
                                type: "success",
                                confirmButtonText: "OK"
                            });
                        }
                        $('#loading').hide();
                    }
                });
                return false;
            });
            // browse image
            $('#browse_profile').click( function(){
                $('.file_hidden').click();
            });
            $('#browse_banner').click(function () {
                $('.file_banner_hidden').click();
            });

            // profile preview
            function readURL(input, id) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#'+id).attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".file_hidden").change(function() {
                readURL(this, 'preview_profile');
            });

            $(".file_banner_hidden").change(function () {
                readURL(this, 'preview_banner');
            });

        });
    </script>
@endsection