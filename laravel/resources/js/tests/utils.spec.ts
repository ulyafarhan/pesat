import { describe, expect, it } from 'vitest';

function parseLocation(locationName: string): {
    base: string;
    gender: string;
    details: string;
} {
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

function isVideo(path: string): boolean {
    if (!path) {
        return false;
    }
    const ext = path.split('.').pop()?.toLowerCase() ?? '';
    return ['mp4', 'webm', 'mov', 'avi', '3gp'].includes(ext);
}

function isCritical(confidenceScore: number, threshold = 0.85): boolean {
    return confidenceScore > threshold;
}

function getGenderClass(gender: string): string {
    if (!gender) return '';
    const g = gender.toLowerCase();
    if (g.includes('wanita') && g.includes('pria')) return 'bg-amber-50 text-amber-700 border-amber-200';
    if (g.includes('wanita')) return 'bg-purple-50 text-purple-700 border-purple-200';
    if (g.includes('pria')) return 'bg-blue-50 text-blue-700 border-blue-200';
    return 'bg-gray-50 text-gray-700 border-gray-200';
}

describe('parseLocation', () => {
    it('returns base only for simple location names', () => {
        const result = parseLocation('Taman Riyadhah');
        expect(result.base).toBe('Taman Riyadhah');
        expect(result.gender).toBe('');
        expect(result.details).toBe('');
    });

    it('parses gender and details from SIWAS location format', () => {
        const result = parseLocation(
            'Taman Riyadhah - [Wanita] R-PKN-001: Tidak mengenakan hijab',
        );
        expect(result.base).toBe('Taman Riyadhah');
        expect(result.gender).toBe('Wanita');
        expect(result.details).toBe('R-PKN-001: Tidak mengenakan hijab');
    });

    it('returns default for empty string', () => {
        const result = parseLocation('');
        expect(result.base).toBe('Lokasi Tidak Diketahui');
    });

    it('handles null/undefined as empty', () => {
        const result = parseLocation(null as unknown as string);
        expect(result.base).toBe('Lokasi Tidak Diketahui');
    });

    it('handles multiple violations separated by pipes', () => {
        const result = parseLocation(
            'Masjid Agung - [Pria & Wanita] R-KHL-001: Berdekatan | R-HYB-001: Interaksi',
        );
        expect(result.base).toBe('Masjid Agung');
        expect(result.gender).toBe('Pria & Wanita');
        expect(result.details).toBe(
            'R-KHL-001: Berdekatan | R-HYB-001: Interaksi',
        );
    });

    it('handles location name without gender tag', () => {
        const result = parseLocation(
            'Pantai Ujong Blang - Deteksi Anomali Umum',
        );
        expect(result.base).toBe('Pantai Ujong Blang');
        expect(result.gender).toBe('');
        expect(result.details).toBe('Deteksi Anomali Umum');
    });

    it('truncates very long label text gracefully', () => {
        const longLabel = 'A'.repeat(100);
        const result = parseLocation(`Lokasi - [Pria] ${longLabel}`);
        expect(result.base).toBe('Lokasi');
        expect(result.gender).toBe('Pria');
        expect(result.details.length).toBe(100);
    });

    it('handles locations with multiple dashes', () => {
        const result = parseLocation('Jalan Merdeka - [Wanita] R-PKN-001');
        expect(result.base).toBe('Jalan Merdeka');
        expect(result.gender).toBe('Wanita');
        expect(result.details).toBe('R-PKN-001');
    });

    it('handles gender tag without trailing space', () => {
        const result = parseLocation('Lokasi - [Pria]Tanpa Spasi');
        expect(result.gender).toBe('Pria');
        expect(result.details).toBe('Tanpa Spasi');
    });

    it('handles single-word location', () => {
        const result = parseLocation('Pasar');
        expect(result.base).toBe('Pasar');
        expect(result.gender).toBe('');
        expect(result.details).toBe('');
    });
});

describe('isVideo', () => {
    it('returns true for video extensions', () => {
        expect(isVideo('video.mp4')).toBe(true);
        expect(isVideo('video.mov')).toBe(true);
        expect(isVideo('video.avi')).toBe(true);
        expect(isVideo('video.webm')).toBe(true);
        expect(isVideo('video.3gp')).toBe(true);
    });

    it('returns false for image extensions', () => {
        expect(isVideo('image.jpg')).toBe(false);
        expect(isVideo('image.jpeg')).toBe(false);
        expect(isVideo('image.png')).toBe(false);
    });

    it('returns false for empty string', () => {
        expect(isVideo('')).toBe(false);
    });

    it('returns false for null path', () => {
        expect(isVideo(null as unknown as string)).toBe(false);
    });

    it('returns false for undefined path', () => {
        expect(isVideo(undefined as unknown as string)).toBe(false);
    });

    it('handles uppercase extensions', () => {
        expect(isVideo('video.MP4')).toBe(true);
        expect(isVideo('video.MOV')).toBe(true);
    });

    it('handles paths with no extension', () => {
        expect(isVideo('filename')).toBe(false);
    });

    it('handles paths with multiple dots', () => {
        expect(isVideo('snapshot.2026.06.19.mp4')).toBe(true);
        expect(isVideo('snapshot.2026.06.19.jpg')).toBe(false);
    });
});

describe('isCritical', () => {
    it('returns true for confidence score above 0.85', () => {
        expect(isCritical(0.92)).toBe(true);
        expect(isCritical(0.86)).toBe(true);
        expect(isCritical(1.0)).toBe(true);
    });

    it('returns false for confidence score at or below 0.85', () => {
        expect(isCritical(0.85)).toBe(false);
        expect(isCritical(0.7)).toBe(false);
        expect(isCritical(0.5)).toBe(false);
        expect(isCritical(0)).toBe(false);
    });

    it('uses custom threshold', () => {
        expect(isCritical(0.6, 0.5)).toBe(true);
        expect(isCritical(0.5, 0.5)).toBe(false);
        expect(isCritical(0.25, 0.3)).toBe(false);
        expect(isCritical(0.31, 0.3)).toBe(true);
    });

    it('handles negative values', () => {
        expect(isCritical(-0.1)).toBe(false);
    });

    it('handles values above 1', () => {
        expect(isCritical(2.0)).toBe(true);
    });

    it('handles zero threshold', () => {
        expect(isCritical(0, 0)).toBe(false);
        expect(isCritical(0.001, 0)).toBe(true);
    });
});

describe('getGenderClass', () => {
    it('returns purple class for female', () => {
        expect(getGenderClass('Wanita')).toContain('purple');
    });

    it('returns blue class for male', () => {
        expect(getGenderClass('Pria')).toContain('blue');
    });

    it('returns amber class for mixed gender', () => {
        expect(getGenderClass('Pria & Wanita')).toContain('amber');
    });

    it('returns amber class regardless of order', () => {
        expect(getGenderClass('Wanita & Pria')).toContain('amber');
    });

    it('returns empty for empty gender', () => {
        expect(getGenderClass('')).toBe('');
    });

    it('handles case insensitive gender', () => {
        expect(getGenderClass('wanita')).toContain('purple');
        expect(getGenderClass('PRIA')).toContain('blue');
    });

    it('returns gray class for unknown gender', () => {
        expect(getGenderClass('Unknown')).toContain('gray');
    });
});
