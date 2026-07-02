# Project Specification: PESAT Extension

This specification details the design requirements for the landing page, citizen reporting flow, break-time automatic routing, and the Wilayatul Hisbah PWA dashboard.

## 1. Public Landing Page & Citizen Reporting
- **Stack:** Blade templates + Tailwind CSS + Alpine.js.
- **Features:**
  - Landing page detailing PESAT system goals, zones, and rules.
  - Form to submit citizen reports.
  - Capture media directly via browser camera API using Alpine.js or upload existing files (image/video).
  - Collect geolocation coordinates (Latitude & Longitude) via Browser Geolocation API.
  - Capture report timestamp.

## 2. Admin Settings & Break Mode Routing
- **Settings System:**
  - Toggle for manual "Break Mode".
  - Scheduled "Break Mode" window (default: 12:00 - 14:00).
- **Routing Logic:**
  - When a report is received, check if the current system time falls within the break window or if break mode is manually toggled active.
  - If NOT in break mode: Report status is set to `pending_admin` and routed to the main Filament admin panel.
  - If IN break mode: Report status is set to `pending_wh` and routed to the Wilayatul Hisbah dashboard. Broadcast notification/event immediately.

## 3. Wilayatul Hisbah (WH) PWA Dashboard
- **Stack:** Vue 3 + Inertia.js (or standalone SPA consuming Laravel API), styled with Tailwind, configured as a PWA.
- **Features:**
  - List of routed citizen reports assigned to WH (`pending_wh`).
  - Interface to view media, map location, and timestamp of the incident.
  - Actions to **Verify** or **Reject** the report with text feedback.
  - Service worker and web app manifest to allow installing the dashboard on mobile devices as a PWA.
