<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
    pendingReports: Array,
    historyReports: Array,
    auth: Object,
});

const pendingList = ref([...props.pendingReports]);
const historyList = ref([...props.historyReports]);
const searchQuery = ref('');
const activeTab = ref('pending'); // 'pending' or 'history'
const selectedReport = ref(null);
const submitting = ref(false);
const windowWidth = ref(1024);
let pollingInterval = null;
const seenReportIds = new Set([
    ...props.pendingReports.map((r) => r.id),
    ...props.historyReports.map((r) => r.id),
]);

const verificationForm = ref({
    status: 'verified',
    verification_notes: '',
});

// Leaflet Map Variables
const mapRef = ref(null);
let mapInstance = null;
let markerInstance = null;
let L = null;

// Search filter
const filteredPending = computed(() => {
    const q = searchQuery.value.toLowerCase().trim();

    if (!q) {
        return pendingList.value;
    }

    return pendingList.value.filter(
        (r) =>
            r.location_name.toLowerCase().includes(q) ||
            r.id.toLowerCase().includes(q),
    );
});

const filteredHistory = computed(() => {
    const q = searchQuery.value.toLowerCase().trim();

    if (!q) {
        return historyList.value;
    }

    return historyList.value.filter(
        (r) =>
            r.location_name.toLowerCase().includes(q) ||
            (r.verification_notes &&
                r.verification_notes.toLowerCase().includes(q)) ||
            r.id.toLowerCase().includes(q),
    );
});

const activeList = computed(() => {
    return activeTab.value === 'pending'
        ? filteredPending.value
        : filteredHistory.value;
});

function selectReport(report) {
    selectedReport.value = report;
    verificationForm.value.status = 'verified';
    verificationForm.value.verification_notes = report.verification_notes || '';

    // Initialize or update mini map
    nextTick(() => {
        initMiniMap();
    });
}

