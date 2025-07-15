@extends('layouts.mobile')

@section('title', 'Form Survey')

@section('content')
<div class="survey-mobile-container">
    <!-- Survey Selection if multiple -->
    @if(!$survey && $pendingSurveys->count() > 0)
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">Pilih Survey</h6>
        </div>
        <div class="card-body">
            <div class="list-group">
                @foreach($pendingSurveys as $pendingSurvey)
                <a href="{{ route('surveys.mobile.form', ['survey_id' => $pendingSurvey->id]) }}" 
                   class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $pendingSurvey->project->name }}</h6>
                            <p class="mb-1 text-muted small">{{ $pendingSurvey->project->client->name }}</p>
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                {{ $pendingSurvey->scheduled_date->format('d M Y H:i') }}
                            </small>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @elseif(!$survey)
    <div class="text-center py-5">
        <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
        <h5>Tidak Ada Survey</h5>
        <p class="text-muted">Anda tidak memiliki survey yang dijadwalkan hari ini</p>
        <a href="{{ route('surveys.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
    @else
    <!-- Survey Form -->
    <form id="surveyForm" method="POST" action="{{ route('surveys.submit', $survey) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        
        <!-- Project Info -->
        <div class="card mb-3 border-primary">
            <div class="card-body">
                <h6 class="text-primary mb-1">{{ $survey->project->name }}</h6>
                <p class="mb-1">{{ $survey->project->client->name }}</p>
                <small class="text-muted">
                    <i class="bi bi-geo-alt me-1"></i>{{ $survey->project->location }}
                </small>
            </div>
        </div>

        <!-- Location Info -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-geo-alt text-primary me-2"></i>
                    Informasi Lokasi
                </h6>
                
                <div id="locationInfo" class="alert alert-info">
                    <i class="bi bi-hourglass-split me-2"></i>
                    Mendapatkan lokasi GPS...
                </div>
                
                <div id="map" style="height: 200px; display: none;" class="rounded mb-3"></div>
                
                <div class="mb-3">
                    <label class="form-label">Alamat Lengkap Survey</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" 
                              name="address" 
                              rows="2" 
                              required>{{ old('address') }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Survey Checklist -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-clipboard-check text-primary me-2"></i>
                    Checklist Survey
                </h6>
                
                <!-- Ketersediaan Listrik -->
                <div class="mb-3">
                    <label class="form-label">Ketersediaan Listrik</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="electricity" id="electricity_yes" value="yes" required>
                        <label class="btn btn-outline-success" for="electricity_yes">
                            <i class="bi bi-check-circle me-1"></i> Ada
                        </label>
                        
                        <input type="radio" class="btn-check" name="electricity" id="electricity_no" value="no">
                        <label class="btn btn-outline-danger" for="electricity_no">
                            <i class="bi bi-x-circle me-1"></i> Tidak Ada
                        </label>
                    </div>
                    <input type="text" class="form-control mt-2" name="electricity_notes" placeholder="Catatan (opsional)">
                </div>
                
                <!-- Ketersediaan Air -->
                <div class="mb-3">
                    <label class="form-label">Ketersediaan Air</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="water" id="water_yes" value="yes" required>
                        <label class="btn btn-outline-success" for="water_yes">
                            <i class="bi bi-check-circle me-1"></i> Ada
                        </label>
                        
                        <input type="radio" class="btn-check" name="water" id="water_no" value="no">
                        <label class="btn btn-outline-danger" for="water_no">
                            <i class="bi bi-x-circle me-1"></i> Tidak Ada
                        </label>
                    </div>
                    <input type="text" class="form-control mt-2" name="water_notes" placeholder="Catatan (opsional)">
                </div>
                
                <!-- Akses Jalan -->
                <div class="mb-3">
                    <label class="form-label">Akses Jalan</label>
                    <select class="form-select @error('road_access') is-invalid @enderror" name="road_access" required>
                        <option value="">Pilih kondisi akses</option>
                        <option value="easy">Mudah - Mobil besar bisa masuk</option>
                        <option value="medium">Sedang - Hanya mobil kecil</option>
                        <option value="difficult">Sulit - Hanya motor/jalan kaki</option>
                    </select>
                    @error('road_access')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Status Izin -->
                <div class="mb-3">
                    <label class="form-label">Status Izin</label>
                    <select class="form-select @error('permit_status') is-invalid @enderror" name="permit_status" required>
                        <option value="">Pilih status</option>
                        <option value="complete">Lengkap</option>
                        <option value="process">Dalam Proses</option>
                        <option value="none">Belum Ada</option>
                    </select>
                    @error('permit_status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Kondisi Existing -->
                <div class="mb-3">
                    <label class="form-label">Kondisi Bangunan Existing</label>
                    <select class="form-select @error('existing_condition') is-invalid @enderror" name="existing_condition" required>
                        <option value="">Pilih kondisi</option>
                        <option value="good">Baik</option>
                        <option value="medium">Sedang</option>
                        <option value="bad">Buruk</option>
                        <option value="empty">Tanah Kosong</option>
                    </select>
                    @error('existing_condition')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Luas Area -->
                <div class="mb-3">
                    <label class="form-label">Luas Area (m²)</label>
                    <input type="number" 
                           class="form-control @error('area_size') is-invalid @enderror" 
                           name="area_size" 
                           placeholder="Contoh: 250" 
                           required>
                    @error('area_size')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Photo Upload -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-camera text-primary me-2"></i>
                    Foto Lokasi
                </h6>
                
                <div class="photo-upload-area text-center p-4 border rounded" 
                     onclick="document.getElementById('photoInput').click()">
                    <i class="bi bi-cloud-upload fs-1 text-primary mb-2 d-block"></i>
                    <p class="mb-0">Tap untuk ambil foto atau upload</p>
                    <small class="text-muted">Maksimal 10 foto, masing-masing 10MB</small>
                </div>
                
                <input type="file" 
                       id="photoInput" 
                       name="photos[]" 
                       multiple 
                       accept="image/*" 
                       capture="environment"
                       style="display: none;">
                
                <div id="photoPreview" class="photo-preview mt-3"></div>
            </div>
        </div>
        
        <!-- Notes -->
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    Catatan Tambahan
                </h6>
                <textarea class="form-control" 
                          name="notes" 
                          rows="4" 
                          placeholder="Tulis catatan atau kondisi khusus di lapangan...">{{ old('notes') }}</textarea>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="d-grid gap-2 mb-4">
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                <i class="bi bi-send me-2"></i>
                Kirim Survey
            </button>
            <button type="button" class="btn btn-outline-secondary" id="saveDraftBtn">
                <i class="bi bi-save me-2"></i>
                Simpan Draft
            </button>
        </div>
    </form>
    @endif
</div>

<!-- Offline Indicator -->
<div id="offlineIndicator" class="position-fixed bottom-0 start-0 end-0 bg-warning text-center py-2" style="display: none; z-index: 1050;">
    <i class="bi bi-wifi-off me-2"></i>
    Offline Mode - Data akan dikirim saat online
</div>
@endsection

@push('styles')
<style>
.photo-upload-area {
    border: 2px dashed var(--primary-color);
    cursor: pointer;
    transition: all 0.3s ease;
}

.photo-upload-area:hover {
    background-color: rgba(26, 115, 232, 0.05);
}

.photo-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.5rem;
}

.photo-preview .photo-item {
    position: relative;
}

.photo-preview img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
}

