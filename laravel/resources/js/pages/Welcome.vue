<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    canLogin?: boolean;
}

defineProps<Props>();
</script>

<template>
    <Head title="PESAT - Lhokseumawe Smart City">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    </Head>

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Navbar -->
        <nav class="flex items-center justify-between px-6 py-4 max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-xl font-bold tracking-tight">PESAT</span>
            </div>
            <div class="flex items-center gap-4">
                <Link href="/login" class="text-sm text-slate-300 hover:text-white transition-colors">Masuk</Link>
                <a href="#report"
                    class="bg-amber-500 hover:bg-amber-400 text-slate-900 px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    Lapor Sekarang
                </a>
            </div>
        </nav>

        <!-- Hero -->
        <section class="max-w-7xl mx-auto px-6 py-20 sm:py-32">
            <div class="max-w-3xl">
                <h1 class="text-4xl sm:text-6xl font-bold tracking-tight leading-tight">
                    Pantau & Lindungi
                    <span class="text-amber-400">Kota Lhokseumawe</span>
                </h1>
                <p class="mt-6 text-lg text-slate-400 leading-relaxed max-w-2xl">
                    Sistem pemantauan berbasis AI untuk mendeteksi pelanggaran Syariat Islam secara real-time.
                    Dukung Satpol PP dan WH dalam menjaga ketertiban umum melalui teknologi cerdas.
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="#report"
                        class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-900 px-6 py-3 rounded-lg font-semibold transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        Laporkan Pelanggaran
                    </a>
                    <Link href="/login"
                        class="inline-flex items-center gap-2 border border-slate-600 hover:border-slate-500 text-slate-300 px-6 py-3 rounded-lg font-semibold transition-colors">
                        Dashboard Petugas
                    </Link>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div class="bg-slate-900/50 border border-slate-800 rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold text-amber-400">AI</div>
                    <div class="mt-2 text-sm text-slate-400">Deteksi Real-time</div>
                </div>
                <div class="bg-slate-900/50 border border-slate-800 rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold text-amber-400">5s</div>
                    <div class="mt-2 text-sm text-slate-400">Interval Polling</div>
                </div>
                <div class="bg-slate-900/50 border border-slate-800 rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold text-amber-400">7+</div>
                    <div class="mt-2 text-sm text-slate-400">CCTV Terkoneksi</div>
                </div>
                <div class="bg-slate-900/50 border border-slate-800 rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold text-amber-400">5</div>
                    <div class="mt-2 text-sm text-slate-400">Kategori Pelanggaran</div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="max-w-7xl mx-auto px-6 py-16">
            <h2 class="text-2xl sm:text-3xl font-bold text-center">Fitur Utama</h2>
            <div class="mt-12 grid sm:grid-cols-3 gap-8">
                <div class="bg-slate-900/30 border border-slate-800 rounded-xl p-6">
                    <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold">Deteksi AI Otomatis</h3>
                    <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                        YOLO pose estimation + CNN classifier mendeteksi pelanggaran secara real-time dari CCTV.
                    </p>
                </div>
                <div class="bg-slate-900/30 border border-slate-800 rounded-xl p-6">
                    <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold">Partisipasi Masyarakat</h3>
                    <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                        Warga dapat melaporkan pelanggaran secara anonim melalui form publik terintegrasi.
                    </p>
                </div>
                <div class="bg-slate-900/30 border border-slate-800 rounded-xl p-6">
                    <div class="w-12 h-12 bg-amber-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold">Dashboard Real-time</h3>
                    <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                        Petugas WH memantau feed deteksi langsung dengan peta interaktif dan filter perangkat.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-800 mt-20">
            <div class="max-w-7xl mx-auto px-6 py-8 text-center text-sm text-slate-500">
                <p>&copy; {{ new Date().getFullYear() }} PESAT Lhokseumawe Smart City. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