async function initMiniMap() {
    if (
        !selectedReport.value ||
        !selectedReport.value.latitude ||
        !selectedReport.value.longitude
    ) {
        destroyMap();

        return;
    }

    try {
        if (!L) {
            L = await import('leaflet');
            await import('leaflet/dist/leaflet.css');
        }

        const lat = parseFloat(selectedReport.value.latitude);
        const lng = parseFloat(selectedReport.value.longitude);

        if (mapInstance) {
            mapInstance.setView([lat, lng], 15);

            if (markerInstance) {
                markerInstance.setLatLng([lat, lng]);
            } else {
                const icon = L.divIcon({
                    className: 'pesat-marker',
                    html: `<div class="marker-dot marker-critical"><div class="marker-ring"></div></div>`,
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                });
                markerInstance = L.marker([lat, lng], { icon }).addTo(
                    mapInstance,
                );
            }
        } else {
            if (!mapRef.value) {
                return;
            }

            mapInstance = L.map(mapRef.value, {
                zoomControl: false,
                attributionControl: false,
            }).setView([lat, lng], 15);

            L.tileLayer(
                'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
                {
                    maxZoom: 19,
                },
            ).addTo(mapInstance);

            const icon = L.divIcon({
                className: 'pesat-marker',
                html: `<div class="marker-dot marker-critical"><div class="marker-ring"></div></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10],
            });
            markerInstance = L.marker([lat, lng], { icon }).addTo(mapInstance);
        }
    } catch (e) {
        console.error('Gagal memuat peta mini Leaflet:', e);
    }
}

function destroyMap() {
    if (mapInstance) {
        mapInstance.remove();
        mapInstance = null;
        markerInstance = null;
    }
}

async function submitVerification() {
    if (!selectedReport.value) {
        return;
    }

    submitting.value = true;

    try {
        const response = await fetch(
            `/api/wh/reports/${selectedReport.value.id}/verify`,
            {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    status: verificationForm.value.status,
                    verification_notes:
                        verificationForm.value.verification_notes,
                }),
            },
        );

        if (response.ok) {
            const data = await response.json();

            // Remove from pending
            pendingList.value = pendingList.value.filter(
                (r) => r.id !== selectedReport.value.id,
            );

            // Add to history (avoid duplicates)
            historyList.value = historyList.value.filter(
                (r) => r.id !== data.data.id,
            );
            historyList.value.unshift(data.data);

            // Select next report in the list or deselect
            if (windowWidth.value >= 768 && activeList.value.length > 0) {
                selectReport(activeList.value[0]);
            } else {
                selectedReport.value = null;
                destroyMap();
            }
        }
    } catch (e) {
        console.error(e);
    } finally {
        submitting.value = false;
    }
}

async function quickVerify(status) {
    verificationForm.value.status = status;

    if (!verificationForm.value.verification_notes) {
        verificationForm.value.verification_notes =
            status === 'verified'
                ? 'Tindakan lapangan selesai'
                : 'Laporan tidak valid / ditolak';
    }

    await submitVerification();
}

// Watch for tab change to auto-select first report on desktop
watch(activeTab, () => {
    if (windowWidth.value >= 768 && activeList.value.length > 0) {
        selectReport(activeList.value[0]);
    } else {
        selectedReport.value = null;
        destroyMap();
    }
});

function parseLocation(locationName) {
    if (!locationName) {
        return { base: 'Lokasi Tidak Diketahui', gender: '', details: '' };
    }

    const parts = locationName.split(' - ');

    if (parts.length < 2) {
        return { base: parts[0], gender: '', details: '' };
    }

    const base = parts[0];
    const rest = parts.slice(1).join(' - ');
    let gender = '';
    let details = rest;
    const genderMatch = rest.match(/^\[(.*?)\]/);

    if (genderMatch) {
        gender = genderMatch[1];
        details = rest.replace(/^\[.*?\]\s*/, '');
    }

    return { base, gender, details };
}

function getGenderClass(gender) {
    if (!gender) {
        return '';
    }

    const g = gender.toLowerCase();

    if (g.includes('wanita') && g.includes('pria')) {
        return 'bg-amber-50 text-amber-700 border-amber-200';
    } else if (g.includes('wanita')) {
        return 'bg-purple-50 text-purple-700 border-purple-200';
    } else if (g.includes('pria')) {
        return 'bg-blue-50 text-blue-700 border-blue-200';
    }

    return 'bg-gray-50 text-gray-700 border-gray-200';
}

function getCategoryClass(category) {
    const map = {
        'Pakaian Tidak Syar\'i': 'bg-red-100 text-red-700 border-red-200',
        'Khalwat': 'bg-orange-100 text-orange-700 border-orange-200',
        'Celana Pendek': 'bg-blue-100 text-blue-700 border-blue-200',
        'Pergaulan Bebas': 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'Peringatan': 'bg-gray-100 text-gray-600 border-gray-200',
    };
    return map[category] || 'bg-gray-100 text-secondary border-neutral-border';
}

function getSourceLabel(source) {
    return source === 'ai_detection' ? 'Deteksi AI' : 'Masyarakat';
}

function getSourceClass(source) {
    return source === 'ai_detection'
        ? 'bg-purple-50 text-purple-700 border-purple-200'
        : 'bg-green-50 text-green-700 border-green-200';
}

function isVideo(path) {
    if (!path) {
        return false;
    }

    const ext = path.split('.').pop().toLowerCase();

    return ['mp4', 'webm', 'mov', 'avi', '3gp'].includes(ext);
}

function handleResize() {
    windowWidth.value = window.innerWidth;
}

function handleNewReport(report) {
    if (seenReportIds.has(report.id)) {
        return;
    }

    seenReportIds.add(report.id);

    if (report.status === 'pending') {
        const idx = pendingList.value.findIndex((r) => r.id === report.id);

        if (idx !== -1) {
            pendingList.value[idx] = report;

            if (selectedReport.value && selectedReport.value.id === report.id) {
                selectReport(report);
            }
        } else {
            pendingList.value.unshift(report);
        }

        const audio = new Audio('/assets/sounds/alert.wav');
        audio.volume = 0.8;
        audio.play().catch(() => {});

        if (
            windowWidth.value >= 768 &&
            !selectedReport.value &&
            activeTab.value === 'pending'
        ) {
            selectReport(report);
        }
    }
}

function handleReportUpdate(report) {
    pendingList.value = pendingList.value.filter((r) => r.id !== report.id);

    const indexInHistory = historyList.value.findIndex(
        (r) => r.id === report.id,
    );

    if (indexInHistory !== -1) {
        historyList.value[indexInHistory] = report;
    } else if (report.status === 'verified' || report.status === 'rejected') {
        historyList.value = historyList.value.filter((r) => r.id !== report.id);
        historyList.value.unshift(report);
    }

    if (selectedReport.value && selectedReport.value.id === report.id) {
        selectedReport.value = report;
        verificationForm.value.verification_notes =
            report.verification_notes || '';
    }
}

async function fetchLatestReports() {
    const latestId =
        pendingList.value.length > 0 ? pendingList.value[0].id : '';

    try {
        const response = await fetch(
            `/api/reports/latest?after_id=${latestId}`,
        );

        if (response.ok) {
            const result = await response.json();

            if (result.status === 'success' && result.data) {
                const newPendings = [...(result.data.pending || [])].reverse();
                newPendings.forEach((report) => handleNewReport(report));

                const updatedHistories = result.data.history || [];
                updatedHistories.forEach((report) => {
                    if (seenReportIds.has(report.id)) {
                        handleReportUpdate(report);
                    } else {
                        seenReportIds.add(report.id);
                        historyList.value.unshift(report);
                    }
                });
            }
        }
    } catch (err) {
        console.error('Failed to poll latest reports:', err);
    }
}

onMounted(() => {
    window.addEventListener('resize', handleResize);
    windowWidth.value = window.innerWidth;

    // Auto select first report on desktop
    if (windowWidth.value >= 768 && activeList.value.length > 0) {
        selectReport(activeList.value[0]);
    }

    // Start 5-second polling
    pollingInterval = setInterval(fetchLatestReports, 5000);

    // Hybrid fallback via Echo
    if (window.Echo) {
        window.Echo.channel('pesat-reports')
            .listen('.report.submitted', (e) => {
                if (e.report) {
                    handleNewReport(e.report);
                }
            })
            .listen('.report.updated', (e) => {
                if (e.report) {
                    handleReportUpdate(e.report);
                }
            });
    }
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);

    if (pollingInterval) {
        clearInterval(pollingInterval);
    }

    if (window.Echo) {
        window.Echo.leaveChannel('pesat-reports');
    }

    destroyMap();
});
</script>

<template>
    <Head title="Verifikasi Laporan Warga" />
    <DashboardLayout :auth="auth">
        <div
            class="flex h-full flex-row overflow-hidden bg-white font-sans antialiased"
        >
            <!-- LEFT COLUMN: LIST PANEL -->
            <div
                class="flex h-full w-full flex-shrink-0 flex-col border-r border-neutral-border bg-white md:w-80 lg:w-[350px] xl:w-[380px]"
                v-show="windowWidth >= 768 || !selectedReport"
            >
                <!-- Search & Filters -->
                <div
                    class="shrink-0 space-y-3 border-b border-neutral-border p-4"
                >
                    <div class="flex items-center justify-between">
                        <h2
                            class="font-sans text-sm font-bold tracking-wider text-primary uppercase"
                        >
                            Laporan Warga
                        </h2>
                        <span
                            class="rounded-full border border-neutral-border bg-gray-100 px-2 py-0.5 font-sans text-[8px] font-bold tracking-widest text-secondary uppercase"
                            >WH Officer</span
                        >
                    </div>

                    <!-- Search Input -->
                    <div class="relative">
                        <span
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                class="h-3.5 w-3.5 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari lokasi atau laporan..."
                            class="w-full rounded-full border border-neutral-border bg-gray-50 py-2 pr-4 pl-8 text-xs text-primary placeholder-gray-400 transition duration-150 focus:border-primary focus:bg-white focus:outline-none"
                        />
                    </div>

                    <!-- Segmented Control Tabs -->
                    <div
                        class="flex rounded-full border border-neutral-border/50 bg-gray-100 p-0.5"
                    >
                        <button
                            @click="activeTab = 'pending'"
                            class="flex-1 rounded-full py-1 text-center text-[10px] font-bold tracking-wider uppercase transition duration-150"
                            :class="
                                activeTab === 'pending'
                                    ? 'bg-white text-primary shadow-sm'
                                    : 'text-secondary hover:text-primary'
                            "
                        >
                            Antrean ({{ filteredPending.length }})
                        </button>
                        <button
                            @click="activeTab = 'history'"
                            class="flex-1 rounded-full py-1 text-center text-[10px] font-bold tracking-wider uppercase transition duration-150"
                            :class="
                                activeTab === 'history'
                                    ? 'bg-white text-primary shadow-sm'
                                    : 'text-secondary hover:text-primary'
                            "
                        >
                            Riwayat ({{ filteredHistory.length }})
                        </button>
                    </div>
                </div>

                <!-- Scrollable Cards List -->
                <div
                    class="flex-1 divide-y divide-gray-100 overflow-y-auto bg-white"
                >
                    <div
                        v-for="report in activeList"
                        :key="report.id"
                        @click="selectReport(report)"
                        class="flex cursor-pointer flex-col space-y-2 border-l-4 p-4 transition duration-150"
                        :class="
                            selectedReport?.id === report.id
                                ? 'border-primary bg-gray-50'
                                : 'border-transparent hover:bg-gray-50/30'
                        "
                    >
                        <div class="flex items-start justify-between space-x-2">
                            <div class="flex min-w-0 flex-1 flex-col space-y-1">
                                <span
                                    class="truncate font-sans text-xs font-bold text-primary"
                                    >{{
                                        parseLocation(report.location_name).base
                                    }}</span
                                >
                                <div
                                    class="flex flex-wrap items-center gap-1.5"
                                >
                                    <span
                                        v-if="report.source"
                                        class="rounded border px-1.5 py-0.5 font-sans text-[7px] font-bold tracking-wider uppercase"
                                        :class="getSourceClass(report.source)"
                                    >
                                        {{ getSourceLabel(report.source) }}
                                    </span>
                                    <span
                                        v-if="report.violation_category"
                                        class="rounded border px-1.5 py-0.5 font-sans text-[7px] font-bold tracking-wider uppercase"
                                        :class="getCategoryClass(report.violation_category)"
                                    >
                                        {{ report.violation_category }}
                                    </span>
                                    <span
                                        v-if="
                                            parseLocation(report.location_name)
                                                .gender
                                        "
                                        class="rounded border px-1.5 py-0.5 font-sans text-[8px] font-bold tracking-wider uppercase"
                                        :class="
                                            getGenderClass(
                                                parseLocation(
                                                    report.location_name,
                                                ).gender,
                                            )
                                        "
                                    >
                                        {{
                                            parseLocation(report.location_name)
                                                .gender
                                        }}
                                    </span>
                                    <span
                                        class="truncate text-[9px] font-light text-secondary"
                                    >
                                        {{
                                            parseLocation(report.location_name)
                                                .details ||
                                            'Pelanggaran terdeteksi'
                                        }}
                                    </span>
                                </div>
                            </div>
                            <span
                                v-if="report.status === 'pending'"
                                class="shrink-0 rounded-full border px-2 py-0.5 font-sans text-[8px] font-bold tracking-wider uppercase"
                                :class="
                                    report.is_break_dispatch
                                        ? 'border-rose-200 bg-rose-50 text-rose-700'
                                        : 'border-neutral-border bg-gray-50 text-secondary'
                                "
                            >
                                {{
                                    report.is_break_dispatch
                                        ? 'Prioritas'
                                        : 'Reguler'
                                }}
                            </span>
                            <span
                                v-else
                                class="shrink-0 rounded-full border px-2 py-0.5 font-sans text-[8px] font-bold tracking-wider uppercase"
                                :class="{
                                    'border-emerald-200 bg-emerald-50 text-emerald-700':
                                        report.status === 'verified',
                                    'border-rose-200 bg-rose-50 text-rose-700':
                                        report.status === 'rejected',
                                }"
                            >
                                {{
                                    report.status === 'verified'
                                        ? 'Valid'
                                        : 'Tolak'
                                }}
                            </span>
                        </div>
                        <div
                            class="flex items-center justify-between pt-1 font-mono text-[9px] text-gray-400"
                        >
                            <span>ID: {{ report.id.substring(0, 8) }}</span>
                            <span>{{
                                new Date(
                                    report.reported_at || report.created_at,
                                ).toLocaleTimeString('id-ID', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                })
                            }}</span>
                        </div>
                    </div>

                    <div
                        v-if="activeList.length === 0"
                        class="py-12 text-center font-sans text-[11px] text-gray-400"
                    >
                        Tidak ada laporan warga.
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: DETAIL PANEL -->
            <div
                class="flex h-full min-w-0 flex-1 flex-col bg-gray-50/50"
                v-show="windowWidth >= 768 || selectedReport"
            >
                <!-- Active Detail View -->
                <div
                    v-if="selectedReport"
                    class="flex min-h-0 flex-1 flex-col bg-white md:bg-transparent"
                >
                    <!-- Detail Header -->
                    <div
                        class="flex shrink-0 items-center justify-between border-b border-neutral-border bg-white px-6 py-4"
                    >
                        <div class="flex min-w-0 items-center space-x-3">
                            <button
                                @click="selectedReport = null"
                                class="shrink-0 rounded-full p-1.5 text-secondary transition duration-150 hover:bg-gray-100 hover:text-primary md:hidden"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                    />
                                </svg>
                            </button>
                            <div class="min-w-0">
                                <h3
                                    class="truncate font-sans text-xs font-bold tracking-wider text-primary uppercase"
                                >
                                    {{
                                        parseLocation(
                                            selectedReport.location_name,
                                        ).base
                                    }}
                                </h3>
                                <p
                                    class="mt-0.5 truncate font-mono text-[9px] text-gray-400"
                                >
                                    ID: {{ selectedReport.id }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span
                            v-if="selectedReport.status === 'pending'"
                            class="flex shrink-0 items-center space-x-1.5 rounded-full border px-3 py-1 font-sans text-[8px] font-bold tracking-wider uppercase"
                            :class="
                                selectedReport.is_break_dispatch
                                    ? 'border-rose-200 bg-rose-50 text-rose-700'
                                    : 'border-neutral-border bg-gray-50 text-secondary'
                            "
                        >
                            <span>{{
                                selectedReport.is_break_dispatch
                                    ? 'Prioritas: Auto-Escalated'
                                    : 'Menunggu Dispatch'
                            }}</span>
                        </span>
                        <span
                            v-else
                            class="flex shrink-0 items-center space-x-1.5 rounded-full border px-3 py-1 font-sans text-[8px] font-bold tracking-wider uppercase"
                            :class="{
                                'border-emerald-200 bg-emerald-50 text-emerald-700':
                                    selectedReport.status === 'verified',
                                'border-rose-200 bg-rose-50 text-rose-700':
                                    selectedReport.status === 'rejected',
                            }"
                        >
                            <span>{{
                                selectedReport.status === 'verified'
                                    ? 'Terverifikasi'
                                    : 'Ditolak'
                            }}</span>
                        </span>
                    </div>

                    <!-- Scrollable Content -->
                    <div
                        class="min-h-0 flex-grow space-y-6 overflow-y-auto p-6"
                    >
                        <div
                            class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2"
                        >
                            <!-- Left Block: Media & Map -->
                            <div class="space-y-6">
                                <!-- Media Box -->
                                <div
                                    class="flex flex-col overflow-hidden rounded-lg border border-neutral-border bg-white shadow-none"
                                >
                                    <div
                                        class="flex items-center justify-between border-b border-neutral-border bg-gray-50 px-4 py-2.5"
                                    >
                                        <span
                                            class="font-sans text-[9px] font-bold tracking-widest text-secondary uppercase"
                                            >Lampiran Bukti Media</span
                                        >
                                    </div>

                                    <div
                                        class="relative flex aspect-video items-center justify-center overflow-hidden bg-gray-100"
                                    >
                                        <template
                                            v-if="selectedReport.media_path"
                                        >
                                            <video
                                                v-if="
                                                    isVideo(
                                                        selectedReport.media_path,
                                                    )
                                                "
                                                :src="
                                                    '/media/' +
                                                    selectedReport.media_path
                                                "
                                                controls
                                                class="h-full w-full object-contain"
                                            ></video>
                                            <img
                                                v-else
                                                :src="
                                                    '/media/' +
                                                    selectedReport.media_path
                                                "
                                                class="h-full w-full object-cover transition duration-300 hover:scale-105"
                                                alt="Media Laporan"
                                            />
                                        </template>
                                        <div
                                            v-else
                                            class="flex flex-col items-center text-gray-400"
                                        >
                                            <svg
                                                class="mb-2 h-6 w-6"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                            <span class="font-sans text-[10px]"
                                                >Tidak ada berkas bukti</span
                                            >
                                        </div>
                                    </div>

                                    <div
                                        v-if="selectedReport.media_path"
                                        class="shrink-0 border-t border-neutral-border bg-white p-2.5 text-center"
                                    >
                                        <a
                                            :href="
                                                '/media/' +
                                                selectedReport.media_path
                                            "
                                            target="_blank"
                                            class="font-sans text-[10px] font-bold tracking-wider text-primary uppercase hover:underline"
                                            >Unduh / Buka Media</a
                                        >
                                    </div>
                                </div>

                                <!-- Leaflet Mini Map -->
                                <div
                                    class="flex flex-col overflow-hidden rounded-lg border border-neutral-border bg-white shadow-none"
                                >
                                    <div
                                        class="flex items-center justify-between border-b border-neutral-border bg-gray-50 px-4 py-2.5"
                                    >
                                        <span
                                            class="font-sans text-[9px] font-bold tracking-widest text-secondary uppercase"
                                            >Peta Lokasi Kejadian</span
                                        >
                                    </div>

                                    <div class="relative h-44 bg-gray-100">
                                        <div
                                            v-show="selectedReport.latitude"
                                            ref="mapRef"
                                            class="z-10 h-full w-full"
                                        ></div>
                                        <div
                                            v-if="!selectedReport.latitude"
                                            class="flex h-full w-full flex-col items-center justify-center text-gray-400"
                                        >
                                            <svg
                                                class="mb-2 h-6 w-6"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                            </svg>
                                            <span class="font-sans text-[10px]"
                                                >Koordinat GPS tidak
                                                disematkan</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Block: Information & Verification Form -->
                            <div class="space-y-6">
                                <!-- Report Metadata Card -->
                                <div
                                    class="space-y-4 rounded-lg border border-neutral-border bg-white p-6 shadow-none"
                                >
                                    <div class="space-y-3">
                                        <span
                                            class="block font-sans text-[8px] font-bold tracking-widest text-gray-400 uppercase"
                                            >Uraian Laporan Kejadian</span
                                        >
                                        <h4
                                            class="flex flex-wrap items-center gap-2 font-sans text-sm leading-snug font-bold text-primary"
                                        >
                                            {{
                                                parseLocation(
                                                    selectedReport.location_name,
                                                ).base
                                            }}
                                            <span
                                                v-if="selectedReport.source"
                                                class="rounded border px-2 py-0.5 font-sans text-[7px] font-bold tracking-wider uppercase"
                                                :class="getSourceClass(selectedReport.source)"
                                            >
                                                {{ getSourceLabel(selectedReport.source) }}
                                            </span>
                                            <span
                                                v-if="selectedReport.violation_category"
                                                class="rounded border px-2 py-0.5 font-sans text-[7px] font-bold tracking-wider uppercase"
                                                :class="getCategoryClass(selectedReport.violation_category)"
                                            >
                                                {{ selectedReport.violation_category }}
                                            </span>
                                            <span
                                                v-if="
                                                    parseLocation(
                                                        selectedReport.location_name,
                                                    ).gender
                                                "
                                                class="rounded border px-2 py-0.5 font-sans text-[8px] font-bold tracking-wider uppercase"
                                                :class="
                                                    getGenderClass(
                                                        parseLocation(
                                                            selectedReport.location_name,
                                                        ).gender,
                                                    )
                                                "
                                            >
                                                {{
                                                    parseLocation(
                                                        selectedReport.location_name,
                                                    ).gender
                                                }}
                                            </span>
                                        </h4>

                                        <div
                                            v-if="
                                                parseLocation(
                                                    selectedReport.location_name,
                                                ).details
                                            "
                                            class="space-y-1 border-t border-gray-100 pt-1.5"
                                        >
                                            <span
                                                class="block font-sans text-[8px] font-bold tracking-widest text-gray-400 uppercase"
                                                >Detail Pelanggaran
                                                Terdeteksi</span
                                            >
                                            <ul class="space-y-1 pt-1">
                                                <li
                                                    v-for="(
                                                        detail, idx
                                                    ) in parseLocation(
                                                        selectedReport.location_name,
                                                    ).details.split(' | ')"
                                                    :key="idx"
                                                    class="flex items-start space-x-1.5 font-sans text-[10px] text-primary"
                                                >
                                                    <span
                                                        class="mt-0.5 shrink-0 font-bold text-emerald-500"
                                                        >•</span
                                                    >
                                                    <span
                                                        class="leading-relaxed font-light"
                                                        >{{ detail }}</span
                                                    >
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div
                                        class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-4 font-sans text-[10px]"
                                    >
                                        <div>
                                            <span
                                                class="block font-light text-gray-400"
                                                >Waktu Kejadian</span
                                            >
                                            <span
                                                class="mt-0.5 block font-semibold text-primary"
                                                >{{
                                                    new Date(
                                                        selectedReport.reported_at ||
                                                            selectedReport.created_at,
                                                    ).toLocaleString('id-ID', {
                                                        weekday: 'long',
                                                        year: 'numeric',
                                                        month: 'long',
                                                        day: 'numeric',
                                                    })
                                                }}</span
                                            >
                                        </div>
                                        <div>
                                            <span
                                                class="block font-light text-gray-400"
                                                >Jam Ingesti</span
                                            >
                                            <span
                                                class="mt-0.5 block font-mono font-semibold text-primary"
                                                >{{
                                                    new Date(
                                                        selectedReport.reported_at ||
                                                            selectedReport.created_at,
                                                    ).toLocaleTimeString(
                                                        'id-ID',
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Form for Pending Reports -->
                                <div
                                    v-if="
                                        selectedReport.status.startsWith(
                                            'pending',
                                        )
                                    "
                                    class="space-y-4 rounded-lg border border-neutral-border bg-white p-6 shadow-none"
                                >
                                    <h4
                                        class="border-b border-gray-100 pb-3 font-sans text-[10px] font-bold tracking-widest text-primary uppercase"
                                    >
                                        Tindakan Lapangan WH Officer (1-Klik)
                                    </h4>

                                    <div class="space-y-4">
                                        <!-- Notes (Optional) -->
                                        <div class="space-y-2">
                                            <label
                                                class="block font-sans text-[8px] font-bold tracking-widest text-secondary uppercase"
                                                >Catatan Penertiban
                                                (Opsional)</label
                                            >
                                            <textarea
                                                v-model="
                                                    verificationForm.verification_notes
                                                "
                                                rows="2"
                                                placeholder="Masukkan detail tindakan atau alasan penolakan..."
                                                class="w-full rounded-lg border border-neutral-border bg-white px-4 py-3 font-sans text-xs text-primary placeholder-gray-400 focus:border-primary focus:outline-none"
                                            ></textarea>
                                        </div>

                                        <!-- Quick Action Buttons -->
                                        <div class="grid grid-cols-2 gap-3">
                                            <button
                                                type="button"
                                                @click="quickVerify('verified')"
                                                :disabled="submitting"
                                                class="cursor-pointer rounded-full bg-emerald-600 px-4 py-3 text-center font-sans text-[11px] font-bold tracking-wider text-white uppercase shadow-sm transition duration-150 hover:bg-emerald-700 hover:shadow-md disabled:opacity-50"
                                            >
                                                Valid / Tertibkan
                                            </button>
                                            <button
                                                type="button"
                                                @click="quickVerify('rejected')"
                                                :disabled="submitting"
                                                class="cursor-pointer rounded-full bg-rose-600 px-4 py-3 text-center font-sans text-[11px] font-bold tracking-wider text-white uppercase shadow-sm transition duration-150 hover:bg-rose-700 hover:shadow-md disabled:opacity-50"
                                            >
                                                Tolak Laporan
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Log / Verification Result Card for Completed Reports -->
                                <div
                                    v-else
                                    class="space-y-4 rounded-lg border border-neutral-border bg-white p-6 shadow-none"
                                >
                                    <h4
                                        class="border-b border-gray-100 pb-3 font-sans text-[10px] font-bold tracking-widest text-primary uppercase"
                                    >
                                        Hasil Tindakan Petugas
                                    </h4>

                                    <div class="space-y-4">
                                        <div
                                            class="flex items-center space-x-3 text-xs"
                                        >
                                            <div
                                                class="flex h-7 w-7 items-center justify-center rounded-full border border-neutral-border bg-gray-100 font-sans font-bold text-primary"
                                            >
                                                {{
                                                    selectedReport.verifier
                                                        ? selectedReport.verifier.name.charAt(
                                                              0,
                                                          )
                                                        : 'P'
                                                }}
                                            </div>
                                            <div>
                                                <span
                                                    class="block font-sans font-semibold text-primary"
                                                    >{{
                                                        selectedReport.verifier
                                                            ? selectedReport
                                                                  .verifier.name
                                                            : 'Petugas Lapangan'
                                                    }}</span
                                                >
                                                <span
                                                    class="font-mono text-[8px] tracking-wider text-gray-400 uppercase"
                                                    >{{
                                                        selectedReport.verifier
                                                            ? selectedReport
                                                                  .verifier.role
                                                            : 'wh_officer'
                                                    }}</span
                                                >
                                            </div>
                                        </div>

                                        <div
                                            class="space-y-1.5 rounded-lg border border-neutral-border bg-gray-50 p-4 font-sans text-[11px] text-secondary"
                                        >
                                            <strong
                                                class="block text-[9px] font-bold tracking-wider text-primary uppercase"
                                                >Laporan Verifikasi
                                                Resmi:</strong
                                            >
                                            <p
                                                class="leading-relaxed font-light text-primary"
                                            >
                                                {{
                                                    selectedReport.verification_notes ||
                                                    'Tidak ada catatan verifikasi tertulis.'
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Blank state -->
                <div
                    v-else
                    class="flex flex-grow flex-col items-center justify-center p-8 text-gray-400 select-none"
                >
                    <div
                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-full border border-neutral-border bg-white text-gray-300 shadow-none"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    </div>
                    <h4
                        class="mb-1 font-sans text-xs font-bold tracking-wider text-primary uppercase"
                    >
                        Belum Ada Laporan Terpilih
                    </h4>
                    <p
                        class="max-w-xs text-center font-sans text-[10px] leading-relaxed font-light text-secondary"
                    >
                        Pilih laporan dari daftar di sebelah kiri untuk meninjau
                        detail pengaduan, bukti media, koordinat GPS, dan
                        memberikan verifikasi tindakan.
                    </p>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style>
.pesat-marker {
    background: transparent;
    border: none;
}
.marker-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2.5px solid #ffffff;
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}
.marker-normal {
    background-color: #121317;
}
.marker-critical {
    background-color: #d93025;
}
.marker-ring {
    position: absolute;
    width: 32px;
    height: 32px;
    border: 2px solid currentColor;
    border-radius: 50%;
    top: -11px;
    left: -11px;
    opacity: 0;
    animation: marker-pulse 2s infinite;
}
.marker-normal .marker-ring {
    color: #121317;
}
.marker-critical .marker-ring {
    color: #d93025;
}

@keyframes marker-pulse {
    0% {
        transform: scale(0.4);
        opacity: 0.8;
    }
    100% {
        transform: scale(1.2);
        opacity: 0;
    }
}
</style>
