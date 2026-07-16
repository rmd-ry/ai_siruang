{{-- Quick Booking Assistant: widget chat mengambang untuk cari & booking ruangan cepat --}}
<div id="assistantWidget" class="fixed bottom-5 right-5 z-[70]">

    {{-- Tombol Bubble --}}
    <button type="button" id="assistantToggle"
        class="w-14 h-14 rounded-full bg-secondary text-white shadow-lg flex items-center justify-center hover:bg-blue-700 transition duration-200">
        <i class="bi bi-robot text-2xl" id="assistantIconOpen"></i>
        <i class="bi bi-x-lg text-xl hidden" id="assistantIconClose"></i>
    </button>

    {{-- Panel Chat --}}
    <div id="assistantPanel"
        class="hidden opacity-0 translate-y-3 transition-all duration-200 absolute bottom-16 right-0
        w-[92vw] max-w-sm sm:w-96 h-[70vh] max-h-[520px] bg-white rounded-2xl shadow-2xl border border-gray-100
        flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="bg-secondary text-white px-4 py-3 flex items-center gap-2 flex-shrink-0">
            <i class="bi bi-robot text-lg"></i>
            <div class="min-w-0">
                <p class="font-semibold text-sm leading-tight">Asisten Booking</p>
                <p class="text-[11px] text-blue-100 leading-tight">Cari & booking ruangan cepat</p>
            </div>
        </div>

        {{-- List pesan --}}
        <div id="assistantMessages" class="flex-1 overflow-y-auto px-3 py-3 space-y-3 bg-gray-50">
            <div class="flex">
                <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-3 py-2 text-sm text-gray-700 max-w-[85%] shadow-sm">
                    Halo! Ketik kebutuhan ruangan kamu, misalnya:
                    <br>
                    <span class="italic text-gray-500">"carikan ruangan kosong besok jam 2 siang kapasitas 40"</span>
                </div>
            </div>
        </div>

        {{-- Input --}}
        <form id="assistantForm" class="flex-shrink-0 border-t border-gray-100 p-2 flex items-center gap-2 bg-white">
            <input type="text" id="assistantInput" autocomplete="off" maxlength="300"
                placeholder="Tulis kebutuhan ruangan..."
                class="flex-1 text-sm rounded-full border border-gray-200 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-secondary">
            <button type="submit" id="assistantSend"
                class="w-9 h-9 flex-shrink-0 rounded-full bg-success text-white flex items-center justify-center hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="bi bi-send-fill text-sm"></i>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const widget = document.getElementById('assistantWidget');
    if (!widget) return;

    const toggleBtn = document.getElementById('assistantToggle');
    const panel = document.getElementById('assistantPanel');
    const iconOpen = document.getElementById('assistantIconOpen');
    const iconClose = document.getElementById('assistantIconClose');
    const messages = document.getElementById('assistantMessages');
    const form = document.getElementById('assistantForm');
    const input = document.getElementById('assistantInput');
    const sendBtn = document.getElementById('assistantSend');

    let open = false;

    function togglePanel(show) {
        open = show;
        iconOpen.classList.toggle('hidden', show);
        iconClose.classList.toggle('hidden', !show);

        if (show) {
            panel.classList.remove('hidden');
            setTimeout(() => panel.classList.remove('opacity-0', 'translate-y-3'), 10);
            input.focus();
        } else {
            panel.classList.add('opacity-0', 'translate-y-3');
            setTimeout(() => panel.classList.add('hidden'), 200);
        }
    }

    toggleBtn.addEventListener('click', () => togglePanel(!open));

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function addUserBubble(text) {
        const div = document.createElement('div');
        div.className = 'flex justify-end';
        div.innerHTML = `
            <div class="bg-secondary text-white rounded-2xl rounded-br-sm px-3 py-2 text-sm max-w-[85%] shadow-sm break-words"></div>
        `;
        div.firstElementChild.textContent = text;
        messages.appendChild(div);
        scrollToBottom();
    }

    function addTypingBubble() {
        const div = document.createElement('div');
        div.id = 'assistantTyping';
        div.className = 'flex';
        div.innerHTML = `
            <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-3 py-2 text-sm text-gray-400 shadow-sm">
                <i class="bi bi-three-dots"></i> mengetik...
            </div>
        `;
        messages.appendChild(div);
        scrollToBottom();
    }

    function removeTypingBubble() {
        const el = document.getElementById('assistantTyping');
        if (el) el.remove();
    }

    function addAssistantBubble(reply, rooms) {
        const wrap = document.createElement('div');
        wrap.className = 'flex flex-col gap-2';

        const bubble = document.createElement('div');
        bubble.className = 'flex';
        bubble.innerHTML = `
            <div class="bg-white border border-gray-200 rounded-2xl rounded-bl-sm px-3 py-2 text-sm text-gray-700 max-w-[85%] shadow-sm break-words"></div>
        `;
        bubble.firstElementChild.textContent = reply;
        wrap.appendChild(bubble);

        (rooms || []).forEach((room) => {
            const card = document.createElement('div');
            card.className = 'bg-white border border-gray-200 rounded-xl p-3 shadow-sm max-w-[90%] ml-1';

            let jamInfo = '';
            if (room.jam_mulai && room.jam_selesai) {
                jamInfo = `<span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full"><i class="bi bi-clock"></i> ${room.jam_mulai} - ${room.jam_selesai}</span>`;
            } else if (room.slot_kosong && room.slot_kosong.length) {
                jamInfo = room.slot_kosong.map(s =>
                    `<span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full mr-1"><i class="bi bi-clock"></i> ${s}</span>`
                ).join('');
            }

            card.innerHTML = `
                <div class="flex justify-between items-start gap-2 mb-1">
                    <p class="font-semibold text-sm text-gray-800">${room.nama_kelas} <span class="text-xs text-gray-400 font-normal">(${room.nama_lantai})</span></p>
                </div>
                <p class="text-xs text-gray-500 mb-2"><i class="bi bi-people"></i> Kapasitas ${room.kapasitas}</p>
                <div class="mb-2">${jamInfo}</div>
                <a href="${room.link_booking}" class="block text-center text-xs font-semibold bg-success text-white py-1.5 rounded-lg hover:bg-green-700 transition">
                    Booking ruangan ini
                </a>
            `;
            wrap.appendChild(card);
        });

        messages.appendChild(wrap);
        scrollToBottom();
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        addUserBubble(text);
        input.value = '';
        sendBtn.disabled = true;
        addTypingBubble();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const res = await fetch('{{ route("assistant.tanya") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({ message: text }),
            });

            removeTypingBubble();

            if (!res.ok) {
                addAssistantBubble('Waduh, ada gangguan pas nyari data. Coba lagi sebentar ya.', []);
                return;
            }

            const data = await res.json();
            addAssistantBubble(data.reply, data.rooms);
        } catch (err) {
            removeTypingBubble();
            addAssistantBubble('Koneksi lagi bermasalah. Coba lagi ya.', []);
        } finally {
            sendBtn.disabled = false;
        }
    });
});
</script>