.photo-preview .remove-photo {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    font-size: 12px;
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
let selectedPhotos = [];

// Get GPS Location
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    } else {
        document.getElementById('locationInfo').innerHTML = 
            '<i class="bi bi-exclamation-triangle me-2"></i>GPS tidak didukung browser ini';
    }
}

function showPosition(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    
    // Update location info
    document.getElementById('locationInfo').innerHTML = 
        `<i class="bi bi-check-circle text-success me-2"></i>Lokasi GPS: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    
    // Show map
    document.getElementById('map').style.display = 'block';
    
    // Initialize map
    const map = L.map('map').setView([lat, lng], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    L.marker([lat, lng]).addTo(map)
        .bindPopup('Lokasi Survey')
        .openPopup();
}

function showError(error) {
    let message = '';
    switch(error.code) {
        case error.PERMISSION_DENIED:
            message = "Akses GPS ditolak. Silakan aktifkan GPS.";
            break;
        case error.POSITION_UNAVAILABLE:
            message = "Informasi lokasi tidak tersedia.";
            break;
        case error.TIMEOUT:
            message = "Request timeout. Coba lagi.";
            break;
        case error.UNKNOWN_ERROR:
            message = "Error tidak diketahui.";
            break;
    }
    document.getElementById('locationInfo').innerHTML = 
        `<i class="bi bi-exclamation-triangle me-2"></i>${message}`;
}

// Photo Preview
document.getElementById('photoInput').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const preview = document.getElementById('photoPreview');
    
    files.forEach((file, index) => {
        if (selectedPhotos.length >= 10) {
            Swal.fire('Peringatan', 'Maksimal 10 foto', 'warning');
            return;
        }
        
        selectedPhotos.push(file);
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const photoItem = document.createElement('div');
            photoItem.className = 'photo-item';
            photoItem.innerHTML = `
                <img src="${e.target.result}" alt="Photo ${selectedPhotos.length}">
                <button type="button" class="remove-photo" onclick="removePhoto(${selectedPhotos.length - 1})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            preview.appendChild(photoItem);
        };
        
        reader.readAsDataURL(file);
    });
});

