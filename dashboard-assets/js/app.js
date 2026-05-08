/**
 * FRS Admin Panel - Vanilla JS (replaces scripts.js + jQuery plugins)
 * No jQuery dependency
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        /* ========================================
         * 1. Sidebar Accordion Navigation
         * Replaces: jquery.dcjqaccordion.2.7.js
         * ======================================== */
        var navAccordion = document.getElementById('nav-accordion');
        if (navAccordion) {
            // Handle sub-menu toggles
            var subMenuLinks = navAccordion.querySelectorAll('.sub-menu > a');
            subMenuLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    var parentLi = this.parentElement;
                    var subUl = parentLi.querySelector('ul.sub');
                    if (!subUl) return;

                    // Close other open sub-menus
                    var siblings = parentLi.parentElement.querySelectorAll('.sub-menu');
                    siblings.forEach(function (sib) {
                        if (sib !== parentLi) {
                            var sibUl = sib.querySelector('ul.sub');
                            if (sibUl) sibUl.style.display = 'none';
                            sib.classList.remove('active');
                        }
                    });

                    // Toggle current sub-menu with smooth transition
                    if (subUl.style.display === 'block') {
                        subUl.style.display = 'none';
                        parentLi.classList.remove('active');
                    } else {
                        subUl.style.display = 'block';
                        parentLi.classList.add('active');
                    }
                });
            });

            // Auto-expand current page's parent menu
            var currentPage = window.location.pathname.split('/').pop();
            var activeLink = navAccordion.querySelector('a[href="' + currentPage + '"]');
            if (activeLink) {
                var parentSub = activeLink.closest('.sub-menu');
                if (parentSub) {
                    parentSub.classList.add('active');
                    var subUl = parentSub.querySelector('ul.sub');
                    if (subUl) subUl.style.display = 'block';
                }
            }
        }

        /* ========================================
         * 2. Sidebar Toggle (Show/Hide)
         * Replaces: jQuery sidebar toggle in scripts.js
         * ======================================== */
        var sidebarToggle = document.querySelector('.sidebar-toggle-box .fa-bars');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                var sidebar = document.getElementById('sidebar');
                var mainContent = document.getElementById('main-content');
                if (sidebar) sidebar.classList.toggle('hide-left-bar');
                if (mainContent) mainContent.classList.toggle('merge-left');
            });
        }

        /* ========================================
         * 3. Panel Collapse / Close
         * Replaces: jQuery panel tools in scripts.js
         * ======================================== */
        document.querySelectorAll('.panel .tools .fa-chevron-down, .panel .tools .fa-chevron-up').forEach(function (icon) {
            icon.addEventListener('click', function () {
                var panel = this.closest('.panel') || this.closest('.card');
                var body = panel ? (panel.querySelector('.panel-body') || panel.querySelector('.card-body')) : null;
                if (!body) return;

                if (this.classList.contains('fa-chevron-down')) {
                    this.classList.replace('fa-chevron-down', 'fa-chevron-up');
                    body.style.display = 'none';
                } else {
                    this.classList.replace('fa-chevron-up', 'fa-chevron-down');
                    body.style.display = '';
                }
            });
        });

        document.querySelectorAll('.panel .tools .fa-times').forEach(function (icon) {
            icon.addEventListener('click', function () {
                var panel = this.closest('.panel') || this.closest('.card');
                if (panel && panel.parentElement) panel.parentElement.removeChild(panel);
            });
        });

        /* ========================================
         * 4. Widget Collapse
         * ======================================== */
        document.querySelectorAll('.widget-head').forEach(function (head) {
            head.addEventListener('click', function (e) {
                e.preventDefault();
                var container = this.nextElementSibling;
                if (container && container.classList.contains('widget-container')) {
                    container.style.display = container.style.display === 'none' ? '' : 'none';
                }
                var icon = this.querySelector('.widget-collapse i');
                if (icon) {
                    icon.classList.toggle('ico-minus');
                    icon.classList.toggle('ico-plus');
                }
            });
        });

        /* ========================================
         * 5. Right Sidebar Toggle
         * ======================================== */
        var rightToggle = document.querySelector('.toggle-right-box .fa-bars');
        if (rightToggle) {
            rightToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                var container = document.getElementById('container');
                var rightSidebar = document.querySelector('.right-sidebar');
                var header = document.querySelector('.header');
                if (container) container.classList.toggle('open-right-panel');
                if (rightSidebar) rightSidebar.classList.toggle('open-right-bar');
                if (header) header.classList.toggle('merge-header');
            });
        }

        // Close right panel on main content click
        var closableAreas = document.querySelectorAll('.header, #main-content, #sidebar');
        closableAreas.forEach(function (area) {
            area.addEventListener('click', function () {
                var container = document.getElementById('container');
                var rightSidebar = document.querySelector('.right-sidebar');
                var header = document.querySelector('.header');
                if (container) container.classList.remove('open-right-panel');
                if (rightSidebar) rightSidebar.classList.remove('open-right-bar');
                if (header) header.classList.remove('merge-header');
            });
        });

        /* ========================================
         * 6. Tooltips & Popovers (Bootstrap 5 native)
         * ======================================== */
        if (typeof bootstrap !== 'undefined') {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                new bootstrap.Popover(el);
            });
        }

    });
})();
