<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
    initialLogs: Array,
    totalToday: Number,
    cameras: Array,
    auth: Object,
    edgeDeviceIds: Array,
});

const logs = ref([...props.initialLogs]);
const count = ref(props.totalToday);
const systemStatus = ref('INITIALIZING');
const mapContainer = ref(null);
const expandedLogId = ref(null);
const selectedDevice = ref('');
let map = null;
const markersMap = {};
let pollingInterval = null;
const seenLogIds = new Set(props.initialLogs.map((l) => l.id));

const filteredCameras = computed(() => {
    if (!selectedDevice.value) {
        return props.cameras || [];
    }
    return (props.cameras || []).filter((c) => c.edge_device_id === selectedDevice.value);
});

const activeCameras = computed(() => {
    return filteredCameras.value.filter((c) => c.is_active).length;
});

const criticalCount = computed(() => {
    return logs.value.filter((l) => l.confidence_score > 0.85).length;
});

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

function formatTime(date) {
    return new Date(date).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
}

async function initMap() {
    const L = await import('leaflet');
    await import('leaflet/dist/leaflet.css');

    if (!mapContainer.value) {
        return;
    }

    map = L.map(mapContainer.value, {
        zoomControl: false,
        attributionControl: false,
    }).setView([5.1802, 97.1507], 13);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    L.tileLayer(
        'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
        {
            maxZoom: 19,
        },
    ).addTo(map);

    if (filteredCameras.value) {
        filteredCameras.value.forEach((cam) => {
            const lastLog = logs.value.find((l) => l.camera_id === cam.id);
            const isCritical = lastLog && lastLog.confidence_score > 0.85;

            const icon = L.divIcon({
                className: 'pesat-marker',
                html: `<div class="marker-dot ${isCritical ? 'marker-critical' : 'marker-normal'}">
                    <div class="marker-ring"></div>
                </div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10],
            });

            const marker = L.marker([cam.latitude, cam.longitude], { icon })
                .addTo(map)
                .bindPopup(buildPopup(cam, lastLog));

            markersMap[cam.id] = marker;
        });
    }
}

function rebuildMarkers() {
    if (!map) return;

    Object.values(markersMap).forEach((m) => map.removeLayer(m));
    Object.keys(markersMap).forEach((k) => delete markersMap[k]);

    if (filteredCameras.value) {
        const L = window.L;
        if (!L) return;

        filteredCameras.value.forEach((cam) => {
            const lastLog = logs.value.find((l) => l.camera_id === cam.id);
            const isCritical = lastLog && lastLog.confidence_score > 0.85;

            const icon = L.divIcon({
                className: 'pesat-marker',
                html: `<div class="marker-dot ${isCritical ? 'marker-critical' : 'marker-normal'}">
                    <div class="marker-ring"></div>
                </div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10],
            });

            const marker = L.marker([cam.latitude, cam.longitude], { icon })
                .addTo(map)
                .bindPopup(buildPopup(cam, lastLog));

            markersMap[cam.id] = marker;
        });
    }
}

watch(selectedDevice, () => {
    rebuildMarkers();
});

function buildPopup(camera, log) {
    const detection = log
        ? `<div style="margin-top:6px;padding:4px 8px;background:${log.confidence_score > 0.85 ? '#fef2f2' : '#f0f9ff'};border-radius:4px;font-size:11px;">
            <strong>${log.label_detected}</strong> &mdash; ${(log.confidence_score * 100).toFixed(1)}%
           </div>`
        : '<div style="margin-top:6px;font-size:11px;color:#94a3b8;">Tidak ada deteksi</div>';

    const image = log
        ? `<img src="/detections/${camera.id}?t=${new Date().getTime()}" style="width:100%; height:110px; border-radius:4px; margin-top:8px; display:block; object-fit:cover; border: 1px solid #e2e8f0;" />`
        : '';

    return `<div style="font-family:Inter,system-ui,sans-serif;min-width:180px;color:#0f172a;">
        <div style="font-weight:700;font-size:12px;color:#0f172a;">${camera.location_name}</div>
        <div style="font-size:10px;color:#64748b;margin-top:2px;font-family:monospace;">${camera.id}</div>
        ${image}
        ${detection}
    </div>`;
}

