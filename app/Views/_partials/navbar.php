<nav class="navbar custom-navbar fixed-top navbar-dark admin-topbar">
    <div class="container-fluid px-3">

        <!-- DESKTOP SIDEBAR TOGGLE -->
        <button
            type="button"
            class="btn admin-sidebar-toggle d-none d-lg-inline-flex"
            id="desktopSidebarToggle"
            aria-label="Toggle sidebar"
            title="Perkecil / Perbesar Sidebar">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>

        <!-- MOBILE MENU -->
        <button
            type="button"
            class="btn admin-mobile-toggle d-lg-none"
            data-bs-toggle="offcanvas"
            data-bs-target="#mobileSidebar"
            aria-controls="mobileSidebar"
            aria-label="Buka menu">
            <i class="bi bi-list"></i>
        </button>

        <!-- BRAND -->
        <a
            class="navbar-brand admin-brand fw-bold"
            href="<?= base_url('/admin/dashboard') ?>">
            <span class="brand-icon">
                <i class="bi bi-grid-1x2-fill"></i>
            </span>
            <span class="brand-text">ABSENSIKU</span>
            <span class="brand-section">ADMIN</span>
        </a>

        <!-- RIGHT -->
        <div class="ms-auto d-flex align-items-center gap-2">

            <!-- STATUS -->
            <div class="admin-top-status d-none d-md-flex">
                <span class="status-dot"></span>
                <span>Online</span>
            </div>

            <!-- PROFILE -->
            <div class="dropdown">

                <button
                    class="btn admin-profile-btn dropdown-toggle d-flex align-items-center"
                    type="button"
                    id="adminProfileMenu"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">

                    <span class="profile-avatar">
                        <i class="bi bi-person-fill"></i>
                    </span>

                    <span class="profile-info d-none d-md-flex">
                        <span class="profile-name">Administrator</span>
                        <span class="profile-role">Admin</span>
                    </span>
                </button>

                <ul
                    class="dropdown-menu dropdown-menu-end admin-profile-menu shadow"
                    aria-labelledby="adminProfileMenu">

                    <li class="dropdown-header">
                        <div class="fw-semibold">Administrator</div>
                        <small>Panel Administrasi</small>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a
                            class="dropdown-item"
                            href="<?= site_url('admin/account') ?>">
                            <i class="bi bi-person-circle"></i>
                            <span>Profil Saya</span>
                        </a>
                    </li>

                    <li>
                        <a
                            class="dropdown-item text-danger"
                            href="<?= site_url('auth/logout') ?>">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Keluar</span>
                        </a>
                    </li>

                </ul>

            </div>

        </div>

    </div>
</nav>

<script>
/* ============================================================
   5E.5 — EARLY SIDEBAR MINI LOCK
   Harus berjalan sebelum DOMContentLoaded.
   ============================================================ */
(function () {

    try {

        if (
            window.innerWidth >= 992 &&
            localStorage.getItem('absensiku_admin_sidebar_mini') === '1'
        ) {

            document.documentElement.classList.add(
                'absensiku-sidebar-mini-active'
            );

        }

    } catch (e) {}

})();

