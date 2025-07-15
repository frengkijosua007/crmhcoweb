@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Notifikasi</h4>
            <p class="text-muted mb-0">{{ $unreadCount }} notifikasi belum dibaca</p>
        </div>
        <div>
            @if($groupedNotifications->count() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-check-all me-2"></i>Tandai Semua Dibaca
                </button>
            </form>
            
            <form action="{{ route('notifications.clear-all') }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Hapus semua notifikasi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-2"></i>Hapus Semua
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card">
        <div class="card-body p-0">
            @forelse($groupedNotifications as $date => $dateNotifications)
            <div class="notification-date-group">
                <div class="notification-date-header">
                    <h6 class="mb-0">
                        @if($date == now()->format('Y-m-d'))
                            Hari Ini
                        @elseif($date == now()->subDay()->format('Y-m-d'))
                            Kemarin
                        @else
                            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                        @endif
                    </h6>
                </div>
                
                <div class="notification-list">
                    @foreach($dateNotifications as $notification)
                    <div class="notification-item {{ !$notification->read_at ? 'unread' : '' }}" 
                         data-notification-id="{{ $notification->id }}">
                        <div class="notification-icon">
                            @php
                                $icon = '';
                                $color = '';
                                
                                switch($notification->type) {
                                    case 'App\Notifications\ProjectStatusChanged':
                                        $icon = 'bi-building';
                                        $color = 'primary';
                                        break;
                                    case 'App\Notifications\SurveyAssigned':
                                        $icon = 'bi-clipboard-check';
                                        $color = 'success';
                                        break;
                                    case 'App\Notifications\DocumentUploaded':
                                        $icon = 'bi-file-earmark';
                                        $color = 'info';
                                        break;
                                    case 'App\Notifications\NewClientAssigned':
                                        $icon = 'bi-person-plus';
                                        $color = 'warning';
                                        break;
                                    default:
                                        $icon = 'bi-bell';
                                        $color = 'secondary';
                                }
                            @endphp
                            <div class="icon-circle bg-{{ $color }}">
                                <i class="{{ $icon }} text-white"></i>
                            </div>
                        </div>
                        
                        <div class="notification-content">
                            <a href="{{ route('notifications.show', $notification->id) }}" 
                               class="notification-link">
                                <h6 class="mb-1">{{ $notification->data['message'] ?? 'New notification' }}</h6>
                                <p class="mb-0 text-muted small">
                                    @if(isset($notification->data['changed_by']))
                                        Oleh {{ $notification->data['changed_by'] }}
                                    @elseif(isset($notification->data['assigned_by']))
                                        Oleh {{ $notification->data['assigned_by'] }}
                                    @endif
                                </p>
                            </a>
                        </div>
                        
                        <div class="notification-meta">
                            <small class="text-muted">{{ $notification->created_at->format('H:i') }}</small>
                            <div class="notification-actions">
                                @if(!$notification->read_at)
                                <button class="btn btn-sm btn-link mark-as-read" 
                                        data-notification-id="{{ $notification->id }}"
                                        title="Tandai sebagai dibaca">
                                    <i class="bi bi-check"></i>
                                </button>
                                @endif
                                <button class="btn btn-sm btn-link delete-notification" 
                                        data-notification-id="{{ $notification->id }}"
                                        title="Hapus">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted d-block mb-3"></i>
                <h5>Tidak Ada Notifikasi</h5>
                <p class="text-muted">Semua notifikasi akan muncul di sini</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.notification-date-header {
    padding: 0.75rem 1.25rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    position: sticky;
    top: 0;
    z-index: 10;
}

.notification-item {
    display: flex;
    align-items: start;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #eee;
    transition: all 0.3s ease;
    position: relative;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e8f4fd;
    border-left: 4px solid var(--primary-color);
}

.notification-icon {
    margin-right: 1rem;
}

.icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-link {
    text-decoration: none;
    color: inherit;
}

.notification-link:hover h6 {
    color: var(--primary-color);
}

.notification-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-left: 1rem;
}

.notification-actions {
    display: flex;
    gap: 0.25rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

.notification-actions .btn-link {
    padding: 0.25rem;
    color: #6c757d;
}

.notification-actions .btn-link:hover {
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .notification-item {
        padding: 0.75rem;
    }
    
    .notification-actions {
        opacity: 1;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Mark as read
document.querySelectorAll('.mark-as-read').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const notificationId = this.dataset.notificationId;
        
        fetch('{{ route("notifications.mark-as-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                notification_id: notificationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`.notification-item[data-notification-id="${notificationId}"]`);
                item.classList.remove('unread');
                this.remove();
                updateNotificationBadge();
            }
        });
    });
});

// Delete notification
document.querySelectorAll('.delete-notification').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const notificationId = this.dataset.notificationId;
        
        if (confirm('Hapus notifikasi ini?')) {
            fetch(`{{ route("notifications.index") }}/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`.notification-item[data-notification-id="${notificationId}"]`);
                    item.remove();
                    updateNotificationBadge();
                }
            });
        }
    });
});

function updateNotificationBadge() {
    // Update the notification badge in the navbar
    fetch('{{ route("notifications.unread") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
}
</script>
@endpush