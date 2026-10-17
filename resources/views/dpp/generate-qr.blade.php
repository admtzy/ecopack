@extends('layouts.app')

@section('content')
<style>
    .form-container {
        max-width: 680px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main, #1f2937);
        margin: 0;
    }

    /* Grid Informational Summary */
    .info-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }

    .info-box {
        background-color: #f8fafc;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 8px;
        padding: 12px 16px;
    }

    .info-box span {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted, #6b7280);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }

    .info-box strong {
        font-size: 1rem;
        color: var(--text-main, #1f2937);
    }

    .qr-badge {
        display: inline-block;
        background-color: var(--primary-light, #f0fdf4);
        color: var(--primary, #15803d);
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
    }

    /* Callout Box Download */
    .download-callout {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: var(--radius, 12px);
        padding: 20px;
        margin-top: 24px;
    }

    .download-callout h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #166534;
        margin: 0 0 6px 0;
    }

    .download-callout p {
        font-size: 0.875rem;
        color: #15803d;
        margin: 0 0 16px 0;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main, #1f2937);
        margin-bottom: 6px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--border, #e5e7eb);
    }

    .helper-text {
        font-size: 0.85rem;
        color: var(--text-muted, #6b7280);
        margin-top: 6px;
        line-height: 1.4;
    }
</style>

<div class="container form-container">
    <!-- Header Halaman -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Generate QR Code</h1>
            <p class="muted">Buat dan unduh batch QR Code unik untuk kemasan produk EcoPack.</p>
        </div>
        <div>
            <a href="{{ route('dpp.index') }}" class="btn-outline">
                ← Kembali ke Daftar DPP
            </a>
        </div>
    </div>

    <!-- Alert Sukses -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Errors -->
    @if($errors->any())
        <div class="alert alert-error">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin-top: 6px; margin-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Form Utama -->
    <div class="card">
        <!-- Ringkasan Info Produk -->
        <div class="info-summary-grid">
            <div class="info-box">
                <span>Kode Produk</span>
                <strong>{{ $product->product_code }}</strong>
            </div>

            <div class="info-box">
                <span>Nama Produk</span>
                <strong>{{ $product->name }}</strong>
            </div>

            <div class="info-box">
                <span>QR Terbuat</span>
                <strong class="qr-badge">{{ $qrCount }} QR</strong>
            </div>
        </div>

        <!-- Form Pembuatan QR -->
        <form method="POST" action="{{ route('dpp.qr.store', $product) }}">
            @csrf

            <div class="form-group">
                <label for="quantity">Jumlah QR yang ingin dibuat</label>
                <input
                    id="quantity"
                    type="number"
                    name="quantity"
                    min="1"
                    max="1000"
                    value="{{ old('quantity', 100) }}"
                    placeholder="Contoh: 100"
                    required
                >
                <p class="helper-text">
                    💡 Setiap QR mempunyai token unik dan terhubung ke DPP produk ini. Jumlah tambahan akan dibuat tanpa menghapus QR yang sudah ada.
                </p>
            </div>

            <div class="form-actions">
                <button type="submit"
                        onclick="return confirm('Buat QR sejumlah yang dimasukkan?')">
                    ⚡ Generate QR Sekarang
                </button>
            </div>
        </form>

        <!-- Blok Download QR ZIP -->
        @if($qrCount > 0)
            <div class="download-callout">
                <h2>📦 Unduh Berkas QR</h2>
                <p>
                    Total QR aktif saat ini: <strong>{{ $qrCount }}</strong>. Seluruh QR aktif produk ini akan dikemas ke dalam satu file arsip (.ZIP).
                </p>

                <a class="btn"
                   href="{{ route('dpp.qr.download', $product) }}"
                   style="background-color: #166534;">
                    📥 Download Semua QR (.ZIP)
                </a>
            </div>
        @endif
    </div>
</div>
@endsection