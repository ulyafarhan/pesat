<?php

namespace Database\Seeders;

use App\Models\Camera;
use Illuminate\Database\Seeder;

class CameraSeeder extends Seeder
{
    public function run(): void
    {
        $cameras = [
            [
                'id' => 'CAM-001',
                'location_name' => 'Taman Riyadhah',
                'latitude' => 5.18020000,
                'longitude' => 97.15070000,
                'is_active' => true,
                'stream_source' => 'https://assets.mixkit.co/videos/preview/mixkit-traffic-in-a-busy-intersection-from-above-41712-large.mp4',
                'edge_device_id' => 'LAPTOP-JURI',
            ],
            [
                'id' => 'CAM-002',
                'location_name' => 'Masjid Agung Islamic Center',
                'latitude' => 5.18340000,
                'longitude' => 97.14510000,
                'is_active' => true,
                'stream_source' => 'https://assets.mixkit.co/videos/preview/mixkit-cars-on-a-highway-at-night-42171-large.mp4',
                'edge_device_id' => 'LAPTOP-JURI',
            ],
            [
                'id' => 'CAM-003',
                'location_name' => 'Pantai Ujong Blang',
                'latitude' => 5.19200000,
                'longitude' => 97.16430000,
                'is_active' => true,
                'stream_source' => 'https://assets.mixkit.co/videos/preview/mixkit-security-camera-view-of-a-street-in-winter-41982-large.mp4',
                'edge_device_id' => 'LAPTOP-JURI',
            ],
            [
                'id' => 'CAM-004',
                'location_name' => 'Kawasan Kampus Unimal',
                'latitude' => 5.14320000,
                'longitude' => 97.02340000,
                'is_active' => true,
                'stream_source' => 'https://assets.mixkit.co/videos/preview/mixkit-aerial-view-of-a-city-intersection-41711-large.mp4',
                'edge_device_id' => 'LAPTOP-JURI',
            ],
            [
                'id' => 'CAM-005',
                'location_name' => 'Terminal Bus Lhokseumawe',
                'latitude' => 5.17640000,
                'longitude' => 97.13890000,
                'is_active' => true,
                'stream_source' => 'https://assets.mixkit.co/videos/preview/mixkit-heavy-traffic-on-a-highway-41710-large.mp4',
                'edge_device_id' => 'LAPTOP-JURI',
            ],
            [
                'id' => 'CAM-006',
                'location_name' => 'Pasar Kota Lhokseumawe',
                'latitude' => 5.18150000,
                'longitude' => 97.14120000,
                'is_active' => true,
                'stream_source' => 'https://assets.mixkit.co/videos/preview/mixkit-surveillance-camera-in-a-parking-garage-41983-large.mp4',
                'edge_device_id' => 'LAPTOP-JURI',
            ],
        ];

        foreach ($cameras as $camera) {
            Camera::updateOrCreate(['id' => $camera['id']], $camera);
        }
    }
}
