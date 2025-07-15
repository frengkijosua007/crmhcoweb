@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">System Settings</h4>
            <p class="text-muted mb-0">Manage application configurations and preferences</p>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Navigation -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="settings-nav list-group list-group-flush">
                        <a href="#generalSettings" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                            <i class="bi bi-gear me-2"></i> General Settings
                        </a>
                        <a href="#companySettings" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="bi bi-building me-2"></i> Company Information
                        </a>
                        <a href="#notificationSettings" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="bi bi-bell me-2"></i> Notification Settings
                        </a>
                        <a href="#userSettings" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="bi bi-people me-2"></i> User & Security Settings
                        </a>
                        <a href="{{ route('settings.system') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-info-circle me-2"></i> System Information
                        </a>
                        <a href="{{ route('settings.backup') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Backup & Restore
                        </a>
                        <a href="{{ route('settings.logs') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-journal-text me-2"></i> System Logs
                        </a>
                    </div>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Setting Tips
                    </h6>
                    <p class="small text-muted mb-0">
                        Changes to system settings may require cache clearing to take effect.
                        Use the clear cache button if you don't see your changes immediately.
                    </p>
                    <hr>
                    <div class="d-grid">
                        <a href="{{ route('settings.clear-cache') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Clear Cache
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Settings Forms -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="generalSettings">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">General Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.update.general') }}" method="POST">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Site Name</label>
                                        <input type="text" name="site_name" class="form-control @error('site_name') is-invalid @enderror"
                                               value="{{ $generalSettings['site_name']->value ?? old('site_name') }}" required>
                                        @error('site_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Default Pagination</label>
                                        <input type="number" name="default_pagination" class="form-control @error('default_pagination') is-invalid @enderror"
                                               value="{{ $generalSettings['default_pagination']->value ?? old('default_pagination') }}" required min="5" max="100">
                                        @error('default_pagination')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Site Description</label>
                                    <textarea name="site_description" class="form-control @error('site_description') is-invalid @enderror" rows="2">{{ $generalSettings['site_description']->value ?? old('site_description') }}</textarea>
                                    @error('site_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Date Format</label>
                                        <select name="date_format" class="form-select @error('date_format') is-invalid @enderror">
                                            <option value="d/m/Y" {{ ($generalSettings['date_format']->value ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2023)</option>
                                            <option value="m/d/Y" {{ ($generalSettings['date_format']->value ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2023)</option>
                                            <option value="Y-m-d" {{ ($generalSettings['date_format']->value ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2023-12-31)</option>
                                            <option value="d-m-Y" {{ ($generalSettings['date_format']->value ?? '') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (31-12-2023)</option>
                                            <option value="d M Y" {{ ($generalSettings['date_format']->value ?? '') == 'd M Y' ? 'selected' : '' }}>DD Mon YYYY (31 Dec 2023)</option>
                                        </select>
                                        @error('date_format')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Time Format</label>
                                        <select name="time_format" class="form-select @error('time_format') is-invalid @enderror">
                                            <option value="H:i" {{ ($generalSettings['time_format']->value ?? '') == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                            <option value="h:i A" {{ ($generalSettings['time_format']->value ?? '') == 'h:i A' ? 'selected' : '' }}>12 Hour (02:30 PM)</option>
                                        </select>
                                        @error('time_format')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Timezone</label>
                                        <select name="timezone" class="form-select @error('timezone') is-invalid @enderror">
                                            <option value="Asia/Jakarta" {{ ($generalSettings['timezone']->value ?? '') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                            <option value="Asia/Makassar" {{ ($generalSettings['timezone']->value ?? '') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                            <option value="Asia/Jayapura" {{ ($generalSettings['timezone']->value ?? '') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                        </select>
                                        @error('timezone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1"
                                           {{ ($generalSettings['maintenance_mode']->value ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_mode">
                                        Enable Maintenance Mode
                                    </label>
                                    <div class="form-text">When enabled, only administrators can access the system.</div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save General Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Company Settings -->
                <div class="tab-pane fade" id="companySettings">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Company Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.update.company') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Company Logo</label>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if(isset($companySettings['company_logo']))
                                                    <img src="{{ asset('storage/' . $companySettings['company_logo']->value) }}"
                                                         alt="Company Logo" class="img-thumbnail" style="max-height: 100px;">
                                                @else
                                                    <div class="border p-3 text-center bg-light">
                                                        <i class="bi bi-building fs-1 text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="file" name="company_logo" class="form-control @error('company_logo') is-invalid @enderror">
                                                <div class="form-text">Recommended size: 200x60px. Max file size: 2MB.</div>
                                                @error('company_logo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Company Name</label>
                                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                               value="{{ $companySettings['company_name']->value ?? old('company_name') }}" required>
                                        @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tax ID</label>
                                        <input type="text" name="company_tax_id" class="form-control @error('company_tax_id') is-invalid @enderror"
                                               value="{{ $companySettings['company_tax_id']->value ?? old('company_tax_id') }}">
                                        @error('company_tax_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Company Address</label>
                                    <textarea name="company_address" class="form-control @error('company_address') is-invalid @enderror" rows="2">{{ $companySettings['company_address']->value ?? old('company_address') }}</textarea>
                                    @error('company_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="company_phone" class="form-control @error('company_phone') is-invalid @enderror"
                                               value="{{ $companySettings['company_phone']->value ?? old('company_phone') }}">
                                        @error('company_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="company_email" class="form-control @error('company_email') is-invalid @enderror"
                                               value="{{ $companySettings['company_email']->value ?? old('company_email') }}">
                                        @error('company_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Website</label>
                                        <input type="url" name="company_website" class="form-control @error('company_website') is-invalid @enderror"
                                               value="{{ $companySettings['company_website']->value ?? old('company_website') }}">
                                        @error('company_website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Currency</label>
                                        <select name="currency" class="form-select @error('currency') is-invalid @enderror">
                                            <option value="IDR" {{ ($companySettings['currency']->value ?? '') == 'IDR' ? 'selected' : '' }}>Indonesian Rupiah (IDR)</option>
                                            <option value="USD" {{ ($companySettings['currency']->value ?? '') == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                        </select>
                                        @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Invoice Prefix</label>
                                        <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror"
                                               value="{{ $companySettings['invoice_prefix']->value ?? old('invoice_prefix') }}">
                                        @error('invoice_prefix')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fiscal Year Start</label>
                                        <select name="fiscal_year_start" class="form-select @error('fiscal_year_start') is-invalid @enderror">
                                            <option value="01-01" {{ ($companySettings['fiscal_year_start']->value ?? '') == '01-01' ? 'selected' : '' }}>January 1</option>
                                            <option value="04-01" {{ ($companySettings['fiscal_year_start']->value ?? '') == '04-01' ? 'selected' : '' }}>April 1</option>
                                            <option value="07-01" {{ ($companySettings['fiscal_year_start']->value ?? '') == '07-01' ? 'selected' : '' }}>July 1</option>
                                            <option value="10-01" {{ ($companySettings['fiscal_year_start']->value ?? '') == '10-01' ? 'selected' : '' }}>October 1</option>
                                        </select>
                                        @error('fiscal_year_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save Company Information
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="tab-pane fade" id="notificationSettings">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Notification Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.update.notification') }}" method="POST">
                                @csrf

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1"
                                           {{ ($notificationSettings['email_notifications']->value ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        Enable Email Notifications
                                    </label>
                                    <div class="form-text">Send email notifications for important events.</div>
                                </div>

                                <div class="mb-3 notification-email-group"
                                     style="{{ ($notificationSettings['email_notifications']->value ?? '') != '1' ? 'display: none;' : '' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Notification Email</label>
                                            <input type="email" name="notification_email" class="form-control @error('notification_email') is-invalid @enderror"
                                                   value="{{ $notificationSettings['notification_email']->value ?? old('notification_email') }}">
                                            <div class="form-text">Email address used to send notifications.</div>
                                            @error('notification_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Sender Name</label>
                                            <input type="text" name="email_sender_name" class="form-control @error('email_sender_name') is-invalid @enderror"
                                                   value="{{ $notificationSettings['email_sender_name']->value ?? old('email_sender_name') }}">
                                            @error('email_sender_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-light mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Notification Types</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="survey_notifications" name="survey_notifications" value="1"
                                                   {{ ($notificationSettings['survey_notifications']->value ?? '') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="survey_notifications">
                                                Survey Notifications
                                            </label>
                                            <div class="form-text">Notifications for survey assignments and completions.</div>
                                        </div>

                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="project_status_notifications" name="project_status_notifications" value="1"
                                                   {{ ($notificationSettings['project_status_notifications']->value ?? '') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="project_status_notifications">
                                                Project Status Notifications
                                            </label>
                                            <div class="form-text">Notifications for project status changes.</div>
                                        </div>

                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="document_upload_notifications" name="document_upload_notifications" value="1"
                                                   {{ ($notificationSettings['document_upload_notifications']->value ?? '') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="document_upload_notifications">
                                                Document Upload Notifications
                                            </label>
                                            <div class="form-text">Notifications when new documents are uploaded.</div>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="client_notifications" name="client_notifications" value="1"
                                                   {{ ($notificationSettings['client_notifications']->value ?? '') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="client_notifications">
                                                Client Notifications
                                            </label>
                                            <div class="form-text">Notifications for new clients and client updates.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save Notification Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- User Settings -->
                <div class="tab-pane fade" id="userSettings">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User & Security Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.update.user') }}" method="POST">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Default Role for New Users</label>
                                        <select name="default_role" class="form-select @error('default_role') is-invalid @enderror">
                                            <option value="user" {{ ($userSettings['default_role']->value ?? '') == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="marketing" {{ ($userSettings['default_role']->value ?? '') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                            <option value="surveyor" {{ ($userSettings['default_role']->value ?? '') == 'surveyor' ? 'selected' : '' }}>Surveyor</option>
                                        </select>
                                        @error('default_role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Max Avatar Size (MB)</label>
                                        <input type="number" name="user_avatar_max_size" class="form-control @error('user_avatar_max_size') is-invalid @enderror"
                                               value="{{ $userSettings['user_avatar_max_size']->value ?? old('user_avatar_max_size') }}" min="1" max="10">
                                        @error('user_avatar_max_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_registration" name="allow_registration" value="1"
                                           {{ ($userSettings['allow_registration']->value ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_registration">
                                        Allow Public Registration
                                    </label>
                                    <div class="form-text">Allow users to register accounts. If disabled, only administrators can create accounts.</div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="account_approval" name="account_approval" value="1"
                                           {{ ($userSettings['account_approval']->value ?? '') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="account_approval">
                                        Require Account Approval
                                    </label>
                                    <div class="form-text">Require administrator approval for new accounts before they can log in.</div>
                                </div>

                                <div class="card bg-light mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Password Policy</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Minimum Password Length</label>
                                            <input type="number" name="password_min_length" class="form-control @error('password_min_length') is-invalid @enderror"
                                                   value="{{ $userSettings['password_min_length']->value ?? old('password_min_length') }}" min="6" max="20">
                                            @error('password_min_length')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="password_requires_letters" name="password_requires_letters" value="1"
                                                   {{ ($userSettings['password_requires_letters']->value ?? '') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="password_requires_letters">
                                                Require Letters
                                            </label>
                                        </div>

                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="password_requires_numbers" name="password_requires_numbers" value="1"
                                                   {{ ($userSettings['password_requires_numbers']->value ?? '') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="password_requires_numbers">
                                                Require Numbers
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="password_requires_symbols" name="password_requires_symbols" value="1"
                                                   {{ ($userSettings['password_requires_symbols']->value ?? '') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="password_requires_symbols">
                                                Require Symbols
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mark Users Inactive After (days)</label>
                                    <input type="number" name="inactive_user_days" class="form-control @error('inactive_user_days') is-invalid @enderror"
                                           value="{{ $userSettings['inactive_user_days']->value ?? old('inactive_user_days') }}" min="30" max="365">
                                    <div class="form-text">Number of days after which users with no login activity are marked as inactive.</div>
                                    @error('inactive_user_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Save User Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.settings-nav .list-group-item {
    border-radius: 0;
    border-left: none;
    border-right: none;
    padding: 0.75rem 1rem;
}

.settings-nav .list-group-item:first-child {
    border-top: none;
}

.settings-nav .list-group-item:last-child {
    border-bottom: none;
}

.settings-nav .list-group-item.active {
    background-color: #f8f9fa;
    color: var(--primary-color);
    border-color: #dee2e6;
    border-left: 3px solid var(--primary-color);
    font-weight: 500;
}

.icon-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide notification email settings based on checkbox
    const emailNotificationsCheckbox = document.getElementById('email_notifications');
    const notificationEmailGroup = document.querySelector('.notification-email-group');

    if (emailNotificationsCheckbox) {
        emailNotificationsCheckbox.addEventListener('change', function() {
            if (this.checked) {
                notificationEmailGroup.style.display = 'block';
            } else {
                notificationEmailGroup.style.display = 'none';
            }
        });
    }

    // Save active tab to localStorage
    const tabLinks = document.querySelectorAll('.settings-nav .list-group-item');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabLinks.forEach(link => {
        link.addEventListener('click', function() {
            localStorage.setItem('activeSettingsTab', this.getAttribute('href'));
        });
    });

    // Restore active tab from localStorage
    const activeTab = localStorage.getItem('activeSettingsTab');
    if (activeTab) {
        const activeLink = document.querySelector(`.settings-nav .list-group-item[href="${activeTab}"]`);
        if (activeLink) {
            tabLinks.forEach(link => {
                link.classList.remove('active');
            });
            tabPanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            activeLink.classList.add('active');
            document.querySelector(activeTab).classList.add('show', 'active');
        }
    }
});
</script>
@endpush
