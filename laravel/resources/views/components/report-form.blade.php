<section id="report" class="max-w-3xl mx-auto bg-white border border-neutral-border rounded-lg p-8 sm:p-12 space-y-8 shadow-none" x-data="reportForm()">
    <div class="space-y-2">
        <h2 class="text-2xl sm:text-3xl font-normal text-primary tracking-tight font-sans">Layanan Pengaduan Masyarakat</h2>
        <p class="text-secondary text-xs sm:text-sm leading-relaxed font-sans font-light">Laporkan tindakan pelanggaran secara aman dan instan. Laporan Anda akan diproses sesuai dengan alur operasional wilayah.</p>
    </div>

    <!-- Notifications -->
    <div x-show="successMessage" x-transition class="p-4 bg-emerald-50/50 border border-emerald-200 text-emerald-700 rounded-lg text-xs font-sans" x-text="successMessage"></div>
    <div x-show="errorMessage" x-transition class="p-4 bg-rose-50/50 border border-rose-200 text-rose-700 rounded-lg text-xs font-sans" x-text="errorMessage"></div>

    <form @submit.prevent="submitReport" class="space-y-6">
        <!-- Location Name -->
        <div class="space-y-2">
            <label class="block text-xs font-semibold text-secondary font-sans">DESKRIPSI LOKASI KEJADIAN</label>
            <input type="text" x-model="form.location_name" required placeholder="Contoh: Depan Taman Riyadhah Lhokseumawe"
                class="w-full bg-white border border-neutral-border rounded-full px-6 py-3.5 text-sm text-primary placeholder-gray-400 focus:outline-none focus:border-primary transition-all duration-200">
        </div>

        <!-- Coordinates -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-secondary font-sans">LATITUDE</label>
                <input type="number" step="any" x-model="form.latitude" readonly placeholder="Latitude otomatis"
                    class="w-full bg-gray-50 border border-neutral-border rounded-full px-6 py-3.5 text-sm text-secondary focus:outline-none cursor-not-allowed">
            </div>
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-secondary font-sans">LONGITUDE</label>
                <input type="number" step="any" x-model="form.longitude" readonly placeholder="Longitude otomatis"
                    class="w-full bg-gray-50 border border-neutral-border rounded-full px-6 py-3.5 text-sm text-secondary focus:outline-none cursor-not-allowed">
            </div>
        </div>

        <!-- GPS Capture Button -->
        <div>
            <button type="button" @click="getLocation" class="w-full sm:w-auto px-6 py-3 bg-white hover:bg-gray-50 border border-neutral-border text-primary text-xs font-semibold rounded-full transition duration-200 flex items-center justify-center space-x-2">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-text="locating ? 'Mengambil Koordinat...' : 'Deteksi Lokasi GPS Anda'"></span>
            </button>
        </div>

        <!-- Media Attachments -->
        <div class="space-y-4 pt-6 border-t border-neutral-border">
            <label class="block text-xs font-semibold text-secondary font-sans">BUKTI FOTO / VIDEO</label>
            
            <div class="flex flex-wrap gap-3">
                <button type="button" @click="startCamera" x-show="!cameraActive" class="px-6 py-3 bg-white hover:bg-gray-50 border border-neutral-border text-primary text-xs font-semibold rounded-full transition duration-200">
                    Buka Kamera Perangkat
                </button>
                <button type="button" @click="capturePhoto" x-show="cameraActive" class="px-6 py-3 bg-primary hover:bg-secondary text-white text-xs font-semibold rounded-full transition duration-200">
                    Ambil Gambar
                </button>
                <button type="button" @click="stopCamera" x-show="cameraActive" class="px-6 py-3 bg-white border border-neutral-border text-secondary hover:text-primary text-xs font-semibold rounded-full transition duration-200">
                    Tutup Kamera
                </button>
            </div>

            <!-- Webcam Player -->
            <div x-show="cameraActive" class="w-full max-w-md border border-neutral-border rounded-lg overflow-hidden bg-black aspect-video relative">
                <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
            </div>

            <!-- Photo Preview -->
            <div x-show="capturedImage" class="space-y-2">
                <span class="text-[10px] tracking-wider uppercase bg-gray-100 text-secondary px-2.5 py-1 rounded-full font-semibold border border-neutral-border font-sans">Hasil Kamera</span>
                <img :src="capturedImage" class="w-full max-w-md border border-neutral-border rounded-lg object-cover aspect-video">
            </div>

            <!-- Manual File Upload -->
            <div class="space-y-2">
                <label class="block text-xs text-gray-500 font-sans">Atau unggah file secara manual (max 20MB)</label>
                <input type="file" @change="handleFileUpload" accept="image/*,video/*"
                    class="block w-full text-xs text-secondary file:mr-4 file:py-2.5 file:px-5 file:rounded-full file:border file:border-neutral-border file:text-xs file:font-semibold file:bg-white file:text-primary hover:file:bg-gray-50 transition file:cursor-pointer">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-neutral-border">
            <button type="submit" :disabled="submitting" class="w-full py-4 bg-primary hover:bg-secondary text-white font-bold rounded-full transition duration-200 disabled:opacity-50 text-center text-xs tracking-wider uppercase">
                <span x-text="submitting ? 'Mengirim Laporan...' : 'Kirim Laporan Resmi'"></span>
            </button>
        </div>
    </form>

    <template x-if="myReports.length > 0">
        <div class="mt-12 space-y-4 pt-8 border-t border-neutral-border">
            <h3 class="text-xs font-semibold text-secondary tracking-widest uppercase font-sans">STATUS LAPORAN ANDA</h3>
            <div class="space-y-4">
                <template x-for="rep in myReports" :key="rep.id">
                    <div class="p-6 bg-white border border-neutral-border rounded-lg space-y-4 hover:border-gray-300 transition-colors duration-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-sm font-semibold text-primary" x-text="rep.location_name"></h4>
                                <p class="text-[10px] text-gray-400 font-mono mt-1" x-text="new Date(rep.reported_at || rep.created_at).toLocaleString('id-ID')"></p>
                            </div>
                            <div>
                                <span class="text-[10px] px-3 py-1 rounded-full font-semibold border"
                                    :class="{
                                        'bg-rose-50 text-rose-700 border-rose-200': rep.status === 'pending' && rep.is_break_dispatch,
                                        'bg-gray-50 text-secondary border-neutral-border': rep.status === 'pending' && !rep.is_break_dispatch,
                                        'bg-emerald-50 text-emerald-700 border-emerald-200': rep.status === 'verified',
                                        'bg-rose-50 text-rose-700 border-rose-200': rep.status === 'rejected'
                                    }"
                                    x-text="rep.status === 'pending' ? (rep.is_break_dispatch ? 'Prioritas (Auto-Routed)' : 'Menunggu Verifikasi') : (rep.status === 'verified' ? 'Terverifikasi' : 'Ditolak')">
                                </span>
                            </div>
                        </div>
                        <div class="text-xs text-secondary font-mono" x-text="rep.latitude && rep.longitude ? 'GPS: ' + parseFloat(rep.latitude).toFixed(5) + ', ' + parseFloat(rep.longitude).toFixed(5) : 'Tidak ada koordinat GPS'"></div>
                        <template x-if="rep.verification_notes">
                            <div class="mt-2 p-4 bg-gray-50 border border-neutral-border rounded-lg text-xs text-secondary">
                                <span class="font-semibold text-primary block mb-1">Catatan Respon Petugas:</span>
                                <span x-text="rep.verification_notes"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </template>
