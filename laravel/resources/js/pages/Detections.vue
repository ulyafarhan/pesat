<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
    initialLogs: Array,
    totalToday: Number,
    auth: Object,
});

const logs = ref(props.initialLogs || []);
const count = ref(props.totalToday || 0);
const searchQuery = ref('');
const selectedCategory = ref('');
const expandedId = ref(null);
let pollingInterval = null;
const seenLogIds = new Set((props.initialLogs || []).map((l) => l.id));

const categories = [
    'Pakaian Tidak Syar\'i', 'Khalwat', 'Celana Pendek', 'Pergaulan Bebas', 'Peringatan',
];

const filteredLogs = computed(() => {
    let list = logs.value;
    const q = searchQuery.value.toLowerCase().trim();
    if (q) {
        list = list.filter((l) =>
            (l.camera?.location_name || '').toLowerCase().includes(q) ||
            (l.camera_id || '').toLowerCase().includes(q) ||
            (l.label_detected || '').toLowerCase().includes(q)
        );
    }
    if (selectedCategory.value) {
        list = list.filter((l) => l.violation_category === selectedCategory.value);
    }
    return list;
});

function getCategoryClass(cat) {
    const map = {
        'Pakaian Tidak Syar\'i': 'bg-red-100 text-red-700 border-red-200',
        'Khalwat': 'bg-orange-100 text-orange-700 border-orange-200',
        'Celana Pendek': 'bg-blue-100 text-blue-700 border-blue-200',
        'Pergaulan Bebas': 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'Peringatan': 'bg-gray-100 text-gray-600 border-gray-200',
    };
    return map[cat] || 'bg-gray-100 text-secondary border-neutral-border';
}

function handleNewDetection(log) {
    if (seenLogIds.has(log.id)) return;
    seenLogIds.add(log.id);
    logs.value.unshift(log);
}

async function fetchLatestData() {
    const latestId = logs.value.length > 0 ? logs.value[0].id : 0;
    try {
        const r = await fetch(`/api/telemetry/latest?after_id=${latestId}`);
        if (r.ok) {
            const result = await r.json();
            if (result.status === 'success' && result.data.length > 0) {
                result.data.slice().reverse().forEach((log) => handleNewDetection(log));
            }
            if (result.meta?.total_today !== undefined) {
                count.value = result.meta.total_today;
            }
        }
    } catch (e) {
        console.error(e);
    }
}

onMounted(() => {
    pollingInterval = setInterval(fetchLatestData, 5000);
    if (window.Echo) {
        window.Echo.channel('pesat-telemetry').listen('.telemetry.updated', (e) => {
            if (e.log) handleNewDetection(e.log);
        });
    }
});

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
    if (window.Echo) window.Echo.leaveChannel('pesat-telemetry');
});
</script>

