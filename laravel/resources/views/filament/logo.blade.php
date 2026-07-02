<style>
    .pesat-brand-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .pesat-brand-logos {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease-in-out;
    }
    .pesat-brand-img {
        height: 2.25rem; /* Ukuran diturunkan sedikit agar pas saat diberi background padding */
        width: auto;
        object-fit: contain;
    }
    
    /* Mode Gelap: Daripada menggunakan shadow yang nge-blur, kita beri "pil" latar putih khusus untuk logonya */
    :is(.dark) .pesat-brand-logos {
        background-color: rgba(255, 255, 255, 0.95);
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .pesat-brand-text-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-left: 0.25rem;
    }
    .pesat-brand-title {
        font-size: 1.5rem; 
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.025em;
    }
    .pesat-brand-subtitle {
        font-size: 0.7rem; 
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        line-height: 1;
        margin-top: 0.2rem;
        color: {{ \Filament\Support\Facades\FilamentColor::getColors()['primary'][500] ?? '#6366f1' }};
    }
</style>

<div class="pesat-brand-container">
    <!-- Logos -->
    <div class="pesat-brand-logos">
        <img src="{{ asset('logo-unimal.webp') }}" alt="Unimal" class="pesat-brand-img" />
        <img src="{{ asset('logo-syariat-islam-lhokseumawe.webp') }}" alt="Syariat" class="pesat-brand-img" />
        <img src="{{ asset('logo-gemastik.png') }}" alt="Gemastik" class="pesat-brand-img" />
    </div>
    
    <!-- Text -->
    <div class="pesat-brand-text-wrapper">
        <span class="pesat-brand-title">
            PESAT
        </span>
        <span class="pesat-brand-subtitle">
            Peudong Syariat
        </span>
    </div>
</div>