</section>

<!-- ALPINE SCRIPT -->
<script>
    function reportForm() {
        return {
            form: {
                location_name: '',
                latitude: '',
                longitude: ''
            },
            myReports: [],
            locating: false,
            cameraActive: false,
            stream: null,
            capturedImage: null,
            file: null,
            submitting: false,
            successMessage: '',
            errorMessage: '',

            init() {
                const stored = localStorage.getItem('my_reports');
                if (stored) {
                    try {
                        this.myReports = JSON.parse(stored);
                    } catch (e) {}
                }
                const checkEcho = setInterval(() => {
                    if (window.Echo) {
                        clearInterval(checkEcho);
                        window.Echo.channel('pesat-reports')
                            .listen('.report.updated', (e) => {
                                const index = this.myReports.findIndex(r => r.id === e.report.id);
                                if (index !== -1) {
                                    this.myReports[index] = e.report;
                                    localStorage.setItem('my_reports', JSON.stringify(this.myReports));
                                }
                            });
                    }
                }, 500);
            },

            async getLocation() {
                this.locating = true;
                this.errorMessage = '';
                if (!navigator.geolocation) {
                    this.errorMessage = 'Geolocation tidak didukung oleh browser Anda.';
                    this.locating = false;
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.form.latitude = position.coords.latitude;
                        this.form.longitude = position.coords.longitude;
                        this.locating = false;
                    },
                    (error) => {
                        this.errorMessage = 'Gagal mendapatkan lokasi. Pastikan izin lokasi aktif.';
                        this.locating = false;
                    }
                );
            },

            async startCamera() {
                this.errorMessage = '';
                this.capturedImage = null;
                this.file = null;
                try {
                    this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    this.cameraActive = true;
                    this.$nextTick(() => {
                        const video = document.getElementById('webcam');
                        if (video) video.srcObject = this.stream;
                    });
                } catch (err) {
                    this.errorMessage = 'Gagal mengakses kamera perangkat.';
                }
            },

            capturePhoto() {
                const video = document.getElementById('webcam');
                if (!video) return;

                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                this.capturedImage = canvas.toDataURL('image/jpeg');
                this.stopCamera();

                canvas.toBlob((blob) => {
                    this.file = new File([blob], 'snapshot.jpg', { type: 'image/jpeg' });
                }, 'image/jpeg');
            },

            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                }
                this.cameraActive = false;
                this.stream = null;
            },

            handleFileUpload(e) {
                this.capturedImage = null;
                const file = e.target.files[0];
                if (file) {
                    this.file = file;
                }
            },

            async submitReport() {
                this.submitting = true;
                this.successMessage = '';
                this.errorMessage = '';

                const formData = new FormData();
                formData.append('location_name', this.form.location_name);
                formData.append('latitude', this.form.latitude);
                formData.append('longitude', this.form.longitude);
                if (this.file) {
                    formData.append('media', this.file);
                }

                try {
                    const response = await fetch('/api/reports', {
                        method: 'POST',
                        body: formData
                    });
                    const resData = await response.json();
                    if (response.ok) {
                        this.successMessage = 'Laporan berhasil terkirim dan segera diverifikasi.';
                        if (resData.data) {
                            this.myReports.unshift(resData.data);
                            localStorage.setItem('my_reports', JSON.stringify(this.myReports));
                        }
                        this.form.location_name = '';
                        this.form.latitude = '';
                        this.form.longitude = '';
                        this.capturedImage = null;
                        this.file = null;
                    } else {
                        this.errorMessage = resData.message || 'Gagal mengirim laporan.';
                    }
                } catch (err) {
                    this.errorMessage = 'Terjadi kesalahan jaringan saat mengirim laporan.';
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
