<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Header -->
            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                <div style="margin-top: 0.25rem;">
                    <x-filament::icon
                        icon="heroicon-o-academic-cap"
                        style="width: 2rem; height: 2rem; color: {{ \Filament\Support\Facades\FilamentColor::getColors()['primary'][500] }};"
                    />
                </div>
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 700; line-height: 1.4; margin: 0;">
                        Inovasi Smart City Lhokseumawe: Implementasi AI Penegak Syariat di Panggung GEMASTIK
                    </h2>
                    <p style="font-size: 0.875rem; font-weight: 600; color: {{ \Filament\Support\Facades\FilamentColor::getColors()['primary'][500] }}; margin-top: 0.25rem; margin-bottom: 0; text-transform: uppercase; letter-spacing: 0.05em;">
                        Project Gemastik 2025
                    </p>
                </div>
            </div>

            <!-- Quote -->
            <div style="padding: 1.25rem; border-radius: 0.5rem; background-color: rgba(100, 116, 139, 0.1);">
                <p style="font-size: 1.125rem; font-weight: 500; text-align: center; font-style: italic; opacity: 0.9; margin: 0;">
                    "Teknologi Bukan untuk Mengubah Budaya, Melainkan untuk Menjaga Nilai Luhurnya."
                </p>
            </div>

            <!-- Paragraphs -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                <div>
                    <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; margin-top: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <x-filament::icon icon="heroicon-m-check-badge" style="width: 1.25rem; height: 1.25rem; color: {{ \Filament\Support\Facades\FilamentColor::getColors()['primary'][500] }};" />
                        Harmoni AI & Syariat
                    </h3>
                    <p style="font-size: 0.875rem; line-height: 1.6; opacity: 0.75; text-align: justify; margin: 0;">
                        Proyek ini hadir sebagai pionir solusi Smart City di Aceh yang menjembatani kemajuan teknologi Artificial Intelligence dengan penegakan hukum Syariat secara humanis. Dengan memanfaatkan infrastruktur CCTV kota yang ditransformasikan menjadi kamera pintar, kami membantu pemerintah daerah dan Satpol PP/WH Kota Lhokseumawe dalam menciptakan ruang publik yang aman, tertib, dan berkah.
                    </p>
                </div>
                
                <div>
                    <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; margin-top: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <x-filament::icon icon="heroicon-m-eye" style="width: 1.25rem; height: 1.25rem; color: {{ \Filament\Support\Facades\FilamentColor::getColors()['primary'][500] }};" />
                        Visi Smart Governance
                    </h3>
                    <p style="font-size: 0.875rem; line-height: 1.6; opacity: 0.75; text-align: justify; margin: 0;">
                        Menghadirkan solusi Smart Governance masa depan melalui integrasi Computer Vision pada CCTV Kota. Sebuah manifestasi nyata pilar Smart City berbasis kearifan lokal untuk mendeteksi pelanggaran Syariat Islam secara otomatis, objektif, dan real-time.
                    </p>
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
