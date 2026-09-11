'use strict';

(function () {
    const jq = window.jQuery;

    if (!jq) {
        console.error('Admin Izin: jQuery tidak tersedia.');
        return;
    }

    const $ = jq.noConflict(true);

    $(document).ready(function () {
        const siswaSelect = $('#siswa_id');

        if (!siswaSelect.length) {
            return;
        }

        const cariSiswaUrl = siswaSelect.data('cari-url');

        if (!cariSiswaUrl) {
            console.error('Admin Izin: endpoint cari siswa tidak tersedia.');
            return;
        }

        siswaSelect.select2({
            width: '100%',
            placeholder: ' Cari berdasarkan Nama Siswa atau NIS...',
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: cariSiswaUrl,
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        term: params.term
                    };
                },

                processResults: function (response) {
                    return {
                        results: response.results
                    };
                },

                cache: true
            }
        });

        siswaSelect.on('select2:select', function (e) {
            const data = e.params.data;

            $('#infoKelas').text(data.kelas || '-');
            $('#infoJurusan').text(data.jurusan || '-');
            $('#siswa_akademik_id').val(data.siswa_akademik_id || '');
        });

        siswaSelect.on('select2:clear', function () {
            $('#infoKelas').text('-');
            $('#infoJurusan').text('-');
            $('#siswa_akademik_id').val('');
        });
    });
})();