/* ============================================================
   ABSENSIKU ADMIN
   5E.9 — CLEAN MINI ACCORDION CONTROLLER
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    const STORAGE_KEY = 'absensiku_admin_sidebar_mini';

    const body = document.body;
    const toggle = document.getElementById('desktopSidebarToggle');
    const sidebar = document.querySelector('.sidebar');

    if (!sidebar) {
        return;
    }

    /*
     * ========================================================
     * 5E.10.13A — ACTIVE PARENT + SUBMENU ICON
     * ========================================================
     *
     * 1. Parent menjadi aktif jika salah satu submenu aktif.
     * 2. Icon submenu dibuat di browser.
     * 3. Tidak mengubah URL.
     * 4. Tidak mengubah Bootstrap collapse.
     * 5. Tidak mengubah mode mobile.
     * ========================================================
     */

    /*
     * --------------------------------------------------------
     * ACTIVE PARENT
     * --------------------------------------------------------
     */

    sidebar.querySelectorAll(
        '> ul > .nav-item'
    ).forEach(function (item) {

        const parentLink =
            item.querySelector(
                ':scope > .nav-link'
            );

        if (!parentLink) {
            return;
        }

        const activeSubmenu =
            item.querySelector(
                ':scope > .collapse .nav-link.active'
            );

        parentLink.classList.toggle(
            'parent-has-active',
            !!activeSubmenu
        );

    });


    /*
     * --------------------------------------------------------
     * ICON SUBMENU
     * --------------------------------------------------------
     */

    const submenuIcons = {

        'Tahun Pelajaran': 'bi-calendar3',
        'Data Jurusan': 'bi-diagram-3',
        'Kelas': 'bi-collection',
        'Guru': 'bi-person-badge',
        'Calon Siswa': 'bi-people',
        'Libur Sekolah': 'bi-calendar-x',

        'Daftar Mapping': 'bi-list-check',
        'Tambah Mapping': 'bi-plus-circle',

        'Data Siswa': 'bi-people',
        'Kenaikan Kelas': 'bi-arrow-up-circle',

        'Siswa': 'bi-person',
        'Kehadiran': 'bi-clock-history',
        'Laporan': 'bi-bar-chart',

        'Surat Izin': 'bi-envelope',
        'Notif Absensi': 'bi-bell',

        'Tampilkan Pesan': 'bi-chat-dots',
        'Buat Pesan': 'bi-chat-square-text',

        'Aplikasi': 'bi-grid',
        'Upload': 'bi-upload',
        'WhatsApp': 'bi-whatsapp',
        'QR Code': 'bi-qr-code',
        'License': 'bi-key'
    };


    sidebar.querySelectorAll(
        '.sidebar-mini-submenu > li > .nav-link'
    ).forEach(function (link) {

        /*
         * Jangan buat icon kedua.
         */

        if (
            link.querySelector(
                '.sidebar-submenu-icon'
            )
        ) {
            return;
        }

        /*
         * Ambil nama submenu tanpa icon.
         */

        const clone =
            link.cloneNode(true);

        clone.querySelectorAll('i').forEach(
            function (icon) {
                icon.remove();
            }
        );

        const label =
            clone.textContent
                .replace(/\s+/g, ' ')
                .trim();

        const iconName =
            submenuIcons[label];

        if (!iconName) {
            return;
        }

        const icon =
            document.createElement('i');

        icon.className =
            'bi ' +
            iconName +
            ' sidebar-submenu-icon';

        icon.setAttribute(
            'aria-hidden',
            'true'
        );

        link.insertBefore(
            icon,
            link.firstChild
        );

    });


    function isDesktop() {
        return window.innerWidth >= 992;
    }

    function isMini() {
        return body.classList.contains('sidebar-mini');
    }

    function closeMiniSubmenus() {

        sidebar
            .querySelectorAll(
                '.nav-item > .collapse.mini-accordion-open'
            )
            .forEach(function (submenu) {

                submenu.classList.remove(
                    'mini-accordion-open'
                );

                submenu.classList.remove('show');

                submenu.style.display = 'none';
                submenu.style.height = '';
                submenu.style.transition = '';
            });

    }

    function applyState(mini) {

        if (!isDesktop()) {

            body.classList.remove('sidebar-mini');

            return;
        }

        /*
         * BODY = satu-satunya state operasional sidebar.
         *
         * HTML EARLY LOCK hanya digunakan saat halaman pertama
         * kali dimuat untuk mencegah flash.
         *
         * Setelah controller aktif, class tersebut HARUS
         * mengikuti state body.
         */
        body.classList.toggle(
            'sidebar-mini',
            mini
        );

        const html = document.documentElement;

        html.classList.toggle(
            'absensiku-sidebar-mini-active',
            mini
        );

        if (mini) {

            closeMiniSubmenus();

        }

        if (toggle) {

            toggle.innerHTML = mini
                ? '<i class="bi bi-layout-sidebar-inset-reverse"></i>'
                : '<i class="bi bi-layout-sidebar-inset"></i>';

            toggle.setAttribute(
                'title',
                mini
                    ? 'Perbesar Sidebar'
                    : 'Perkecil Sidebar'
            );

        }

    }

    /*
     * --------------------------------------------------------
     * INITIAL STATE
     * --------------------------------------------------------
     */

    const savedState =
        localStorage.getItem(STORAGE_KEY) === '1';

    applyState(savedState);


    /*
     * --------------------------------------------------------
     * DESKTOP TOGGLE
     * --------------------------------------------------------
     */

    if (toggle) {

        toggle.addEventListener(
            'click',
            function (event) {

                if (!isDesktop()) {
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                const nextState = !isMini();

                localStorage.setItem(
                    STORAGE_KEY,
                    nextState ? '1' : '0'
                );

                applyState(nextState);

            },
            true
        );

    }


    /*
     * --------------------------------------------------------
     * MINI MODE PARENT MENU
     *
     * Bootstrap collapse tidak digunakan.
     * Submenu dikontrol langsung.
     * --------------------------------------------------------
     */

    sidebar
        .querySelectorAll(
            '.nav-item > a[data-bs-toggle="collapse"]'
        )
        .forEach(function (parentLink) {

            parentLink.addEventListener(
                'click',
                function (event) {

                    if (!isDesktop() || !isMini()) {
                        return;
                    }

                    event.preventDefault();
                    event.stopImmediatePropagation();

                    const targetSelector =
                        parentLink.getAttribute(
                            'href'
                        );

                    if (!targetSelector) {
                        return;
                    }

                    const submenu =
                        sidebar.querySelector(
                            targetSelector
                        );

                    if (!submenu) {
                        return;
                    }

                    const isOpen =
                        submenu.classList.contains(
                            'mini-accordion-open'
                        );

                    /*
                     * Tutup semua submenu lain.
                     */

                    closeMiniSubmenus();

                    /*
                     * Buka submenu yang diklik.
                     *
                     * DISPLAY langsung.
                     * Tidak memakai:
                     * - .collapsing
                     * - height animation
                     * - Bootstrap transition
                     */

                    if (!isOpen) {

                        submenu.classList.add(
                            'mini-accordion-open'
                        );

                        submenu.classList.remove(
                            'collapsing'
                        );

                        submenu.classList.add(
                            'show'
                        );

                        submenu.style.display =
                            'block';

                        submenu.style.height =
                            'auto';

                        submenu.style.transition =
                            'none';

                    }

                },
                true
            );

        });


    /*
     * --------------------------------------------------------
     * SUBMENU CLICK
     *
     * Jangan sampai navigasi membuat sidebar
     * kembali expanded.
     * --------------------------------------------------------
     */

    sidebar
        .querySelectorAll(
            '.collapse a'
        )
        .forEach(function (submenuLink) {

            submenuLink.addEventListener(
                'click',
                function () {

                    if (!isDesktop() || !isMini()) {
                        return;
                    }

                    /*
                     * Pertahankan state mini sebelum
                     * browser melakukan navigasi.
                     */

                    localStorage.setItem(
                        STORAGE_KEY,
                        '1'
                    );

                    body.classList.add(
                        'sidebar-mini'
                    );

                },
                true
            );

        });


    /*
     * --------------------------------------------------------
     * RESIZE
     * --------------------------------------------------------
     */

    window.addEventListener(
        'resize',
        function () {

            if (!isDesktop()) {

                body.classList.remove(
                    'sidebar-mini'
                );

                return;
            }

            const saved =
                localStorage.getItem(
                    STORAGE_KEY
                ) === '1';

            applyState(saved);

        }
    );

});


    /*
     * ========================================================
     * 5E.10.13D — HARD RESET DESKTOP TOGGLE
     * ========================================================
     *
     * Tujuan:
     *
     * EXPANDED  <->  COLLAPSED
     *
     * Tidak bergantung pada Bootstrap.
     * Tidak bergantung pada handler toggle lama.
     *
     * ========================================================
     */

    (function () {

        const oldToggle =
            document.getElementById('desktopSidebarToggle');

        if (!oldToggle) {
            return;
        }

        /*
         * ----------------------------------------------------
         * PUTUS SEMUA EVENT HANDLER LAMA
         * ----------------------------------------------------
         *
         * cloneNode() tidak membawa event listener.
         */

        const newToggle =
            oldToggle.cloneNode(true);

        oldToggle.parentNode.replaceChild(
            newToggle,
            oldToggle
        );


        /*
         * ----------------------------------------------------
         * TOGGLE BARU
         * ----------------------------------------------------
         */

        newToggle.addEventListener(
            'click',
            function (event) {

                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();


                /*
                 * Hanya desktop.
                 */

                if (window.innerWidth < 992) {
                    return;
                }


                const body =
                    document.body;

                const html =
                    document.documentElement;


                /*
                 * Baca kondisi SEKARANG.
                 */

                const currentlyMini =
                    body.classList.contains(
                        'sidebar-mini'
                    );


                /*
                 * Toggle ke kondisi berikutnya.
                 */

                const nextMini =
                    !currentlyMini;


                /*
                 * =================================================
                 * COLLAPSED
                 * =================================================
                 */

                if (nextMini) {

                    body.classList.add(
                        'sidebar-mini'
                    );

                    html.classList.add(
                        'absensiku-sidebar-mini-active'
                    );

                    try {

                        localStorage.setItem(
                            'absensiku_admin_sidebar_mini',
                            '1'
                        );

                    } catch (e) {}

                }


                /*
                 * =================================================
                 * EXPANDED
                 * =================================================
                 */

                else {

                    body.classList.remove(
                        'sidebar-mini'
                    );

                    html.classList.remove(
                        'absensiku-sidebar-mini-active'
                    );

                    try {

                        localStorage.setItem(
                            'absensiku_admin_sidebar_mini',
                            '0'
                        );

                    } catch (e) {}

                }


                /*
                 * ------------------------------------------------
                 * Pastikan Bootstrap tidak membawa class
                 * transisi lama pada sidebar.
                 * ------------------------------------------------
                 */

                const sidebar =
                    document.querySelector('.sidebar');

                if (sidebar) {

                    sidebar.classList.remove(
                        'collapsing'
                    );

                }


                /*
                 * Update aria state tombol.
                 */

                newToggle.setAttribute(
                    'aria-expanded',
                    nextMini ? 'false' : 'true'
                );

            },
            true
        );


        /*
         * ----------------------------------------------------
         * INITIAL STATE
         * ----------------------------------------------------
         *
         * Jangan memaksa state baru.
         * Ikuti localStorage yang sudah ada.
         * ----------------------------------------------------
         */

        try {

            if (
                window.innerWidth >= 992 &&
                localStorage.getItem(
                    'absensiku_admin_sidebar_mini'
                ) === '1'
            ) {

                document.body.classList.add(
                    'sidebar-mini'
                );

                document.documentElement.classList.add(
                    'absensiku-sidebar-mini-active'
                );

                newToggle.setAttribute(
                    'aria-expanded',
                    'false'
                );

            } else {

                newToggle.setAttribute(
                    'aria-expanded',
                    'true'
                );

            }

        } catch (e) {}

    })();

</script>
