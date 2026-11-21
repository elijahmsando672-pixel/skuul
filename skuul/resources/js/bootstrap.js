import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrfToken = document.querySelector('meta[name="csrf-token"]');

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
}

document.addEventListener('DOMContentLoaded', function () {
    const sidebars = Array.from(document.querySelectorAll('[id="sidebar"]'));
    const toggles = Array.from(document.querySelectorAll('[id="sidebar-toggle"]'));
    const closeButtons = Array.from(document.querySelectorAll('[id="sidebar-close"]'));
    const overlays = Array.from(document.querySelectorAll('[id="sidebar-overlay"]'));

    function isOpen(sidebar) {
        return !sidebar.classList.contains('hidden') && !sidebar.classList.contains('-translate-x-full');
    }

    function openSidebar(sidebar) {
        sidebar.classList.remove('hidden', '-translate-x-full');
        overlays.forEach(function (overlay) {
            overlay.classList.remove('hidden');
        });
    }

    function closeSidebar(sidebar) {
        if (sidebar.classList.contains('lg:flex')) {
            sidebar.classList.add('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
        }

        if (!sidebars.some(isOpen)) {
            overlays.forEach(function (overlay) {
                overlay.classList.add('hidden');
            });
        }
    }

    function targetsFor(element, candidates) {
        const index = candidates.indexOf(element);
        return candidates.length === sidebars.length && index !== -1 ? [sidebars[index]] : sidebars;
    }

    toggles.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            targetsFor(toggle, toggles).forEach(function (sidebar) {
                if (isOpen(sidebar)) {
                    closeSidebar(sidebar);
                } else {
                    openSidebar(sidebar);
                }
            });
        });
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            targetsFor(button, closeButtons).forEach(closeSidebar);
        });
    });

    overlays.forEach(function (overlay) {
        overlay.addEventListener('click', function () {
            targetsFor(overlay, overlays).forEach(closeSidebar);
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' || event.key === 'Esc') {
            sidebars.forEach(closeSidebar);
        }
    });

    document.querySelectorAll('[id="theme-toggle"]').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    });

    document.querySelectorAll('[data-confirm]').forEach(function (element) {
        const message = element.getAttribute('data-confirm') || 'Are you sure?';
        const eventName = element.tagName === 'FORM' ? 'submit' : 'click';

        element.addEventListener(eventName, function (event) {
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('.flash-message').forEach(function (message) {
        window.setTimeout(function () {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            window.setTimeout(function () {
                message.remove();
            }, 500);
        }, 5000);
    });
});
