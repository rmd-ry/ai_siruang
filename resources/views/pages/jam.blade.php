@extends('layouts.app')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mx-4 md:mx-8">
        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
            {{-- Tombol Back --}}
            <a href="{{ route('ruangan.index') }}"
                class="group flex items-center justify-center w-10 h-10 rounded-xl flex-shrink-0
          border border-gray-200 bg-white text-gray-500
          hover:bg-blue-50 hover:text-black hover:border-secondary
          transition-200 shadow-sm">

                <i class="bi bi-arrow-left-short text-2xl group-hover:-translate-x-0.5 transition-transform"></i>
            </a>

            <h1 class="text-base sm:text-xl font-semibold min-w-0 break-words">
                Reservasi {{ $kelas->nama_kelas }} ({{ $kelas->lantai->nama_lantai }})
            </h1>
        </div>

        {{-- Form tanggal --}}
        <form action="{{ route('reservasi.create') }}" method="GET" class="flex-shrink-0">
            <input type="hidden" name="class_id" value="{{ $kelas->id }}">
            <input type="date" name="date" value="{{ $tanggal }}"
                class="w-full sm:w-auto border rounded-md p-2 text-sm cursor-pointer" onchange="this.form.submit()">
        </form>
    </div>


    {{-- Jam Grid --}}
    <div id="jamContainer" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-6 mt-10 mx-4 md:mx-8"></div>

    {{-- Summary Box --}}
    <div id="summaryBox"
        class="mt-10 mx-4 md:mx-8 bg-white rounded-xl shadow-md p-4 sm:p-6 hidden opacity-0 transition-opacity duration-300 border border-gray-100">

        <h3 class="font-semibold text-center mb-4">Detail Reservasi</h3>

        <div class="flex flex-wrap justify-center gap-4 md:gap-6 text-sm mb-6">
            <span class="bg-gray-100 px-3 py-1 rounded"><i class="bi bi-door-closed mr-2"></i>
                {{ $kelas->nama_kelas }}</span>
            <span class="bg-gray-100 px-3 py-1 rounded"><i class="bi bi-calendar3 mr-2"></i>
                {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</span>
            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded border border-blue-200">
                <i class="bi bi-clock mr-2"></i>
                <span id="summaryJamMulai" class="font-bold"></span> -
                <span id="summaryJamSelesai" class="font-bold"></span>
            </span>
        </div>

        <a id="btnLanjut" href="#"
            class="block text-center bg-success text-white w-full cursor-pointer hover:bg-green-700 transition-200 p-3 rounded-xl font-semibold shadow-md">
            Lanjut Isi Formulir
        </a>
    </div>

    <div class="mx-4 md:mx-8 mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md text-sm text-gray-700">
        <p class="leading-relaxed">
            <i class="bi bi-info-circle mr-1"></i>
            Klik <span class="font-semibold text-secondary">jam pertama</span> untuk waktu mulai, lalu klik
            <span class="font-semibold text-secondary">jam kedua</span> untuk waktu selesai.
        </p>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                // 1. DATA SETUP
                const bookedSlots = @json($bookedSlots ?? []);
                const jamList = @json($jamOperasional ?? []);

                const baseUrlForm = "{{ route('reservasi.form') }}";
                const classId = "{{ $kelas->id }}";
                const selectedDate = "{{ $tanggal }}";

                // 2. STATE VARIABLES
                let jamMulai = null;
                let jamSelesai = null;

                // 3. DOM ELEMENTS
                const jamContainer = document.getElementById("jamContainer");
                const summaryBox = document.getElementById("summaryBox");
                const btnLanjut = document.getElementById("btnLanjut");

                const summaryJamMulai = document.getElementById("summaryJamMulai");
                const summaryJamSelesai = document.getElementById("summaryJamSelesai");

                // 4. HELPER FUNCTIONS
                function isBooked(jam) {
                    const jamInt = parseInt(jam.split(':')[0]);
                    return bookedSlots.some(slot => {
                        const start = parseInt(slot.start.split(':')[0]);
                        const end = parseInt(slot.end.split(':')[0]);
                        return jamInt >= start && jamInt < end;
                    });
                }

                // --- UPDATE LOGIC LABEL ---
                function getLabel(jam, isDisabled, isBookedSlot, isSelected, isInRange) {
                    if (isBookedSlot) return "Terisi";
                    if (isDisabled) return "-";

                    // Request: Semua yang dipilih (range) jadi "Terpilih"
                    if (isSelected || isInRange) return "Terpilih";

                    return "Ajukan";
                }

                // 5. RENDER UTAMA
                function renderJam() {
                    if (!jamContainer) return;
                    jamContainer.innerHTML = "";

                    if (jamList.length === 0) {
                        jamContainer.innerHTML =
                            '<p class="col-span-full text-center text-gray-400">Jam operasional tidak tersedia.</p>';
                        return;
                    }

                    jamList.forEach((jam) => {
                        const card = document.createElement("div");
                        const booked = isBooked(jam);

                        // Logic Disable: Jika jam < jamMulai (dan jamSelesai belum dipilih)
                        const isBeforeStart = jamMulai && !jamSelesai && jam < jamMulai;
                        const isDisabled = booked || isBeforeStart;

                        const isSelected = jam === jamMulai || jam === jamSelesai;
                        const isInRange = jamMulai && jamSelesai && jam > jamMulai && jam < jamSelesai;

                        // --- STYLING CARD (CONTAINER) ---
                        // Tambahin class 'jam-card' biar bisa dideteksi click-outside
                        let cardClasses =
                            "jam-card bg-white p-4 rounded-xl shadow-sm text-center cursor-pointer transition border hover:shadow-md flex flex-col justify-between h-32";

                        if (isDisabled) {
                            cardClasses += " opacity-50 cursor-not-allowed bg-gray-50 border-gray-200";
                        } else if (isSelected || isInRange) {
                            // Semua yang terpilih (range) pake border hijau
                            cardClasses += " border-2 border-green-500 bg-green-50";
                        } else {
                            cardClasses += " border-gray-100 hover:border-green-400";
                        }

                        card.className = cardClasses;

                        // --- STYLING TOMBOL ---
                        let btnClass = "text-sm w-full p-2 rounded-lg transition-200 font-medium ";

                        if (isDisabled) {
                            btnClass += "bg-gray-300 text-gray-500 cursor-not-allowed";
                        } else if (isSelected || isInRange) {
                            // Semua yang terpilih (range) warnanya hijau solid
                            btnClass += "bg-green-600 text-white shadow-md font-bold";
                        } else {
                            // Default Tombol Ajukan
                            btnClass += "bg-success hover:bg-success text-white";
                        }

                        // --- EVENT LISTENER ---
                        if (!isDisabled) {
                            // Pake stopPropagation biar gak dianggap click outside
                            card.addEventListener("click", (e) => {
                                e.stopPropagation();
                                handleClick(jam);
                            });
                        }

                        // --- INJECT HTML ---
                        card.innerHTML = `
                    <div class="mb-3">
                        <p class="font-medium text-lg text-gray-800 flex items-center justify-center gap-2">
                            <i class="bi bi-clock-fill text-black"></i>
                            ${jam}
                        </p>
                    </div>

                    <div class="mt-auto">
                        <button class="${btnClass}">
                            ${getLabel(jam, isDisabled, booked, isSelected, isInRange)}
                        </button>
                    </div>
                `;
                        jamContainer.appendChild(card);
                    });
                }

                // 6. HANDLE CLICK
                function handleClick(jam) {
                    // Reset kalau klik Jam Mulai lagi
                    if (jam === jamMulai && !jamSelesai) {
                        jamMulai = null;
                        renderJam();
                        renderSummary();
                        return;
                    }

                    if (!jamMulai) {
                        jamMulai = jam;
                    } else if (!jamSelesai && jam > jamMulai) {
                        // Cek Tabrakan
                        let adaTabrakan = false;
                        const startInt = parseInt(jamMulai.split(':')[0]);
                        const endInt = parseInt(jam.split(':')[0]);

                        for (let i = startInt; i < endInt; i++) {
                            let checkJam = (i < 10 ? '0' : '') + i + ':00';
                            if (isBooked(checkJam)) adaTabrakan = true;
                        }

                        if (adaTabrakan) {
                            alert("Gabisa nyebrang jam yang udah dibooking orang lain, Brok!");
                            return;
                        }
                        jamSelesai = jam;
                    } else {
                        jamMulai = jam;
                        jamSelesai = null;
                    }
                    renderJam();
                    renderSummary();
                }

                // 7. RENDER SUMMARY
                function renderSummary() {
                    if (jamMulai && jamSelesai) {
                        if (summaryJamMulai) summaryJamMulai.textContent = jamMulai;
                        if (summaryJamSelesai) summaryJamSelesai.textContent = jamSelesai;

                        const url =
                            `${baseUrlForm}?class_id=${classId}&date=${selectedDate}&start=${jamMulai}&end=${jamSelesai}`;
                        btnLanjut.href = url;

                        summaryBox.classList.remove("hidden");
                        setTimeout(() => summaryBox.classList.remove("opacity-0"), 10);
                    } else {
                        summaryBox.classList.add("opacity-0");
                        setTimeout(() => summaryBox.classList.add("hidden"), 300);
                    }
                }

                // 🔥 8. FITUR CLICK OUTSIDE (RESET)
                document.addEventListener("click", (e) => {
                    // Jika yang diklik BUKAN kartu jam DAN BUKAN area summary box/tombol lanjut
                    // Maka reset pilihan
                    const isCard = e.target.closest(".jam-card");
                    const isSummary = e.target.closest("#summaryBox");
                    const isForm = e.target.closest("form"); // Biar klik ganti tanggal gak ngereset

                    if (!isCard && !isSummary && !isForm) {
                        if (jamMulai !== null) { // Cuma render ulang kalau emang ada yang dipilih
                            jamMulai = null;
                            jamSelesai = null;
                            renderJam();
                            renderSummary();
                        }
                    }
                });

                renderJam();
            });
        </script>
    @endpush
@endsection