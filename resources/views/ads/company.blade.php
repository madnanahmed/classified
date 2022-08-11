@extends('layouts.app')
@section('content')

    <style>

    </style>

    <div class="section featureds explore-nearby">
        <div class="featured-top">
            <h4>ShowRooms</h4>
        </div>

        @if(count($companies)>0)
            @foreach($companies as $company)
                <div class="col-md-6 col-lg-3">
                    <div class="featured">
                        <div class="featured-image">
                            @if($company->banner =='')
                                <a href="{{ url('showrooms/ads/'.$company->id) }}"><img src="{{ asset('assets/images/banner/empty.png') }}" alt="" class="img-fluid"></a>
                            @else
                                <a href="{{ url('showrooms/ads/'.$company->id) }}"><img src="{{ asset('assets/images/banner/'.$company->banner) }}" alt="" class="img-fluid"></a>
                            @endif
                        </div>

                        <!-- ad-info -->
                        <div class="ad-info">
                            <h4><a href="{{ url('company/ads/'.$company->id) }}">{{ ucwords( $company->name ) }}</a></h4>

                            <address>
                                <p> <i class="fa fa-road"></i> {{ $company->address  }}</p>
                            </address>
                        </div><!-- ad-info -->
                    </div><!-- featured -->
                </div><!-- featured -->
            @endforeach
            @else
            <p class="text-danger">No Showroom found!</p>
        @endif

        </div><!-- row -->

        <!-- pagination  -->
        <div class="text-center">
            <ul class="pagination ">
                {{ $companies->appends(request()->query())->links() }}
            </ul>
        </div><!-- pagination  -->
    </div>

@endsection
