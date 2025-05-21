/**
 * News Detail Page JavaScript
 * Handles comment form interactions, AJAX submissions, and UI enhancements
 */

class NewsCommentManager {
    constructor() {
        this.toggleButton = document.getElementById('toggle-comment-form');
        this.commentForm = document.getElementById('comment-form');
        this.cancelButton = document.getElementById('cancel-comment');
        this.inlineForm = document.getElementById('inline-comment-form');
        this.modalForm = document.getElementById('modal-comment-form');

        this.init();
    }

    init() {
        this.setupFormToggle();
        this.setupCharCounters();
        this.setupFormSubmissions();
        this.setupFormValidation();
        this.setupTextareaResize();
        this.animateCommentsSection();
    }

    /**
     * Setup comment form toggle functionality
     */
    setupFormToggle() {
        if (!this.toggleButton || !this.commentForm || !this.cancelButton) return;

        this.toggleButton.addEventListener('click', () => {
            if (this.commentForm.classList.contains('show')) {
                this.hideCommentForm();
            } else {
                this.showCommentForm();
            }
        });

        this.cancelButton.addEventListener('click', () => {
            this.hideCommentForm();
            this.resetForm(this.inlineForm);
        });
    }

    /**
     * Show the comment form with animation
     */
    showCommentForm() {
        this.commentForm.classList.add('show');
        this.toggleButton.innerHTML = '<i class="bi bi-dash-circle"></i> Hide Form';
        this.toggleButton.classList.remove('btn-primary');
        this.toggleButton.classList.add('btn-outline-primary');

        // Focus on first input after animation
        setTimeout(() => {
            const firstInput = this.commentForm.querySelector('input[type="text"]');
            if (firstInput) firstInput.focus();
        }, 200);
    }

    /**
     * Hide the comment form
     */
    hideCommentForm() {
        this.commentForm.classList.remove('show');
        this.toggleButton.innerHTML = '<i class="bi bi-plus-circle"></i> Add Comment';
        this.toggleButton.classList.remove('btn-outline-primary');
        this.toggleButton.classList.add('btn-primary');
    }

    /**
     * Setup character counters for textareas
     */
    setupCharCounters() {
        this.setupCounter('content', 'inline-char-count');
        this.setupCounter('modal-content', 'modal-char-count');
    }

    /**
     * Setup individual character counter
     */
    setupCounter(textareaId, counterId) {
        const textarea = document.getElementById(textareaId);
        const counter = document.getElementById(counterId);

        if (!textarea || !counter) return;

        const updateCounter = () => {
            const length = textarea.value.length;
            const maxLength = 2000;

            counter.textContent = length;
            counter.classList.remove('text-success', 'text-warning', 'text-danger');

            if (length > maxLength) {
                counter.classList.add('text-danger');
                textarea.classList.add('is-invalid');
            } else if (length > maxLength * 0.9) {
                counter.classList.add('text-warning');
                textarea.classList.remove('is-invalid');
            } else if (length > 0) {
                counter.classList.add('text-success');
                textarea.classList.remove('is-invalid');
            } else {
                textarea.classList.remove('is-invalid');
            }
        };

        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial call
    }

    /**
     * Setup AJAX form submissions
     */
    setupFormSubmissions() {
        if (this.inlineForm) {
            this.handleFormSubmit(this.inlineForm, false);
        }
        if (this.modalForm) {
            this.handleFormSubmit(this.modalForm, true);
        }
    }

