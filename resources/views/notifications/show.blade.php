@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Notification Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="notification-details">
                            <h5 class="font-weight-bold mb-3">
                                {{ $notification->data['title'] ?? 'No Title' }}
                            </h5>
                            <p class="mb-3">
                                {{ $notification->data['message'] ?? 'No message available' }}
                            </p>
                            <p class="text-muted">
                                <strong>Created at:</strong> {{ $notification->created_at->toFormattedDateString() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
