'use strict';

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('broadcastForm');

    if (!form) {
        return;
    }

    const jenisPesan = document.getElementById('jenis_pesan');

    const fieldPerorangan = document.getElementById('field_perorangan');
    const phoneInput = document.getElementById('phone');
    const studentIdInput = document.getElementById('student_id');
    const nisInput = document.getElementById('nis');

    const fieldKelas = document.getElementById('field_kelas');
    const kelasSelect = document.getElementById('kelas_id');

    const fieldTingkat = document.getElementById('field_tingkat');
    const tingkatSelect = document.getElementById('tingkat');

    const fieldJurusan = document.getElementById('field_jurusan');
    const jurusanSelect = document.getElementById('jurusan_id');

    const phoneUrl = form.dataset.phoneUrl;

    if (!jenisPesan) {
        return;
    }

    function resetField(field, input) {
        if (field) {
            field.classList.add('d-none');
        }

        if (input) {
            input.removeAttribute('required');
            input.value = '';
        }
    }

    function resetFormFields() {
        resetField(fieldPerorangan, phoneInput);

        if (studentIdInput) {
            studentIdInput.value = '';
        }

        if (nisInput) {
            nisInput.value = '';
        }

        resetField(fieldKelas, kelasSelect);
        resetField(fieldTingkat, tingkatSelect);
        resetField(fieldJurusan, jurusanSelect);
    }

    jenisPesan.addEventListener('change', function () {
        const jenis = this.value;

        resetFormFields();

        if (jenis === 'perorangan') {
            fieldPerorangan.classList.remove('d-none');
            phoneInput.setAttribute('required', 'required');

        } else if (jenis === 'kelas') {
            fieldKelas.classList.remove('d-none');
            kelasSelect.setAttribute('required', 'required');

        } else if (jenis === 'tingkat') {
            fieldTingkat.classList.remove('d-none');
            tingkatSelect.setAttribute('required', 'required');

        } else if (jenis === 'jurusan') {
            fieldJurusan.classList.remove('d-none');
            jurusanSelect.setAttribute('required', 'required');
        }
    });

    if (nisInput) {
        nisInput.addEventListener('change', function () {
            const nis = this.value.trim();

            if (nis.length < 4 || !phoneUrl) {
                return;
            }

            fetch(phoneUrl + '?nis=' + encodeURIComponent(nis))
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Server error: ' + response.status);
                    }

                    return response.json();
                })
                .then(function (data) {
                    if (data && data.wa_ortu) {
                        phoneInput.value = data.wa_ortu;
                        studentIdInput.value = data.student_id || '';
                    } else {
                        phoneInput.value = '';
                        studentIdInput.value = '';
                    }
                })
                .catch(function (error) {
                    console.error('Broadcast phone lookup error:', error);
                    phoneInput.value = '';
                    studentIdInput.value = '';
                });
        });
    }

});