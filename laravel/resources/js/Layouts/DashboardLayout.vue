<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    auth: Object,
});

const currentTime = ref(new Date());
let clockInterval = null;
const isSidebarOpen = ref(false);

function formatTime(date) {
    return date.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
}

function formatDate(date) {
    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function logout() {
    router.post('/logout');
}

onMounted(() => {
    clockInterval = setInterval(() => {
        currentTime.value = new Date();
    }, 1000);
});

onUnmounted(() => {
    if (clockInterval) {
        clearInterval(clockInterval);
    }
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-50 font-sans text-primary">
        <!-- TOP HEADER -->
        <header
            class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-neutral-border bg-white px-4 sm:px-6"
        >
            <div class="flex items-center space-x-3">
                <button
                    @click="isSidebarOpen = !isSidebarOpen"
                    class="text-secondary hover:text-primary focus:outline-none md:hidden"
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
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>
                <div class="flex items-center space-x-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm font-bold text-white"
                    >
                        P
                    </div>
                    <div>
                        <span
                            class="block text-sm font-bold tracking-tight text-primary"
                            >PESAT</span
                        >
                        <span
                            class="font-mono text-[9px] leading-none tracking-widest text-gray-400"
                            >PEUDONG SYARIAT</span
                        >
                    </div>
                </div>
            </div>

            <!-- Global Clock -->
            <div class="hidden flex-col items-center sm:flex">
                <span
                    class="font-mono text-sm font-semibold tracking-wide text-primary"
                    >{{ formatTime(currentTime) }}</span
                >
                <span
                    class="mt-0.5 text-[9px] tracking-wider text-gray-400 uppercase"
                    >{{ formatDate(currentTime) }}</span
                >
            </div>

            <!-- Profile and Logout -->
            <div class="flex items-center space-x-4">
                <div class="hidden flex-col text-right md:flex">
                    <span class="text-xs font-semibold text-primary">{{
                        auth?.user?.name || 'Petugas WH'
                    }}</span>
                    <span
                        class="mt-0.5 text-[9px] font-medium tracking-wider text-secondary uppercase"
                        >{{ auth?.user?.role || 'operator' }}</span
                    >
                </div>
                <template v-if="auth?.user?.role === 'admin'">
                    <a
                        href="/admin"
                        title="Kembali ke Admin Panel"
                        class="rounded-full border border-neutral-border bg-white p-2 text-secondary transition-all duration-150 hover:bg-primary hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                </template>
                <template v-else>
                    <button
                        @click="logout"
                        title="Keluar (Logout)"
                        class="rounded-full border border-neutral-border bg-white p-2 text-secondary transition-all duration-150 hover:bg-gray-50 hover:text-primary"
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
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                            />
                        </svg>
                    </button>
                </template>
            </div>
        </header>

        <!-- CONTAINER -->
        <div class="relative flex min-h-0 flex-1">
            <!-- SIDEBAR -->
            <aside
                class="fixed inset-y-0 left-0 z-30 w-64 transform border-r border-neutral-border bg-white pt-16 transition-transform duration-200 ease-in-out md:static md:translate-x-0 md:pt-0"
                :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="space-y-1 p-4">
                    <div
                        class="mb-4 px-4 text-[9px] font-semibold tracking-widest text-gray-400 uppercase"
                    >
                        Navigasi Utama
                    </div>

                    <Link
                        href="/dashboard"
                        class="flex items-center space-x-3 rounded-full px-4 py-2.5 text-xs font-medium transition-all duration-150"
                        :class="
                            $page.url === '/dashboard'
                                ? 'bg-primary text-white'
                                : 'text-secondary hover:bg-gray-100 hover:text-primary'
                        "
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
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"
                            />
                        </svg>
                        <span>AI Telemetri</span>
                    </Link>

                    <Link
                        href="/dashboard/detections"
                        class="flex items-center space-x-3 rounded-full px-4 py-2.5 text-xs font-medium transition-all duration-150"
                        :class="
                            $page.url.startsWith('/dashboard/detections')
                                ? 'bg-primary text-white'
                                : 'text-secondary hover:bg-gray-100 hover:text-primary'
                        "
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
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                            />
                        </svg>
                        <span>Riwayat Deteksi</span>
                    </Link>

                    <Link
                        href="/dashboard/reports"
                        class="flex items-center space-x-3 rounded-full px-4 py-2.5 text-xs font-medium transition-all duration-150"
                        :class="
                            $page.url.startsWith('/dashboard/reports')
                                ? 'bg-primary text-white'
                                : 'text-secondary hover:bg-gray-100 hover:text-primary'
                        "
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
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                        <span>Laporan Warga</span>
                    </Link>
                </div>
            </aside>

            <!-- MAIN PANEL CONTENT -->
            <main
                class="flex min-w-0 flex-1 flex-col"
                :class="
                    $page.url.startsWith('/dashboard/reports')
                        ? 'overflow-hidden p-0'
                        : 'overflow-y-auto p-4 sm:p-6'
                "
            >
                <slot />
            </main>
        </div>
    </div>
</template>
