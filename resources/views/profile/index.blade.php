@extends('layout.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-6">
                <div class="user-profile-header-banner">
                    <img src="{{ asset('assets/img/pages/sakae depan.jpg') }}" alt="Banner image" class="rounded-top">
                </div>
                <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-5">
                    <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}"
                                alt="user image"
                                class="d-block h-auto ms-0 ms-sm-5 rounded-4 user-profile-img">
                        @else
                            <span class="avatar-initial rounded-circle bg-label-success d-block ms-0 ms-sm-5 rounded-4 user-profile-img"
                                style="width: 110px; height: 110px; font-size: 48px; display: flex; align-items: center; justify-content: center;">
                                {{ $user->name[0] }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-grow-1 mt-4 mt-sm-12">
                        <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-6">
                            <div class="user-profile-info">
                                <h4 class="mb-2">{{ $user->name }}</h4>
                                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4">
                                    <li class="list-inline-item">
                                        <i class="icon-base ri ri-user-3-line me-2 icon-24px"></i>
                                        <span class="fw-medium">{{ ucfirst($user->role) }}</span>
                                    </li>
                                    <li class="list-inline-item">
                                        <i class="icon-base ri ri-mail-open-line me-2 icon-24px"></i>
                                        <span class="fw-medium">{{ $user->email }}</span>
                                    </li>
                                    <li class="list-inline-item">
                                        <i class="icon-base ri ri-calendar-line me-2 icon-24px"></i>
                                        <span class="fw-medium">Joined {{ $user->created_at->format('F Y') }}</span>
                                    </li>
                                    <li class="list-inline-item">
                                        <i class="icon-base ri ri-signature-line me-2 icon-24px"></i>
                                        <span class="fw-medium">Signature</span>
                                    </li>
                                </ul>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary waves-effect waves-light">
                                <i class="icon-base ri ri-edit-line icon-16px me-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <!-- User Profile Content -->
    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-5">
            <!-- About User -->
            <div class="card mb-6">
                <div class="card-body">
                    <small class="card-text text-uppercase text-body-secondary small">About</small>
                    <ul class="list-unstyled my-3 py-1">
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base ri ri-user-3-line icon-24px"></i>
                            <span class="fw-medium mx-2">Full Name:</span>
                            <span>{{ $user->name }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base ri ri-check-line icon-24px"></i>
                            <span class="fw-medium mx-2">Status:</span>
                            <span>Active</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base ri ri-star-smile-line icon-24px"></i>
                            <span class="fw-medium mx-2">Role:</span>
                            <span>{{ ucfirst($user->role) }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base ri ri-user-3-line icon-24px"></i>
                            <span class="fw-medium mx-2">Username:</span>
                            <span>{{ $user->username }}</span>
                        </li>
                    </ul>
                    <small class="card-text text-uppercase text-body-secondary small">Contacts</small>
                    <ul class="list-unstyled my-3 py-1">
                        <li class="d-flex align-items-center mb-4">
                            <i class="icon-base ri ri-mail-open-line icon-24px"></i>
                            <span class="fw-medium mx-2">Email:</span>
                            <span>{{ $user->email }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <!--/ About User -->
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7">
            <!-- Activity Timeline -->
            <div class="card card-action mb-6">
                <div class="card-header align-items-center">
                    <h5 class="card-action-title mb-0">
                        <i class="icon-base ri ri-bar-chart-2-line icon-24px text-body me-4"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="text-muted mb-0">Name</p>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0">{{ $user->name }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="text-muted mb-0">Username</p>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0">{{ $user->username }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="text-muted mb-0">Email</p>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="text-muted mb-0">Role</p>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0">{{ ucfirst($user->role) }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <p class="text-muted mb-0">Member Since</p>
                        </div>
                        <div class="col-md-8">
                            <p class="mb-0">{{ $user->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Activity Timeline -->
        </div>
    </div>
    <!--/ User Profile Content -->
</div>


@endsection