<template>
    <Head title="Riwayat Deteksi AI" />
    <DashboardLayout :auth="auth">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-sans text-sm font-bold tracking-wider text-primary uppercase">Riwayat Deteksi AI</h2>
                <span class="rounded-full border border-neutral-border bg-gray-100 px-2 py-0.5 font-sans text-[8px] font-bold tracking-widest text-secondary uppercase">{{ count }} Hari Ini</span>
            </div>

            <input v-model="searchQuery" type="text" placeholder="Cari kamera, lokasi, atau label..." class="w-full rounded-lg border border-neutral-border bg-white px-4 py-2.5 text-xs text-primary placeholder-gray-400 transition duration-150 focus:border-primary focus:outline-none" />

            <div class="flex flex-wrap gap-1.5">
                <button @click="selectedCategory = ''" class="rounded-full border px-2.5 py-1 font-sans text-[8px] font-bold tracking-wider uppercase transition duration-150"
                    :class="!selectedCategory ? 'bg-primary text-white border-primary' : 'border-neutral-border text-secondary hover:bg-gray-100'">Semua ({{ logs.length }})</button>
                <button v-for="cat in categories" :key="cat" @click="selectedCategory = selectedCategory === cat ? '' : cat"
                    class="rounded-full border px-2.5 py-1 font-sans text-[8px] font-bold tracking-wider uppercase transition duration-150"
                    :class="selectedCategory === cat ? getCategoryClass(cat) : 'border-neutral-border text-secondary hover:bg-gray-100'">{{ cat }}</button>
            </div>

            <div class="space-y-2">
                <div v-for="log in filteredLogs" :key="log.id" class="rounded-lg border border-neutral-border bg-white">
                    <button @click="expandedId = expandedId === log.id ? null : log.id" class="flex w-full items-center justify-between px-4 py-3 text-left transition duration-150 hover:bg-gray-50">
                        <div class="flex min-w-0 flex-1 items-center space-x-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="truncate font-sans text-xs font-bold text-primary">{{ log.camera?.location_name || log.camera_id }}</span>
                                    <span v-if="log.violation_category" class="shrink-0 rounded border px-1.5 py-0.5 font-sans text-[7px] font-bold tracking-wider uppercase" :class="getCategoryClass(log.violation_category)">{{ log.violation_category }}</span>
                                </div>
                                <p class="mt-0.5 truncate text-[10px] text-secondary">{{ log.label_detected || '-' }}</p>
                            </div>
                            <span class="shrink-0 text-right">
                                <span class="block font-mono text-xs font-bold" :class="log.confidence_score > 0.85 ? 'text-rose-600' : 'text-gray-600'">{{ (log.confidence_score * 100).toFixed(1) }}%</span>
                                <span class="block font-mono text-[9px] text-gray-400">{{ new Date(log.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}</span>
                            </span>
                        </div>
                        <svg class="ml-2 h-3 w-3 shrink-0 text-gray-400 transition duration-150" :class="expandedId === log.id ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div v-if="expandedId === log.id" class="border-t border-neutral-border p-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="flex aspect-video items-center justify-center overflow-hidden rounded-lg bg-gray-100">
                                <img :src="log.snapshot ? '/detections/snap/' + log.snapshot + '?t=' + new Date(log.created_at).getTime() : '/detections/' + log.camera_id + '?t=' + new Date(log.created_at).getTime()" class="h-full w-full object-cover" alt="Snapshot" @error="$event.target.style.display='none'" />
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-sans text-[9px] font-bold tracking-widest text-gray-400 uppercase">Kategori</span>
                                    <div class="mt-0.5"><span class="rounded border px-2 py-0.5 font-sans text-[10px] font-bold tracking-wider uppercase" :class="getCategoryClass(log.violation_category)">{{ log.violation_category || '-' }}</span></div>
                                </div>
                                <div>
                                    <span class="font-sans text-[9px] font-bold tracking-widest text-gray-400 uppercase">Label Deteksi</span>
                                    <p class="mt-0.5 font-sans text-xs text-primary">{{ log.label_detected || '-' }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <span class="font-sans text-[9px] font-bold tracking-widest text-gray-400 uppercase">Confidence</span>
                                        <p class="mt-0.5 font-mono text-base font-bold" :class="log.confidence_score > 0.85 ? 'text-rose-600' : 'text-primary'">{{ (log.confidence_score * 100).toFixed(1) }}%</p>
                                    </div>
                                    <div>
                                        <span class="font-sans text-[9px] font-bold tracking-widest text-gray-400 uppercase">Kamera</span>
                                        <p class="mt-0.5 font-mono text-xs text-primary">{{ log.camera_id }}</p>
                                    </div>
                                </div>
                                <div>
                                    <span class="font-sans text-[9px] font-bold tracking-widest text-gray-400 uppercase">Waktu</span>
                                    <p class="mt-0.5 font-mono text-xs text-primary">{{ new Date(log.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</p>
                                </div>
                                <div v-if="log.camera?.latitude && log.camera?.longitude">
                                    <span class="font-sans text-[9px] font-bold tracking-widest text-gray-400 uppercase">Lokasi</span>
                                    <p class="mt-0.5 font-sans text-xs text-primary">{{ log.camera.location_name }} ({{ log.camera.latitude }}, {{ log.camera.longitude }})</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="filteredLogs.length === 0" class="py-12 text-center font-sans text-[11px] text-gray-400">Tidak ada data deteksi AI.</div>
            </div>
        </div>
    </DashboardLayout>
</template>
