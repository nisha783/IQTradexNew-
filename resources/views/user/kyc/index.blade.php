@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    @if (auth()->user()->kyc && auth()->user()->kyc->status === 'approved')
                        <h4 class="mt-4">KYC Status {{ auth()->user()->kyc->status }}</h4>
                        <h1 class="display-1"><i class="bx bx-check-circle"></i></h1>
                    @elseif (auth()->user()->kyc && auth()->user()->kyc->status === 'rejected')
                        <h4 class="mt-4">KYC Status {{ auth()->user()->kyc->status }}</h4>
                        <h1 class="display-1"><i class="bx bx-x-circle"></i></h1>
                    @elseif (auth()->user()->kyc && auth()->user()->kyc->status === 'pending')
                        <h4 class="mt-4">KYC Status {{ auth()->user()->kyc->status }}</h4>
                        <h1 class="display-1"><i class="bx bx-time-five"></i></h1>
                    @else
                        <h4>Please Submit Your KYC</h4>
                        <div class="card-body">
                            <form method="POST" action="{{ route('user.kyc.store') }}" enctype="multipart/form-data">
                                @csrf

                                <!-- Legal Name -->
                                <div class="form-group mb-4">
                                    <label for="name" class="form-label">Legal Name *</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" required>
                                </div>

                                <!-- Document Type -->
                                <div class="form-group mb-4">
                                    <label for="document_type" class="form-label">Document Type *</label>
                                    <select name="document_type" id="document_type" class="form-select">
                                        <option value="passport">Passport</option>
                                        <option value="driving_license">Driving License</option>
                                        <option value="identity_card">Identity Card</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Document Number -->
                                <div class="form-group mb-4">
                                    <label for="document_number" class="form-label">Document Number *</label>
                                    <input type="text" class="form-control" id="document_number" name="document_number"
                                        value="{{ old('document_number') }}" required>
                                </div>

                                <!-- Address -->
                                <div class="form-group mb-4">
                                    <label for="address" class="form-label">Address *</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="{{ old('address') }}" required>
                                </div>

                                <!-- Selfie -->
                                <div class="form-group mb-4">
                                    <label for="selfie" class="form-label">Selfie *</label>
                                    <input type="file" class="form-control" id="selfie" name="selfie" required>
                                </div>

                                <!-- Document Front Image -->
                                <div class="form-group mb-4">
                                    <label for="front" class="form-label">Document Front Image *</label>
                                    <input type="file" class="form-control" id="front" name="front" required>
                                </div>

                                <!-- Document Back Image -->
                                <div class="form-group mb-4">
                                    <label for="back" class="form-label">Document Back Image (Optional)</label>
                                    <input type="file" class="form-control" id="back" name="back">
                                    <small>Skip for Passport</small>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Submit for Approval</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
