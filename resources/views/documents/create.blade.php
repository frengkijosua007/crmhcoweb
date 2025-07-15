@extends('layouts.app')

@section('title', 'Upload Dokumen')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="mb-4">
        <h4 class="mb-0">Upload Dokumen</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Dokumen</a></li>
                <li class="breadcrumb-item active">Upload</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf
        
        <div class="row">
            <div class="col-md-8">
                <!-- Upload Area -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="upload-area text-center p-5 border-2 border-dashed rounded" 
                             id="uploadArea">
                            <i class="bi bi-cloud-upload fs-1 text-primary mb-3 d-block"></i>
                            <h5>Drag & Drop file di sini</h5>
                            <p class="text-muted mb-3">atau</p>
                            <label for="fileInput" class="btn btn-primary">
                                <i class="bi bi-folder2-open me-2"></i>Browse File
                            </label>
                            <input type="file" 
                                   id="fileInput" 
                                   name="file" 
                                   class="d-none @error('file') is-invalid @enderror"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                            <p class="text-muted mt-3 mb-0">
                                <small>Format: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX, PPT, PPTX (Max: 50MB)</small>
                            </p>
                            @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- File Preview -->
                        <div id="filePreview" class="mt-4" style="display: none;">
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="bi bi-file-earmark me-2" id="fileIcon"></i>
                                <div class="flex-grow-1">
                                    <strong id="fileName"></strong>
                                    <br>
                                    <small id="fileSize"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Information -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Dokumen</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label required">Nama Dokumen</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}"
                                       placeholder="Contoh: Penawaran Renovasi Kantor"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Kategori</label>
                                <select name="category" 
                                        class="form-select @error('category') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="penawaran" {{ old('category') == 'penawaran' ? 'selected' : '' }}>
                                        Penawaran
                                    </option>
                                    <option value="kontrak" {{ old('category') == 'kontrak' ? 'selected' : '' }}>
                                        Kontrak
                                    </option>
                                    <option value="invoice" {{ old('category') == 'invoice' ? 'selected' : '' }}>
                                        Invoice
                                    </option>
                                    <option value="survey" {{ old('category') == 'survey' ? 'selected' : '' }}>
                                        Survey
                                    </option>
                                    <option value="design" {{ old('category') == 'design' ? 'selected' : '' }}>
                                        Design
                                    </option>
                                    <option value="progress" {{ old('category') == 'progress' ? 'selected' : '' }}>
                                        Progress
                                    </option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>
                                        Lainnya
                                    </option>
                                </select>
                                @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Related To</label>
                                <div class="input-group">
                                    <select name="documentable_type" 
                                            class="form-select @error('documentable_type') is-invalid @enderror" 
                                            id="documentableType"
                                            style="max-width: 120px;"
                                            required>
                                        <option value="project" {{ old('documentable_type', $preselected['type']) == 'project' ? 'selected' : '' }}>
                                            Project
                                        </option>
                                        <option value="client" {{ old('documentable_type', $preselected['type']) == 'client' ? 'selected' : '' }}>
                                            Client
                                        </option>
                                    </select>
                                    <select name="documentable_id" 
                                            class="form-select @error('documentable_id') is-invalid @enderror" 
                                            id="documentableId"
                                            required>
                                        <option value="">-- Pilih --</option>
                                    </select>
                                </div>
                                @error('documentable_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('documentable_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Deskripsi singkat tentang dokumen ini...">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_public" 
                                           name="is_public" 
                                           value="1"
                                           {{ old('is_public') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_public">
                                        Dokumen dapat diakses oleh semua user
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Upload Guidelines -->
                <div class="card mb-4 border-info">
                    <div class="card-body">
                        <h6 class="text-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>Panduan Upload
                        </h6>
                        <ul class="small mb-0">
                            <li>Maksimal ukuran file: 50MB</li>
                            <li>Format yang didukung: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX, PPT, PPTX</li>
                            <li>Gunakan nama file yang deskriptif</li>
                            <li>Pilih kategori yang sesuai</li>
                            <li>Pastikan dokumen terhubung dengan project/client yang tepat</li>
                        </ul>
                    </div>
                </div>

                <!-- Related Info (Dynamic) -->
                <div class="card mb-4" id="relatedInfo" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0" id="relatedTitle">Info Project</h6>
                    </div>
                    <div class="card-body" id="relatedContent">
                        <!-- Will be filled by JavaScript -->
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="bi bi-upload me-2"></i>Upload Dokumen
                            </button>
                            <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.upload-area {
    border: 2px dashed #dee2e6;
    background-color: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: var(--primary-color);
    background-color: rgba(26, 115, 232, 0.05);
}

.upload-area.drag-over {
    border-color: var(--primary-color);
    background-color: rgba(26, 115, 232, 0.1);
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}
</style>
@endpush

@push('scripts')
<script>
// Projects and Clients data
const projects = {!! json_encode($projects) !!};
const clients = {!! json_encode($clients) !!};
const preselected = {!! json_encode($preselected) !!};

// File handling
const fileInput = document.getElementById('fileInput');
const uploadArea = document.getElementById('uploadArea');
const filePreview = document.getElementById('filePreview');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const fileIcon = document.getElementById('fileIcon');
const removeFile = document.getElementById('removeFile');
const uploadBtn = document.getElementById('uploadBtn');

// Drag & Drop
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('drag-over');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect(files[0]);
    }
});

