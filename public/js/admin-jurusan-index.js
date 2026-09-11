'use strict';

(function () {
    const deleteLinks = document.querySelectorAll('[data-confirm-delete-jurusan]');

    Array.from(deleteLinks).forEach(function (link) {
        link.addEventListener('click', function (event) {
            if (!window.confirm('Hapus jurusan ini?')) {
                event.preventDefault();
            }
        });
    });
})();