    /**
     * Handle form submission with AJAX
     */
    handleFormSubmit(form, isModal) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            // Set loading state
            this.setButtonLoading(submitButton, true);

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.handleSuccessfulSubmission(data, form, isModal);
                } else {
                    this.showNotification(data.message || 'Error submitting comment', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Network error occurred. Please try again.', 'error');
            } finally {
                this.setButtonLoading(submitButton, false, originalText);
            }
        });
    }

    /**
     * Handle successful comment submission
     */
    handleSuccessfulSubmission(data, form, isModal) {
        // Add new comment to the list
        this.addNewCommentToList(data.comment);

        // Update comment count
        this.updateCommentCount();

        // Reset form
        this.resetForm(form);

        // Hide form or close modal
        if (isModal) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('commentModal'));
            modal.hide();
        } else {
            this.hideCommentForm();
        }

        // Show success notification
        this.showNotification('Your comment has been added successfully!', 'success');
    }

    /**
     * Add new comment to the comments list
     */
    addNewCommentToList(comment) {
        const commentsList = document.getElementById('comments-list');
        const noComments = document.getElementById('no-comments');

        // Remove "no comments" message if it exists
        if (noComments) {
            noComments.remove();
        }

        // Create new comment element
        const newComment = this.createCommentElement(comment);

        // Insert at the beginning of comments list
        let commentsWrapper = commentsList.querySelector('.comments-wrapper');
        if (!commentsWrapper) {
            commentsWrapper = document.createElement('div');
            commentsWrapper.className = 'comments-wrapper';
            commentsList.appendChild(commentsWrapper);
        }

        commentsWrapper.insertBefore(newComment, commentsWrapper.firstChild);

        // Scroll to new comment after animation
        setTimeout(() => {
            newComment.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Remove animation class after delay
            setTimeout(() => {
                newComment.classList.remove('new-comment');
                const avatar = newComment.querySelector('.comment-avatar div');
                avatar.classList.remove('bg-success');
                avatar.classList.add('bg-primary');
            }, 3000);
        }, 300);
    }

    /**
     * Create comment element HTML
     */
    createCommentElement(comment) {
        const newComment = document.createElement('div');
        newComment.className = 'comment-item p-3 mb-3 new-comment';
        newComment.id = `comment-${comment.id}`;
        newComment.innerHTML = `
            <div class="d-flex">
                <div class="comment-avatar me-3">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 45px; height: 45px;">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-primary">${this.escapeHtml(comment.author)}</h6>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            Just now
                        </small>
                    </div>
                    <div class="comment-content">
                        <p class="mb-0">${this.formatCommentContent(comment.content)}</p>
                    </div>
                </div>
            </div>
        `;
        return newComment;
    }

    /**
     * Update comment count in UI
     */
    updateCommentCount() {
        const countElements = ['comment-count', 'comment-count-display'];
        countElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                const currentCount = parseInt(element.textContent);
                element.textContent = currentCount + 1;
            }
        });
    }

    /**
     * Reset form to initial state
     */
    resetForm(form) {
        form.reset();
        form.querySelectorAll('.is-invalid, .is-valid').forEach(field => {
            field.classList.remove('is-invalid', 'is-valid');
        });

        // Reset character counters
        form.querySelectorAll('[id$="-char-count"]').forEach(counter => {
            counter.textContent = '0';
            counter.classList.remove('text-success', 'text-warning', 'text-danger');
        });
    }

    /**
     * Setup form validation
     */
    setupFormValidation() {
        const requiredFields = document.querySelectorAll('input[required], textarea[required]');

        requiredFields.forEach(field => {
            // Validation on blur
            field.addEventListener('blur', () => {
                if (field.value.trim() === '') {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                } else {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            });

            // Remove invalid state on input
            field.addEventListener('input', () => {
                if (field.classList.contains('is-invalid') && field.value.trim() !== '') {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                }
            });
        });
    }

    /**
     * Setup auto-resize for textareas
     */
    setupTextareaResize() {
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.classList.add('auto-resize');

            const resize = () => {
                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            };

            textarea.addEventListener('input', resize);

            // Initial resize
            if (textarea.value) {
                resize();
            }
        });
    }

    /**
     * Animate comments section on page load
     */
    animateCommentsSection() {
        const commentsSection = document.querySelector('.comments-section');
        if (commentsSection) {
            // Add slight delay to show after page content
            setTimeout(() => {
                commentsSection.style.animationDelay = '0.3s';
            }, 100);
        }
    }

    /**
     * Set button loading state
     */
    setButtonLoading(button, loading, originalText = null) {
        if (loading) {
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
            button.classList.add('btn-loading');
        } else {
            button.disabled = false;
            button.innerHTML = originalText || button.innerHTML;
            button.classList.remove('btn-loading');
        }
    }

    /**
     * Show notification to user
     */
    showNotification(message, type) {
        // Remove existing notifications
        document.querySelectorAll('.notification').forEach(notification => {
            notification.remove();
        });

        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show notification shadow`;
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                <span>${message}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /**
     * Format comment content (convert newlines to <br>)
     */
    formatCommentContent(content) {
        return this.escapeHtml(content).replace(/\n/g, '<br>');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new NewsCommentManager();
});

// Utility functions for external use
window.NewsDetail = {
    scrollToComments: () => {
        document.getElementById('comments')?.scrollIntoView({ behavior: 'smooth' });
    },

    shareOnSocial: (platform) => {
        const url = window.location.href;
        const title = document.querySelector('h1').textContent;

        const shareUrls = {
            twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`,
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
            linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`
        };

        if (shareUrls[platform]) {
            window.open(shareUrls[platform], '_blank', 'width=600,height=400');
        }
    }
};
