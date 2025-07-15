/**
 * Pipeline Management JavaScript
 *
 * This file handles the interactive functionality of the pipeline feature:
 * - Drag and drop for kanban board
 * - Status updates
 * - Filtering
 * - Animations and UI enhancements
 */

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initKanbanBoard();
    initPipelineFilters();
    enhanceUIExperience();
});

/**
 * Initialize the Kanban board with Sortable.js
 */
function initKanbanBoard() {
    // Only initialize if we're on the kanban view
    const kanbanColumns = document.querySelectorAll('.kanban-column-body');
    if (kanbanColumns.length === 0) return;

    kanbanColumns.forEach(column => {
        new Sortable(column, {
            group: 'shared',
            animation: 150,
            ghostClass: 'dragging',
            dragClass: 'dragging',
            onStart: function(evt) {
                evt.item.classList.add('dragging');

                // Highlight possible drop zones
                document.querySelectorAll('.kanban-column-body').forEach(col => {
                    if (col !== evt.from) {
                        col.classList.add('highlight-dropzone');
                    }
                });
            },
            onEnd: function(evt) {
                evt.item.classList.remove('dragging');

                // Remove highlight from drop zones
                document.querySelectorAll('.kanban-column-body').forEach(col => {
                    col.classList.remove('highlight-dropzone');
                });

                // Get project ID and new status
                const projectId = evt.item.dataset.projectId;
                const newStatus = evt.to.dataset.status;
                const oldStatus = evt.from.dataset.status;

                // If status changed, update in backend
                if (newStatus !== oldStatus) {
                    updateProjectStatus(projectId, newStatus);
                }
            }
        });
    });
}

/**
 * Update project status via AJAX
 * @param {string} projectId - The project ID
 * @param {string} newStatus - The new status
 */
function updateProjectStatus(projectId, newStatus) {
    // Show loading
    Swal.fire({
        title: 'Updating...',
        text: 'Moving project to ' + newStatus,
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!csrfToken) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'CSRF token not found. Please refresh the page.'
        });
        return;
    }

    // Make AJAX request
    fetch('/pipeline/update-stage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            project_id: projectId,
            new_status: newStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });

            // Update column counts and values (would require a page reload or dynamic update)
            // For now, we'll just reload after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.error || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to update project status.'
        });
        // Reload to revert changes
        setTimeout(() => window.location.reload(), 2000);
    });
}

/**
 * Initialize pipeline filters and search functionality
 */
function initPipelineFilters() {
    // Filter modal handling
    const filterForm = document.querySelector('#filterModal form');

    if (filterForm) {
        // Clear filters button
        const clearFiltersBtn = document.createElement('button');
        clearFiltersBtn.type = 'button';
        clearFiltersBtn.className = 'btn btn-outline-secondary me-2';
        clearFiltersBtn.textContent = 'Clear Filters';

        clearFiltersBtn.addEventListener('click', () => {
            // Clear all filter inputs
            filterForm.querySelectorAll('input[type="text"], input[type="date"]').forEach(input => {
                input.value = '';
            });

            // Submit the form to refresh without filters
            filterForm.submit();
        });

        // Insert clear button before submit button
        filterForm.querySelector('.modal-footer button[type="submit"]')
            .insertAdjacentElement('beforebegin', clearFiltersBtn);

        // Auto-submit on date changes for better UX
        filterForm.querySelectorAll('input[type="date"]').forEach(dateInput => {
            dateInput.addEventListener('change', () => {
                // Only auto-submit if both dates are filled or both are empty
                const dateInputs = filterForm.querySelectorAll('input[type="date"]');
                const dateValues = Array.from(dateInputs).map(input => input.value);

                if (dateValues.every(value => value) || dateValues.every(value => !value)) {
                    filterForm.submit();
                }
            });
        });
    }

    // Quick search functionality (if exists)
    const quickSearchInput = document.querySelector('#quick-search-input');
    if (quickSearchInput) {
        // Add debounce function to prevent excessive requests
        let searchTimeout;
        quickSearchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Set the search value to the hidden form input
                const searchValue = quickSearchInput.value.trim();
                document.querySelector('#search-value').value = searchValue;

                // Submit the form with the current page
                document.querySelector('#search-form').submit();
            }, 500); // 500ms debounce
        });
    }
}