// File selection
fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

function handleFileSelect(file) {
    // Validate file size (50MB)
    if (file.size > 52428800) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 50MB'
        });
        fileInput.value = '';
        return;
    }
    
    // Show file preview
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    
    // Set appropriate icon
    const ext = file.name.split('.').pop().toLowerCase();
    const iconMap = {
        'pdf': 'bi-file-pdf',
        'jpg': 'bi-file-image',
        'jpeg': 'bi-file-image',
        'png': 'bi-file-image',
        'doc': 'bi-file-word',
        'docx': 'bi-file-word',
        'xls': 'bi-file-excel',
        'xlsx': 'bi-file-excel',
        'ppt': 'bi-file-ppt',
        'pptx': 'bi-file-ppt'
    };
    
    fileIcon.className = `bi ${iconMap[ext] || 'bi-file-earmark'} me-2`;
    filePreview.style.display = 'block';
    
    // Auto-fill document name if empty
    const nameInput = document.querySelector('input[name="name"]');
    if (!nameInput.value) {
        nameInput.value = file.name.replace(/\.[^/.]+$/, '').replace(/[_-]/g, ' ');
    }
}

// Remove file
removeFile.addEventListener('click', () => {
    fileInput.value = '';
    filePreview.style.display = 'none';
});

// Format file size
function formatFileSize(bytes) {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;
    
    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }
    
    return size.toFixed(2) + ' ' + units[unitIndex];
}

// Related to dropdown handling
const documentableType = document.getElementById('documentableType');
const documentableId = document.getElementById('documentableId');
const relatedInfo = document.getElementById('relatedInfo');
const relatedTitle = document.getElementById('relatedTitle');
const relatedContent = document.getElementById('relatedContent');

function updateDocumentableOptions() {
    const type = documentableType.value;
    documentableId.innerHTML = '<option value="">-- Pilih --</option>';
    
    if (type === 'project') {
        relatedTitle.textContent = 'Info Project';
        projects.forEach(project => {
            const option = document.createElement('option');
            option.value = project.id;
            option.textContent = `${project.code} - ${project.name}`;
            option.selected = preselected.type === 'project' && preselected.id == project.id;
            documentableId.appendChild(option);
        });
    } else if (type === 'client') {
        relatedTitle.textContent = 'Info Client';
        clients.forEach(client => {
            const option = document.createElement('option');
            option.value = client.id;
            option.textContent = client.name;
            option.selected = preselected.type === 'client' && preselected.id == client.id;
            documentableId.appendChild(option);
        });
    }
    
    // Trigger change event to show related info
    if (documentableId.value) {
        documentableId.dispatchEvent(new Event('change'));
    }
}

documentableType.addEventListener('change', updateDocumentableOptions);

documentableId.addEventListener('change', function() {
    const type = documentableType.value;
    const id = this.value;
    
    if (!id) {
        relatedInfo.style.display = 'none';
        return;
    }
    
    relatedInfo.style.display = 'block';
    
    if (type === 'project') {
        const project = projects.find(p => p.id == id);
        if (project) {
            relatedContent.innerHTML = `
                <dl class="row mb-0 small">
                    <dt class="col-5">Client:</dt>
                    <dd class="col-7">${project.client.name}</dd>
                    <dt class="col-5">Status:</dt>
                    <dd class="col-7"><span class="badge bg-secondary">${project.status}</span></dd>
                    <dt class="col-5">PIC:</dt>
                    <dd class="col-7">${project.pic?.name || 'N/A'}</dd>
                </dl>
            `;
        }
    } else if (type === 'client') {
        const client = clients.find(c => c.id == id);
        if (client) {
            relatedContent.innerHTML = `
                <dl class="row mb-0 small">
                    <dt class="col-5">Email:</dt>
                    <dd class="col-7">${client.email || 'N/A'}</dd>
                    <dt class="col-5">Phone:</dt>
                    <dd class="col-7">${client.phone || 'N/A'}</dd>
                    <dt class="col-5">Status:</dt>
                    <dd class="col-7"><span class="badge bg-secondary">${client.status}</span></dd>
                </dl>
            `;
        }
    }
});

// Initialize on load
updateDocumentableOptions();

// Form submit handling
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    if (!fileInput.files.length) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'File Belum Dipilih',
            text: 'Silakan pilih file yang akan diupload'
        });
        return;
    }
    
    // Show loading
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
});
</script>
@endpush