function removePhoto(index) {
    selectedPhotos.splice(index, 1);
    updatePhotoPreview();
}

function updatePhotoPreview() {
    const preview = document.getElementById('photoPreview');
    preview.innerHTML = '';
    
    selectedPhotos.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const photoItem = document.createElement('div');
            photoItem.className = 'photo-item';
            photoItem.innerHTML = `
                <img src="${e.target.result}" alt="Photo ${index + 1}">
                <button type="button" class="remove-photo" onclick="removePhoto(${index})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            preview.appendChild(photoItem);
        };
        reader.readAsDataURL(file);
    });
}

// Offline Detection
window.addEventListener('online', function() {
    document.getElementById('offlineIndicator').style.display = 'none';
    // Sync offline data
    syncOfflineData();
});

window.addEventListener('offline', function() {
    document.getElementById('offlineIndicator').style.display = 'block';
});

// Form Submit with Photos
document.getElementById('surveyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!navigator.onLine) {
        saveOfflineData();
        Swal.fire({
            icon: 'info',
            title: 'Offline Mode',
            text: 'Data survey disimpan offline dan akan dikirim saat online kembali.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Create FormData with selected photos
    const formData = new FormData(this);
    
    // Remove default photos input and add selected photos
    formData.delete('photos[]');
    selectedPhotos.forEach((photo, index) => {
        formData.append('photos[]', photo);
    });
    
    // Show loading
    Swal.fire({
        title: 'Mengirim Survey',
        text: 'Mohon tunggu...',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit form
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Survey berhasil dikirim',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = data.redirect || '{{ route("surveys.index") }}';
            });
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Terjadi kesalahan saat mengirim survey'
        });
    });
});

// Save offline data
function saveOfflineData() {
    const formData = {
        survey_id: {{ $survey->id ?? 'null' }},
        latitude: document.getElementById('latitude').value,
        longitude: document.getElementById('longitude').value,
        address: document.querySelector('[name="address"]').value,
        electricity: document.querySelector('[name="electricity"]:checked')?.value,
        electricity_notes: document.querySelector('[name="electricity_notes"]').value,
        water: document.querySelector('[name="water"]:checked')?.value,
        water_notes: document.querySelector('[name="water_notes"]').value,
        road_access: document.querySelector('[name="road_access"]').value,
        permit_status: document.querySelector('[name="permit_status"]').value,
        existing_condition: document.querySelector('[name="existing_condition"]').value,
        area_size: document.querySelector('[name="area_size"]').value,
        notes: document.querySelector('[name="notes"]').value,
        photos: [] // Handle photos separately
    };
    
    // Save to localStorage
    const offlineData = JSON.parse(localStorage.getItem('offlineSurveys') || '[]');
    offlineData.push({
        ...formData,
        timestamp: new Date().toISOString()
    });
    localStorage.setItem('offlineSurveys', JSON.stringify(offlineData));
}

// Sync offline data when online
function syncOfflineData() {
    const offlineData = JSON.parse(localStorage.getItem('offlineSurveys') || '[]');
    
    if (offlineData.length > 0) {
        // TODO: Implement sync logic
        console.log('Syncing offline data:', offlineData);
    }
}

// Initialize
getLocation();

// Check online status
if (!navigator.onLine) {
    document.getElementById('offlineIndicator').style.display = 'block';
}
</script>
@endpush