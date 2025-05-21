// Main JavaScript for public interface

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

    // Smooth scroll for anchor links
    const anchors = document.querySelectorAll('a[href^="#"]');
    anchors.forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Form validation for comment form
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

    // Image lazy loading for older browsers
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // Character counter for comment textarea
    const commentTextarea = document.querySelector('#comment_content');
    if (commentTextarea) {
        const maxLength = 1000;
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        counter.style.marginTop = '0.25rem';
        commentTextarea.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLength - commentTextarea.value.length;
            counter.textContent = `${commentTextarea.value.length}/${maxLength} characters`;

            if (remaining < 100) {
                counter.classList.add('text-warning');
                counter.classList.remove('text-muted');
            } else {
                counter.classList.add('text-muted');
                counter.classList.remove('text-warning');
            }
        }

        commentTextarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial call
    }
});
