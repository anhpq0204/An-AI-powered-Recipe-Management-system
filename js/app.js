/**
 * FRS Frontend — Vanilla JS
 * Replaces jQuery + plugins with modern ES6 patterns
 * Dependencies: None (pure Vanilla JS)
 */
(function () {
    'use strict';

    // ── Preloader ──────────────────────────────────────
    window.addEventListener('load', function () {
        var preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.transition = 'opacity 0.5s ease';
            preloader.style.opacity = '0';
            setTimeout(function () { preloader.remove(); }, 500);
        }
    });

    document.addEventListener('DOMContentLoaded', function () {

        // ── News Ticker ────────────────────────────────
        var ticker = document.getElementById('breakingNewsTicker');
        if (ticker) {
            var items = ticker.querySelectorAll('li');
            if (items.length > 1) {
                var idx = 0;
                setInterval(function () {
                    items[idx].style.display = 'none';
                    idx = (idx + 1) % items.length;
                    items[idx].style.display = '';
                }, 3500);
            }
        }

        // ── Responsive Navigation ──────────────────────
        var navToggler = document.querySelector('.navbarToggler');
        var classyMenu = document.querySelector('.classy-menu');
        var closeIcon  = document.querySelector('.classycloseIcon');

        if (navToggler && classyMenu) {
            navToggler.addEventListener('click', function () {
                classyMenu.classList.add('menu-on');
            });
        }
        if (closeIcon && classyMenu) {
            closeIcon.addEventListener('click', function () {
                classyMenu.classList.remove('menu-on');
            });
        }

        // ── Search Overlay ─────────────────────────────
        var searchOverlay = document.getElementById('searchOverlay');
        var searchBtn     = document.querySelector('.search-btn');
        var searchClose   = document.getElementById('searchClose');
        var searchInput   = document.getElementById('searchInput');

        if (searchBtn && searchOverlay) {
            searchBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                searchOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                if (searchInput) {
                    setTimeout(function () { searchInput.focus(); }, 400);
                }
            });
        }

        if (searchClose && searchOverlay) {
            searchClose.addEventListener('click', function () {
                searchOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && searchOverlay && searchOverlay.classList.contains('active')) {
                searchOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        if (searchOverlay) {
            searchOverlay.addEventListener('click', function (e) {
                if (e.target === searchOverlay) {
                    searchOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }

        // ── Scroll-triggered Animations ────────────────
        if (window.innerWidth > 767) {
            var animEls = document.querySelectorAll('[data-animation]');
            if (animEls.length > 0) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            var name = entry.target.getAttribute('data-animation');
                            entry.target.classList.add('animated', name);
                            entry.target.style.opacity = '1';
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });

                animEls.forEach(function (el) {
                    el.style.opacity = '0';
                    observer.observe(el);
                });
            }
        }

        // ── Scroll to Top Button ───────────────────────
        var scrollBtn = document.createElement('a');
        scrollBtn.innerHTML = '<i class="fa fa-angle-up"></i>';
        scrollBtn.className = 'scroll-to-top';
        scrollBtn.href = '#';
        scrollBtn.style.cssText =
            'display:none;position:fixed;bottom:20px;right:20px;' +
            'width:40px;height:40px;line-height:40px;text-align:center;' +
            'background:#2d6a4f;color:#fff;border-radius:50%;' +
            'font-size:18px;z-index:9999;text-decoration:none;' +
            'transition:opacity 0.3s;box-shadow:0 2px 8px rgba(0,0,0,0.2);';
        document.body.appendChild(scrollBtn);

        window.addEventListener('scroll', function () {
            scrollBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
        scrollBtn.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // ── Prevent default on empty links ─────────────
        document.querySelectorAll('a[href="#"]').forEach(function (link) {
            link.addEventListener('click', function (e) { e.preventDefault(); });
        });

        // ── Animation delay/duration helpers ───────────
        document.querySelectorAll('[data-delay]').forEach(function (el) {
            el.style.animationDelay = el.getAttribute('data-delay');
        });
        document.querySelectorAll('[data-duration]').forEach(function (el) {
            el.style.animationDuration = el.getAttribute('data-duration');
        });

        // ── Pending toast from PHP ─────────────────────
        if (window._frsToast) {
            showToast(window._frsToast.msg, window._frsToast.type);
        }

    });

    function showToast(msg, type) {
        if (typeof bootstrap === 'undefined') return;
        var container = document.getElementById('frsToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'frsToastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        var el = document.createElement('div');
        el.className = 'toast align-items-center text-bg-' + (type || 'success') + ' border-0';
        el.setAttribute('role', 'alert');
        el.innerHTML = '<div class="d-flex"><div class="toast-body">' + msg + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        var t = new bootstrap.Toast(el, { delay: 4000 });
        t.show();
        el.addEventListener('hidden.bs.toast', function () { el.remove(); });
    }

    window.showToast = showToast;

    // ── Favorite (heart) toggle ────────────────────────
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.fav-btn, .fav-btn-detail');
        if (!btn) return;

        if (btn.dataset.loggedIn !== '1') {
            window.location.href = 'user/login.php';
            return;
        }

        var recipeId = btn.dataset.recipeId;
        if (!recipeId) return;

        btn.disabled = true;

        // Determine API base path (works from root or recipe-details.php)
        var apiBase = 'api/toggle-favorite.php';
        if (window.location.pathname.indexOf('/user/') !== -1) {
            apiBase = '../api/toggle-favorite.php';
        }

        fetch(apiBase, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'recipe_id=' + encodeURIComponent(recipeId)
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            btn.disabled = false;
            if (!data.success) return;

            var isFav = data.is_favorited;
            btn.classList.toggle('favorited', isFav);

            // Update FA icon class
            var iconEl = btn.querySelector('.fav-icon');
            if (iconEl) {
                iconEl.classList.remove('fa-heart', 'fa-heart-o');
                iconEl.classList.add(isFav ? 'fa-heart' : 'fa-heart-o');
            }

            // Update label
            var labelEl = btn.querySelector('.fav-label');
            if (labelEl) {
                labelEl.textContent = isFav
                    ? (window._favI18n ? window._favI18n.saved : 'Saved!')
                    : (window._favI18n ? window._favI18n.save  : 'Save to favorites');
            }

            // Update count on card buttons
            var countEl = btn.querySelector('.fav-count');
            if (countEl) { countEl.textContent = data.count; }

            // Update count badge on detail page
            var countBadgeEl = btn.querySelector('.fav-count-badge');
            if (countBadgeEl) { countBadgeEl.textContent = data.count; }

            btn.title = isFav
                ? (window._favI18n ? window._favI18n.remove : 'Remove from favorites')
                : (window._favI18n ? window._favI18n.save   : 'Save to favorites');

            // Pop animation — remove then re-add to retrigger
            btn.classList.remove('pop');
            void btn.offsetWidth;
            btn.classList.add('pop');
            btn.addEventListener('animationend', function () {
                btn.classList.remove('pop');
            }, { once: true });
        })
        .catch(function () { btn.disabled = false; });
    });

})();
