'use strict';

document.addEventListener('DOMContentLoaded', function () {
    const mode = document.getElementById('mode');
    const modeSatuan = document.getElementById('mode-satuan');
    const modeKelas = document.getElementById('mode-kelas');

    if (!mode || !modeSatuan || !modeKelas) {
        return;
    }

    function toggleMode() {
        const isSatuan = mode.value === 'satuan';
        const isKelas = mode.value === 'kelas';

        modeSatuan.classList.toggle('d-none', !isSatuan);
        modeKelas.classList.toggle('d-none', !isKelas);
    }

    mode.addEventListener('change', toggleMode);

    toggleMode();
});