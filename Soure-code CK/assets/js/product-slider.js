/**
 * Product Slider / Carousel with 6 products per view
 * Features:
 * - Next / Prev navigation buttons
 * - Pagination dots with active states
 * - Real-time "Trang X / Y" and "Còn lại Z sản phẩm" tracking
 * - Clickable dots to jump to any page
 * - Touch swipe support for mobile
 */
function initProductSliders() {
    const sliders = document.querySelectorAll('.product-slider-component');
    sliders.forEach(slider => {
        const track = slider.querySelector('.slider-track');
        if (!track) return;

        const sliderId = slider.dataset.sliderId;
        const totalItems = parseInt(slider.dataset.totalItems, 10) || 0;
        const pageSize = parseInt(slider.dataset.pageSize, 10) || 6;
        const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
        let currentPage = 1;

        const prevBtn = slider.querySelector('.slider-btn.prev-btn');
        const nextBtn = slider.querySelector('.slider-btn.next-btn');
        const dots = slider.querySelectorAll('.slider-dot');
        const curPageEl = slider.querySelector('.status-current-page');
        const totalPagesEl = slider.querySelector('.status-total-pages');

        function goToPage(page) {
            currentPage = Math.max(1, Math.min(page, totalPages));
            const offset = (currentPage - 1) * 100;
            track.style.transform = `translateX(-${offset}%)`;

            // Update dots
            dots.forEach((dot, idx) => {
                if (idx + 1 === currentPage) {
                    dot.classList.add('active');
                    dot.setAttribute('aria-current', 'true');
                } else {
                    dot.classList.remove('active');
                    dot.removeAttribute('aria-current');
                }
            });

            // Update prev/next button states
            if (prevBtn) {
                if (currentPage === 1) {
                    prevBtn.classList.add('disabled');
                    prevBtn.setAttribute('disabled', 'disabled');
                } else {
                    prevBtn.classList.remove('disabled');
                    prevBtn.removeAttribute('disabled');
                }
            }
            if (nextBtn) {
                if (currentPage === totalPages) {
                    nextBtn.classList.add('disabled');
                    nextBtn.setAttribute('disabled', 'disabled');
                } else {
                    nextBtn.classList.remove('disabled');
                    nextBtn.removeAttribute('disabled');
                }
            }

            // Update text indicators
            if (curPageEl) curPageEl.textContent = currentPage;
            if (totalPagesEl) totalPagesEl.textContent = totalPages;
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) {
                    goToPage(currentPage - 1);
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage < totalPages) {
                    goToPage(currentPage + 1);
                }
            });
        }

        dots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                const targetPage = parseInt(dot.dataset.page, 10);
                if (!isNaN(targetPage)) {
                    goToPage(targetPage);
                }
            });
        });

        // Touch swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        track.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        track.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (diff > 50 && currentPage < totalPages) {
                goToPage(currentPage + 1);
            } else if (diff < -50 && currentPage > 1) {
                goToPage(currentPage - 1);
            }
        }, { passive: true });

        // Initial setup
        goToPage(1);
    });
}

// Auto-run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductSliders);
} else {
    initProductSliders();
}