async function updateMarker(log) {
    if (!map || !log.camera) {
        return;
    }

    const L = window.L || (await import('leaflet')).default;

    if (!L) {
        return;
    }

    const cameraId = log.camera_id;
    const isCritical = log.confidence_score > 0.85;

    if (markersMap[cameraId]) {
        map.removeLayer(markersMap[cameraId]);
    }

    const icon = L.divIcon({
        className: 'pesat-marker',
        html: `<div class="marker-dot ${isCritical ? 'marker-critical' : 'marker-normal'}">
            <div class="marker-ring"></div>
        </div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10],
    });

    const marker = L.marker([log.camera.latitude, log.camera.longitude], {
        icon,
    })
        .addTo(map)
        .bindPopup(buildPopup(log.camera, log));

    markersMap[cameraId] = marker;

    if (isCritical) {
        map.flyTo([log.camera.latitude, log.camera.longitude], 15, {
            duration: 0.8,
        });
        setTimeout(() => marker.openPopup(), 900);
    }
}

function handleNewDetection(log) {
    if (seenLogIds.has(log.id)) {
        return;
    }

    seenLogIds.add(log.id);

    logs.value.unshift(log);

    updateMarker(log);

    if (log.confidence_score > 0.85) {
        const audio = new Audio('/assets/sounds/alert.wav');
        audio.volume = 0.6;
        audio.play().catch(() => {});
    }

    if (logs.value.length > 30) {
        logs.value.pop();
    }
}

async function fetchLatestData() {
    const latestId = logs.value.length > 0 ? logs.value[0].id : 0;

    try {
        const response = await fetch(
            `/api/telemetry/latest?after_id=${latestId}`,
        );

        if (response.ok) {
            const result = await response.json();

            if (result.status === 'success' && result.data.length > 0) {
                const newLogs = [...result.data].reverse();
                newLogs.forEach((log) => handleNewDetection(log));
            }

            if (result.meta && typeof result.meta.total_today !== 'undefined') {
                count.value = result.meta.total_today;
            }

            systemStatus.value = 'ONLINE';
        } else {
            systemStatus.value = 'DEGRADED';
        }
    } catch (err) {
        console.error('Failed to poll latest telemetry logs:', err);
        systemStatus.value = 'DISCONNECTED';
    }
}

onMounted(async () => {
    await initMap();
    window.L = (await import('leaflet')).default;
    systemStatus.value = 'ONLINE';

    pollingInterval = setInterval(fetchLatestData, 5000);

    if (window.Echo) {
        window.Echo.channel('pesat-telemetry').listen(
            '.telemetry.updated',
            (e) => {
                if (e.log) {
                    handleNewDetection(e.log);
                }
            },
        );
    }
});

onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }

    if (window.Echo) {
        window.Echo.leaveChannel('pesat-telemetry');
    }

    if (map) {
        map.remove();
    }
});
</script>

<template>
    <Head title="Panel Telemetri AI" />
    <DashboardLayout :auth="auth">
        <div class="space-y-6">
            <!-- Device Filter -->
            <div v-if="edgeDeviceIds && edgeDeviceIds.length > 0" class="flex items-center space-x-3">
                <label class="font-sans text-xs font-semibold tracking-widest text-secondary uppercase">Perangkat Edge</label>
                <select
                    v-model="selectedDevice"
                    class="rounded-lg border border-neutral-border bg-white px-3 py-2 font-sans text-xs text-primary outline-none focus:ring-1 focus:ring-primary"
                >
                    <option value="">Semua Perangkat</option>
                    <option v-for="id in edgeDeviceIds" :key="id" :value="id">{{ id }}</option>
                </select>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div
                    class="flex items-center space-x-4 rounded-lg border border-neutral-border bg-white p-6"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-primary"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"
                            />
                        </svg>
                    </div>
                    <div>
                        <div
                            class="font-sans text-[9px] font-semibold tracking-widest text-gray-400 uppercase"
                        >
                            Deteksi Hari Ini
                        </div>
                        <div
                            class="mt-1 font-mono text-2xl font-bold text-primary"
                        >
                            {{ count }}
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center space-x-4 rounded-lg border border-neutral-border bg-white p-6"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-primary"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <div>
                        <div
                            class="font-sans text-[9px] font-semibold tracking-widest text-gray-400 uppercase"
                        >
                            Kamera Aktif
                        </div>
                        <div
                            class="mt-1 font-mono text-2xl font-bold text-primary"
                        >
                            {{ activeCameras }}
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center space-x-4 rounded-lg border border-neutral-border bg-white p-6"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-error"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                    </div>
                    <div>
                        <div
                            class="font-sans text-[9px] font-semibold tracking-widest text-gray-400 uppercase"
                        >
                            Peringatan Kritis
                        </div>
                        <div
                            class="mt-1 font-mono text-2xl font-bold text-error"
                        >
                            {{ criticalCount }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Map Panel -->
                <div
                    class="flex h-[500px] flex-col overflow-hidden rounded-lg border border-neutral-border bg-white lg:col-span-2"
                >
                    <div
                        class="flex items-center justify-between border-b border-neutral-border px-6 py-4"
                    >
                        <div class="flex items-center space-x-2">
                            <span
                                class="font-sans text-xs font-semibold tracking-widest text-primary uppercase"
                                >Peta Telemetri CCTV ({{ systemStatus }})</span
                            >
                        </div>
                    </div>
                    <div
                        ref="mapContainer"
                        class="min-h-0 flex-1 bg-gray-100"
                    ></div>
                </div>

                <!-- AI Feeds Ingestion Stream -->
                <div
                    class="flex h-[500px] flex-col overflow-hidden rounded-lg border border-neutral-border bg-white"
                >
                    <div
                        class="flex items-center justify-between border-b border-neutral-border px-6 py-4"
                    >
                        <div class="flex items-center space-x-2">
                            <span
                                class="font-sans text-xs font-semibold tracking-widest text-primary uppercase"
                                >Feed Deteksi AI</span
                            >
                        </div>
                        <span
                            class="font-mono font-sans text-[10px] text-gray-400"
                            >{{ logs.length }} Entri</span
                        >
                    </div>

                    <div class="flex-1 space-y-3 overflow-y-auto p-4">
                        <TransitionGroup name="feed">
                            <div
                                v-for="item in logs"
                                :key="item.id"
                                @click="
                                    expandedLogId =
                                        expandedLogId === item.id
                                            ? null
                                            : item.id
                                "
                                class="cursor-pointer rounded-lg border p-4 font-sans transition-all duration-300 hover:bg-gray-50/50"
                                :class="
                                    item.confidence_score > 0.85
                                        ? 'border-rose-200 bg-rose-50/50'
                                        : 'border-neutral-border bg-white'
                                "
                            >
                                <div
                                    class="mb-2 flex items-start justify-between"
                                >
                                    <div class="flex flex-wrap items-center gap-1.5 min-w-0">
                                        <span
                                            v-if="item.violation_category"
                                            class="rounded-full border px-2 py-0.5 font-sans text-[8px] font-bold tracking-wider uppercase whitespace-nowrap"
                                            :class="getCategoryClass(item.violation_category)"
                                        >
                                            {{ item.violation_category }}
                                        </span>
                                        <span
                                            class="rounded-full px-2.5 py-1 font-sans text-[10px] font-semibold tracking-wide uppercase truncate"
                                            :class="
                                                item.confidence_score > 0.85
                                                    ? 'bg-rose-100 text-rose-700'
                                                    : 'bg-gray-100 text-secondary'
                                            "
                                        >
                                            {{ item.label_detected }}
                                        </span>
                                    </div>
                                    <span
                                        class="font-mono text-[9px] text-gray-400 shrink-0 ml-2"
                                        >{{ formatTime(item.created_at) }}</span
                                    >
                                </div>
                                <div
                                    class="mb-2 flex items-center justify-between text-xs"
                                >
                                    <span class="font-semibold text-primary">{{
                                        item.camera
                                            ? item.camera.location_name
                                            : item.camera_id
                                    }}</span>
                                    <span
                                        class="font-mono font-bold"
                                        :class="
                                            item.confidence_score > 0.85
                                                ? 'text-error'
                                                : 'text-primary'
                                        "
                                    >
                                        {{
                                            (
                                                item.confidence_score * 100
                                            ).toFixed(1)
                                        }}%
                                    </span>
                                </div>
                                <div
                                    v-if="expandedLogId === item.id"
                                    class="mt-2 border-t border-gray-100 pt-2 transition-all duration-300"
                                >
                                    <img
                                        :src="
                                            '/detections/' +
                                            item.camera_id +
                                            '?t=' +
                                            new Date(item.created_at).getTime()
                                        "
                                        class="h-auto w-full rounded border border-gray-200 object-cover"
                                    />
                                </div>
                            </div>
                        </TransitionGroup>
                        <div
                            v-if="logs.length === 0"
                            class="py-12 text-center font-sans text-xs text-gray-400"
                        >
                            Menunggu data telemetri masuk...
                        </div>
                    </div>
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

.feed-enter-active,
.feed-leave-active {
    transition: all 0.4s ease;
}
.feed-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}
.feed-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>
