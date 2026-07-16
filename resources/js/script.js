// ========================
// KELUAR components/logout-dropdown.blade.php
// ========================
document.addEventListener("DOMContentLoaded", () => {
    const logoutBtn = document.getElementById("logoutBtn");
    const logoutDropdown = document.getElementById("logoutDropdown");
    const cancelLogout = document.getElementById("cancelLogout");
    const wrapper = document.getElementById("logoutWrapper");

    let isOpen = false;

    function toggleDropdown(show) {
        isOpen = show;

        if (show) {
            // Hapus invisible dulu agar elemen ada di DOM
            logoutDropdown.classList.remove("invisible");

            // Beri sedikit delay agar browser merender transisi opacity & scale
            requestAnimationFrame(() => {
                logoutDropdown.classList.remove("opacity-0", "scale-95");
                logoutDropdown.classList.add("opacity-100", "scale-100");
            });
        } else {
            // Mulai transisi menghilang
            logoutDropdown.classList.remove("opacity-100", "scale-100");
            logoutDropdown.classList.add("opacity-0", "scale-95");

            // Tunggu transisi selesai (200ms) baru set invisible
            setTimeout(() => {
                if (!isOpen) {
                    // Cek lagi jaga-jaga user klik cepat
                    logoutDropdown.classList.add("invisible");
                }
            }, 200);
        }
    }

    // Toggle saat tombol diklik
    logoutBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleDropdown(!isOpen);
    });

    // Tombol Batal
    cancelLogout.addEventListener("click", () => {
        toggleDropdown(false);
    });

    // Klik di luar area
    document.addEventListener("click", (e) => {
        if (!wrapper.contains(e.target) && isOpen) {
            toggleDropdown(false);
        }
    });

    // Tombol ESC
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && isOpen) {
            toggleDropdown(false);
        }
    });
});

// ========================
// POPUP NOTIFIKASI components/notifikasi-popup.blade.php
// Bisa ada lebih dari satu instance di halaman (versi desktop & versi mobile),
// jadi tiap instance ditangani independen lewat class, bukan id.
// ========================
document.addEventListener("DOMContentLoaded", () => {
    const wrappers = document.querySelectorAll(".notif-wrapper");

    wrappers.forEach((wrapper) => {
        const notifBtn = wrapper.querySelector(".notif-btn");
        const notifDropdown = wrapper.querySelector(".notif-dropdown");
        let openNotif = false;

        if (!notifBtn || !notifDropdown) return;

        function toggleNotif() {
            openNotif = !openNotif;

            if (openNotif) {
                notifDropdown.classList.remove("hidden");
                setTimeout(() => notifDropdown.classList.add("opacity-100"), 10);
            } else {
                notifDropdown.classList.remove("opacity-100");
                setTimeout(() => notifDropdown.classList.add("hidden"), 200);
            }
        }

        // Klik tombol icon
        notifBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleNotif();
        });

        // Klik luar → tutup
        document.addEventListener("click", (e) => {
            if (!wrapper.contains(e.target) && openNotif) {
                toggleNotif();
            }
        });
    });
});

// ========================
// DROPRIGHT LANTAI pages/ruang.blade.php
// ========================
document.addEventListener("DOMContentLoaded", () => {
    const btnLantai = document.getElementById("btnLantai");
    const dropdownLantai = document.getElementById("dropdownLantai");
    const lantaiWrapper = document.getElementById("lantaiWrapper");

    let open = false;

    function toggleDropdown() {
        open = !open;

        if (open) {
            dropdownLantai.classList.remove("hidden");
            // Delay dikit biar transisi CSS jalan
            setTimeout(() => {
                dropdownLantai.classList.remove("opacity-0", "-translate-x-5");
                dropdownLantai.classList.add("opacity-100", "translate-x-0");
            }, 10);
        } else {
            dropdownLantai.classList.remove("opacity-100", "translate-x-0");
            dropdownLantai.classList.add("opacity-0", "-translate-x-5");

            setTimeout(() => dropdownLantai.classList.add("hidden"), 300);
        }
    }

    // Klik tombol lantai
    btnLantai.addEventListener("click", (e) => {
        e.stopPropagation();
        toggleDropdown();
    });

    // Klik luar dropdown → tutup
    document.addEventListener("click", (e) => {
        if (!lantaiWrapper.contains(e.target) && open) {
            toggleDropdown();
        }
    });
});
// ========================
// halaman form pages/form.blade.php
// ========================
document.addEventListener("DOMContentLoaded", () => {
    const btnBatal = document.getElementById("btn-batal");
    const popupBatal = document.getElementById("popup-batal");
    const batalTidak = document.getElementById("batal-tidak");

    if (!btnBatal || !popupBatal || !batalTidak) return;

    btnBatal.addEventListener("click", () => {
        popupBatal.classList.remove("hidden");
        popupBatal.classList.add("flex");
    });

    batalTidak.addEventListener("click", () => {
        popupBatal.classList.add("hidden");
        popupBatal.classList.remove("flex");
    });

    // Klik background tutup popup
    popupBatal.addEventListener("click", (e) => {
        if (e.target === popupBatal) {
            popupBatal.classList.add("hidden");
            popupBatal.classList.remove("flex");
        }
    });
});

        // ========================
        // HIDE PASSWORD (bisa dipakai di banyak field sekaligus: password, konfirmasi, dst)
        // Pasangkan lewat: <button class="toggle-password" data-target="ID_INPUT"><i class="toggle-password-icon bi bi-eye-slash"></i></button>
        // ========================
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".toggle-password").forEach((btn) => {
                const targetId = btn.dataset.target;
                const input = targetId ? document.getElementById(targetId) : null;
                const icon = btn.querySelector(".toggle-password-icon");

                if (!input || !icon) return;

                btn.addEventListener("click", () => {
                    const isPassword = input.type === "password";
                    input.type = isPassword ? "text" : "password";

                    icon.classList.toggle("bi-eye");
                    icon.classList.toggle("bi-eye-slash");
                });
            });
        });