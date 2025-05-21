// Admin JavaScript for admin interface

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide flash messages after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && !alert.classList.contains('alert-permanent')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Character counters for textareas
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(function(textarea) {
        const maxLength = parseInt(textarea.getAttribute('maxlength'));
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.style.marginTop = '0.25rem';
        textarea.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${textarea.value.length}/${maxLength} characters`;

            if (remaining < 50) {
                counter.classList.add('text-warning');
                counter.classList.remove('text-muted');
            } else {
                counter.classList.add('text-muted');
                counter.classList.remove('text-warning');
            }
        }

        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial call
    });

    // Preview image uploads
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remove existing preview if any
                    const existingPreview = input.parentNode.querySelector('.image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    // Create new preview
                    const preview = document.createElement('div');
                    preview.className = 'image-preview mt-2';
                    preview.innerHTML = `
                        <img src="${e.target.result}"
                             class="img-thumbnail"
                             style="max-width: 200px; max-height: 200px;"
                             alt="Preview">
                        <div class="form-text">Preview of selected image</div>
                    `;
                    input.parentNode.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Table row highlighting
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(function(row) {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });

    // Auto-save draft functionality for news forms
    const newsContentTextarea = document.querySelector('#news_content');
    if (newsContentTextarea) {
        let saveTimeout;

        function saveDraft() {
            const formData = new FormData();
            formData.append('title', document.querySelector('#news_title').value);
            formData.append('content', newsContentTextarea.value);
            formData.append('shortDescription', document.querySelector('#news_shortDescription').value);

            // Simple localStorage save
            const draftData = {
                title: document.querySelector('#news_title').value,
                content: newsContentTextarea.value,
                shortDescription: document.querySelector('#news_shortDescription').value,
                timestamp: Date.now()
            };
            localStorage.setItem('news_draft', JSON.stringify(draftData));

            // Show saved indicator
            const saveIndicator = document.querySelector('#save-indicator') || document.createElement('small');
            saveIndicator.id = 'save-indicator';
            saveIndicator.className = 'text-muted ms-2';
            saveIndicator.textContent = 'Draft saved';
            if (!document.querySelector('#save-indicator')) {
                newsContentTextarea.parentNode.querySelector('label').appendChild(saveIndicator);
            }

            setTimeout(function() {
                saveIndicator.style.opacity = '0';
                setTimeout(function() {
                    saveIndicator.remove();
                }, 300);
            }, 2000);
        }

        newsContentTextarea.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(saveDraft, 2000); // Save after 2 seconds of inactivity
        });

        // Load draft on page load
        const savedDraft = localStorage.getItem('news_draft');
        if (savedDraft) {
            const draft = JSON.parse(savedDraft);
            // Only load if the draft is less than 24 hours old
            if (Date.now() - draft.timestamp < 24 * 60 * 60 * 1000) {
                if (confirm('A draft was found. Would you like to restore it?')) {
                    document.querySelector('#news_title').value = draft.title || '';
                    document.querySelector('#news_shortDescription').value = draft.shortDescription || '';
                    newsContentTextarea.value = draft.content || '';
                }
            }
        }

        // Clear draft on successful form submission
        const form = newsContentTextarea.closest('form');
        form.addEventListener('submit', function() {
            localStorage.removeItem('news_draft');
        });
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});
