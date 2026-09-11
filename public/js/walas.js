'use strict';

document.addEventListener('DOMContentLoaded', function () {
    const toggleSidebar = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (!toggleSidebar || !sidebar || !overlay) {
        return;
    }

    toggleSidebar.addEventListener('click', function () {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
});