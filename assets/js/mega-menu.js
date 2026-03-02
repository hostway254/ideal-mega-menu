/**
 * Ideal Mega Menu - Frontend JavaScript
 *
 * @package Ideal_Mega_Menu
 * @version 1.0.0
 */

(function () {
    'use strict';

    // Settings from WordPress.
    var settings = window.idealMegaMenu || {
        animation: 'fade',
        animationSpeed: 300,
        trigger: 'hover',
        mobileBreakpoint: 768,
        menuStyle: 'default',
    };

    /**
     * Initialize the mega menu.
     */
    function init() {
        var containers = document.querySelectorAll('.imm-mega-menu-container');

        containers.forEach(function (container) {
            // Add animation class.
            container.classList.add('imm-animation-' + settings.animation);

            // Set animation speed via CSS custom property.
            container.style.setProperty(
                '--imm-animation-speed',
                settings.animationSpeed + 'ms'
            );

            setupDesktopMenu(container);
            setupMobileMenu(container);
            setupKeyboardNavigation(container);
        });

        // Handle window resize.
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                containers.forEach(function (container) {
                    handleResize(container);
                });
            }, 250);
        });
    }

    /**
     * Setup desktop menu interactions.
     *
     * @param {Element} container Menu container element.
     */
    function setupDesktopMenu(container) {
        var menuItems = container.querySelectorAll(
            '.imm-mega-menu > li.menu-item-has-children, .imm-mega-menu > li.imm-has-mega-menu'
        );

        menuItems.forEach(function (item) {
            var hoverTimer;

            if (settings.trigger === 'hover') {
                item.addEventListener('mouseenter', function () {
                    clearTimeout(hoverTimer);
                    closeAllMenus(container, item);
                    openMenu(item);
                });

                item.addEventListener('mouseleave', function () {
                    hoverTimer = setTimeout(function () {
                        closeMenu(item);
                    }, 200);
                });
            } else {
                // Click trigger.
                var link = item.querySelector(':scope > a');
                if (link) {
                    link.addEventListener('click', function (e) {
                        if (
                            item.classList.contains('menu-item-has-children') ||
                            item.classList.contains('imm-has-mega-menu')
                        ) {
                            e.preventDefault();
                            if (item.classList.contains('imm-active')) {
                                closeMenu(item);
                            } else {
                                closeAllMenus(container);
                                openMenu(item);
                            }
                        }
                    });
                }
            }
        });

        // Close menus when clicking outside.
        document.addEventListener('click', function (e) {
            if (!container.contains(e.target)) {
                closeAllMenus(container);
            }
        });
    }

    /**
     * Setup mobile menu toggle and accordion behavior.
     *
     * @param {Element} container Menu container element.
     */
    function setupMobileMenu(container) {
        var toggle = container.querySelector('.imm-mobile-toggle');
        var menu = container.querySelector('.imm-mega-menu');

        if (toggle && menu) {
            toggle.addEventListener('click', function () {
                var expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', !expanded);
                menu.classList.toggle('imm-mobile-open');
            });
        }

        // Mobile accordion for sub-menus.
        var parentItems = container.querySelectorAll(
            '.imm-mega-menu .menu-item-has-children > a, .imm-mega-menu .imm-has-mega-menu > a'
        );

        parentItems.forEach(function (link) {
            link.addEventListener('click', function (e) {
                if (window.innerWidth <= settings.mobileBreakpoint) {
                    e.preventDefault();
                    var parentLi = link.parentElement;
                    parentLi.classList.toggle('imm-mobile-expanded');
                }
            });
        });
    }

    /**
     * Setup keyboard navigation for accessibility.
     *
     * @param {Element} container Menu container element.
     */
    function setupKeyboardNavigation(container) {
        var topLevelItems = container.querySelectorAll(
            '.imm-mega-menu > li'
        );

        topLevelItems.forEach(function (item) {
            var link = item.querySelector(':scope > a');
            if (!link) return;

            link.addEventListener('keydown', function (e) {
                switch (e.key) {
                    case 'Enter':
                    case ' ':
                        if (
                            item.classList.contains('menu-item-has-children') ||
                            item.classList.contains('imm-has-mega-menu')
                        ) {
                            e.preventDefault();
                            if (item.classList.contains('imm-active')) {
                                closeMenu(item);
                            } else {
                                closeAllMenus(container);
                                openMenu(item);
                                // Focus first link in submenu.
                                var firstSubLink = item.querySelector(
                                    '.sub-menu a, .imm-mega-menu-panel a'
                                );
                                if (firstSubLink) {
                                    firstSubLink.focus();
                                }
                            }
                        }
                        break;

                    case 'Escape':
                        closeAllMenus(container);
                        link.focus();
                        break;

                    case 'ArrowDown':
                        if (item.classList.contains('imm-active')) {
                            e.preventDefault();
                            var firstLink = item.querySelector(
                                '.sub-menu a, .imm-mega-menu-panel a'
                            );
                            if (firstLink) {
                                firstLink.focus();
                            }
                        }
                        break;
                }
            });
        });

        // Navigate within submenus.
        container.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAllMenus(container);
                var activeTop = container.querySelector(
                    '.imm-mega-menu > li.imm-active > a'
                );
                if (activeTop) {
                    activeTop.focus();
                }
            }

            if (e.key === 'Tab') {
                // Close menus when tabbing out.
                setTimeout(function () {
                    if (!container.contains(document.activeElement)) {
                        closeAllMenus(container);
                    }
                }, 0);
            }
        });
    }

    /**
     * Open a menu item's submenu.
     *
     * @param {Element} item Menu item element.
     */
    function openMenu(item) {
        item.classList.add('imm-active');
        var link = item.querySelector(':scope > a');
        if (link) {
            link.setAttribute('aria-expanded', 'true');
        }
    }

    /**
     * Close a menu item's submenu.
     *
     * @param {Element} item Menu item element.
     */
    function closeMenu(item) {
        item.classList.remove('imm-active');
        var link = item.querySelector(':scope > a');
        if (link) {
            link.setAttribute('aria-expanded', 'false');
        }
    }

    /**
     * Close all open menus in a container.
     *
     * @param {Element} container Menu container element.
     * @param {Element} except    Optional element to exclude.
     */
    function closeAllMenus(container, except) {
        var openItems = container.querySelectorAll('.imm-active');
        openItems.forEach(function (item) {
            if (item !== except) {
                closeMenu(item);
            }
        });
    }

    /**
     * Handle window resize events.
     *
     * @param {Element} container Menu container element.
     */
    function handleResize(container) {
        var menu = container.querySelector('.imm-mega-menu');
        var toggle = container.querySelector('.imm-mobile-toggle');

        if (window.innerWidth > settings.mobileBreakpoint) {
            // Desktop: reset mobile states.
            if (menu) {
                menu.classList.remove('imm-mobile-open');
            }
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
            // Remove mobile expanded classes.
            var expanded = container.querySelectorAll('.imm-mobile-expanded');
            expanded.forEach(function (item) {
                item.classList.remove('imm-mobile-expanded');
            });
        }
    }

    // Initialize when DOM is ready.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
