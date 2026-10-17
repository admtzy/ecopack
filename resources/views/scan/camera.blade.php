@extends('layouts.app')

@section('content')
<style>
    .scan-container {
        max-width: 680px;
        margin: 0 auto;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main, #1f2937);
        margin-bottom: 4px;
    }

    /* Container Pemindai QR / Viewer Kamera */
    .scanner-wrapper {
        position: relative;
        background: #000;
        border-radius: var(--radius, 12px);
        overflow: hidden;
        margin: 20px 0;
        box-shadow: var(--shadow, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
    }

    #reader {
        width: 100% !important;
        max-width: 500px !important;
        margin: 0 auto;
        border: none !important;
    }

    /* Penyesuaian video dari HTML5QRCode */
    #reader video {
        width: 100% !important;
        height: auto !important;
        border-radius: var(--radius, 12px);
    }

    /* Tombol Aksi */
    .action-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .action-toolbar button, 
    .action-toolbar .btn {
        flex: 1;
        min-width: 140px;
        padding: 10px 14px;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-camera {
        background-color: var(--primary, #15803d);
        color: white;
    }

    .btn-camera:hover {
        background-color: var(--primary-hover, #166534);
    }

    .btn-scan {
        background-color: #16a34a;
        color: white;
    }

    .btn-stop {
        background-color: #dc2626;
        color: white;
    }

    .btn-stop:hover {
        background-color: #b91c1c;
    }

    button:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Area Upload Galeri */
    .gallery-upload-box {
        border: 2px dashed var(--border, #cbd5e1);
        border-radius: var(--radius, 12px);
        padding: 24px;
        text-align: center;
        background-color: #f8fafc;
        margin-top: 16px;
        transition: border-color 0.2s, background-color 0.2s;
    }

    .gallery-upload-box:hover {
        border-color: var(--primary, #15803d);
        background-color: var(--primary-light, #f0fdf4);
    }

    /* Status Card */
    .status-card {
        background: #ffffff;
        border-left: 4px solid var(--primary, #15803d);
        border-radius: 8px;
        padding: 16px;
        box-shadow: var(--shadow, 0 2px 4px rgba(0, 0, 0, 0.05));
        margin: 20px 0;
    }

    .status-title {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-main, #1f2937);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .status-text {
        font-size: 0.95rem;
        color: var(--text-main, #374151);
        margin: 0;
    }
</style>

<div class="container py-4 scan-container">
    <div class="mb-4">
        <h1 class="page-title">Scan QR Produk EcoPack</h1>
        <p class="muted">Arahkan kamera ke QR produk, atau unggah foto dari galeri perangkat Anda.</p>
    </div>

    <!-- Toolbar Tombol Navigasi/Kamera -->
    <div class="action-toolbar">
        <button type="button" id="camera-btn" class="btn btn-camera">
            📷 Aktifkan Kamera
        </button>

        <button type="button" id="scan-btn" class="btn btn-scan" disabled>
            ⚡ Scan QR Sekarang
        </button>

        <button type="button" id="stop-btn" class="btn btn-stop" disabled>
            ⏹️ Hentikan Kamera
        </button>

        <button type="button" id="gallery-btn" class="btn-outline">
            🖼️ Upload dari Galeri
        </button>
    </div>

    <!-- Container Kamera Pemindai -->
    <div class="scanner-wrapper">
        <div id="reader" style="display:none;"></div>
    </div>

    <!-- Area Input Galeri -->
    <div id="gallery-area" class="gallery-upload-box" style="display:none;">
        <label for="qr-file" class="form-label" style="font-weight: 600; cursor: pointer;">
            📁 Pilih gambar QR dari galeri Anda:
        </label>

        <input
            type="file"
            id="qr-file"
            accept="image/*"
            class="form-control"
            style="margin-top: 12px;"
        >
    </div>

    <!-- Kotak Status Pemindaian -->
    <div class="status-card">
        <div class="status-title">Status Scan</div>
        <p id="scan-status" role="status" aria-live="polite" class="status-text">
            Kamera belum aktif. Tekan "Aktifkan Kamera" untuk memulai.
        </p>
    </div>

    <!-- Tombol Kembali -->
    <div style="margin-top: 24px;">
        <a href="{{ route('dashboard') }}" class="btn-outline">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const status = document.getElementById('scan-status');
    const reader = document.getElementById('reader');
    const galleryArea = document.getElementById('gallery-area');
    const fileInput = document.getElementById('qr-file');

    const cameraBtn = document.getElementById('camera-btn');
    const scanBtn = document.getElementById('scan-btn');
    const stopBtn = document.getElementById('stop-btn');
    const galleryBtn = document.getElementById('gallery-btn');

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    ).content;

    let scanner = null;
    let cameraActive = false;
    let processing = false;
    let latestDecodedText = null;
    let decoding = false;

    function setStatus(message) {
        status.textContent = message;
    }

    function extractToken(decodedText) {
        try {
            const url = new URL(decodedText);

            if (url.origin !== window.location.origin) {
                return null;
            }

            const match = url.pathname.match(
                /\/(?:dpp\/qr|scan)\/([^/]+)\/?$/
            );

            return match ? decodeURIComponent(match[1]) : null;
        } catch (error) {
            return null;
        }
    }

    async function processScan(decodedText) {
        if (processing) return;

        const token = extractToken(decodedText);

        if (!token) {
            setStatus(
                'QR tidak valid atau bukan QR produk EcoPack. Coba arahkan kamera ke QR yang benar.'
            );
            latestDecodedText = null;
            scanBtn.disabled = true;
            return;
        }

        processing = true;
        scanBtn.disabled = true;
        cameraBtn.disabled = true;
        galleryBtn.disabled = true;

        setStatus('QR berhasil dibaca. Sedang memproses scan...');

        try {
            if (scanner && cameraActive) {
                await scanner.stop();
                cameraActive = false;
            }

            const response = await fetch(
                @json(url('/scan')) + '/' + encodeURIComponent(token),
                {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({})
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'Scan gagal diproses.'
                );
            }

            setStatus(result.message || 'Scan berhasil.');

            if (result.redirect) {
                const destination = new URL(
                    result.redirect,
                    window.location.origin
                );

                if (destination.origin !== window.location.origin) {
                    throw new Error('Alamat tujuan tidak valid.');
                }

                destination.searchParams.set(
                    'scan_message',
                    result.message || 'Scan berhasil.'
                );

                destination.searchParams.set(
                    'scan_rewarded',
                    result.rewarded ? '1' : '0'
                );

                setTimeout(function () {
                    window.location.assign(destination.toString());
                }, 1200);
            } else {
                setStatus(
                    result.message ||
                    'Scan berhasil, tetapi alamat tujuan tidak tersedia.'
                );
                processing = false;
                cameraBtn.disabled = false;
                galleryBtn.disabled = false;
            }

        } catch (error) {
            setStatus(error.message || 'Gagal memproses QR.');
            processing = false;
            cameraBtn.disabled = false;
            galleryBtn.disabled = false;
            scanBtn.disabled = !latestDecodedText;
        }
    }

    async function startCamera() {
        if (processing || cameraActive) return;

        if (typeof Html5Qrcode === 'undefined') {
            setStatus('Library QR gagal dimuat. Periksa koneksi internet.');
            return;
        }

        galleryArea.style.display = 'none';
        reader.style.display = 'block';
        latestDecodedText = null;
        scanBtn.disabled = true;

        cameraBtn.disabled = true;
        galleryBtn.disabled = true;
        stopBtn.disabled = false;

        if (!scanner) {
            scanner = new Html5Qrcode('reader');
        }

        try {
            await scanner.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                function (decodedText) {
                    // Hanya membaca QR dan menyimpannya.
                    // Tidak memproses reward sebelum tombol ditekan.
                    latestDecodedText = decodedText;
                    scanBtn.disabled = false;
                    setStatus(
                        'QR terdeteksi. Periksa QR di kamera, lalu tekan "Scan QR Sekarang".'
                    );
                },
                function () {
                    // Tidak menampilkan error pada setiap frame
                    // yang belum berhasil membaca QR.
                }
            );

            cameraActive = true;
            setStatus(
                'Kamera aktif. Arahkan QR ke kotak kamera hingga QR terdeteksi.'
            );

        } catch (error) {
            cameraActive = false;
            cameraBtn.disabled = false;
            galleryBtn.disabled = false;
            stopBtn.disabled = true;

            setStatus(
                'Kamera tidak dapat dibuka. Periksa izin kamera dan pastikan menggunakan localhost atau HTTPS.'
            );
        }
    }

    async function stopCamera() {
        if (scanner && cameraActive) {
            try {
                await scanner.stop();
            } catch (error) {
                console.error('Gagal menghentikan kamera:', error);
            }
        }

        cameraActive = false;
        latestDecodedText = null;

        reader.style.display = 'none';
        scanBtn.disabled = true;
        stopBtn.disabled = true;
        cameraBtn.disabled = false;
        galleryBtn.disabled = false;

        setStatus('Kamera dihentikan.');
    }

    cameraBtn.addEventListener('click', startCamera);

    scanBtn.addEventListener('click', async function () {
        if (processing) return;

        if (!latestDecodedText) {
            setStatus('QR belum terbaca. Arahkan kamera ke QR terlebih dahulu.');
            return;
        }

        await processScan(latestDecodedText);
    });

    stopBtn.addEventListener('click', stopCamera);

    galleryBtn.addEventListener('click', async function () {
        if (processing) return;

        if (cameraActive) {
            await stopCamera();
        }

        reader.style.display = 'none';
        galleryArea.style.display = 'block';

        setStatus('Pilih gambar yang berisi QR produk.');
        fileInput.click();
    });

    fileInput.addEventListener('change', async function () {
        const file = fileInput.files[0];

        if (!file || processing) return;

        if (typeof Html5Qrcode === 'undefined') {
            setStatus('Library QR gagal dimuat.');
            fileInput.value = '';
            return;
        }

        try {
            if (!scanner) {
                scanner = new Html5Qrcode('reader');
            }

            setStatus('Sedang membaca QR dari gambar...');

            const decodedText = await scanner.scanFile(file, false);

            setStatus('QR gambar berhasil dibaca. Memproses scan...');
            await processScan(decodedText);

        } catch (error) {
            setStatus(
                'QR tidak ditemukan pada gambar. Gunakan gambar yang jelas, tidak buram, dan QR tidak terpotong.'
            );
        } finally {
            fileInput.value = '';
        }
    });

    window.addEventListener('beforeunload', function () {
        if (scanner && cameraActive) {
            scanner.stop().catch(function () {});
        }
    });
});
</script>
@endsection