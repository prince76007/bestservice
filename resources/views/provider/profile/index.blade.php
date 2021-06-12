@extends('provider.layout.app')

@section('content')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="#" class="pro-head-link active">Profile</a>
        <a href="{{ route('provider.documents.index') }}" class="pro-head-link">Manage Documents</a>
        <a href="{{ route('provider.location.index') }}" class="pro-head-link">Update Location</a>
    </div>
</div>


<!-- Pro-dashboard-content -->
<div class="pro-dashboard-content gray-bg">
    <div class="profile">
        <!-- Profile head -->
        
        <div class="container">
            <div class="profile-head white-bg row no-margin">
                <div class="prof-head-left col-lg-2 col-md-2 col-sm-3 col-xs-12">
                    <div class="new-pro-img bg-img" style="background-image: url({{ img(Auth::guard('provider')->user()->avatar) }});"></div>
                </div> 

                <div class="prof-head-right col-lg-10 col-md-10 col-sm-9 col-xs-12"">
                    <h3 class="prof-name">{{ Auth::guard('provider')->user()->first_name }} {{ Auth::guard('provider')->user()->last_name }}</h3>
                    <p class="board-badge">{{ strtoupper(Auth::guard('provider')->user()->status) }}</p>
                </div>
            </div>
        </div>


        <!-- Profile-content -->
        <div class="profile-content gray-bg pad50">
            <div class="container">
                <div class="row no-margin">
        @include('common.notify')

                    <div class="col-lg-7 col-md-7 col-sm-8 col-xs-12 no-padding">
                        <form class="profile-form" action="{{route('provider.profile.update')}}" method="POST" enctype="multipart/form-data" role="form">
                            {{csrf_field()}}
                            <!-- Prof-form-sub-sec -->
                            <div class="prof-form-sub-sec">
                                <div class="row no-margin">
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" placeholder="Contact Number" name="first_name" value="{{ Auth::guard('provider')->user()->first_name }}">
                                        </div>
                                    </div>
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-right-padding">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control" placeholder="Contact Number" name="last_name" value="{{ Auth::guard('provider')->user()->last_name }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="prof-sub-col prof-1 col-xs-12">
                                        <div class="form-group">
                                            <label>Avatar</label>
                                            <input type="file" class="form-control" name="avatar">
                                        </div>
                                    </div>
                                </div>

                                <div class="row no-margin">
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-left-padding">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" placeholder="Contact Number" name="mobile" value="{{ Auth::guard('provider')->user()->mobile }}">
                                        </div>
                                    </div>
                                    <div class="prof-sub-col col-sm-6 col-xs-12 no-right-padding">
                                        <div class="form-group no-margin">
                                            <label for="exampleSelect1">Language</label>
                                            <select class="form-control" name="language">
                                                <option value="English">English</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of prof-sub-sec -->

                            <!-- Prof-form-sub-sec -->
                            <div class="prof-form-sub-sec border-top">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" class="form-control" placeholder="Enter Address" name="address" value="{{ Auth::guard('provider')->user()->profile ? Auth::guard('provider')->user()->profile->address : "" }}">
                                    
                                </div>

                                

                                <div class="form-group">
                                    <label>Service Providing : </label>
                                    @foreach($Provider as $service)
                                        @if($service->service_type)
                                            <p>{{$service->service_type->name}}</p>
                                        @endif
                                    @endforeach
                                </div>

                                
                            </div>
                            <!-- End of prof-sub-sec -->

                            <!-- Prof-form-sub-sec -->
                            <div class="prof-form-sub-sec border-top">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" class="form-control" placeholder="Enter Description" name="description" value="{{ Auth::guard('provider')->user()->description ? Auth::guard('provider')->user()->description : "" }}">
                                </div>

                                
                            </div>
                            <!-- End of prof-sub-sec -->

                            <!-- Prof-form-sub-sec -->
                            <div class="prof-form-sub-sec border-top">
                                <div class="col-xs-12 col-md-6 col-md-offset-3">
                                    <button type="submit" class="btn btn-block btn-primary update-link">Update</button>
                                </div>
                            </div>
                            <!-- End of prof-sub-sec -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection