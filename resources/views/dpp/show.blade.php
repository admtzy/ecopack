@extends('layouts.app')

@section('content')

@php
    $scanMessage = session('scan_message')
        ?? request()->query('scan_message');

    $scanRewarded = session('scan_rewarded');

    if ($scanRewarded === null) {
        $scanRewarded = request()->query('scan_rewarded') === '1';
    }
@endphp

<style>
    .dpp-container {
        max-width: 720px;
        margin: 0 auto;
    }

    .dpp-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border, #e5e7eb);
        border-radius: var(--radius, 12px);
        padding: 28px;
        box-shadow: var(--shadow, 0 4px 6px -1px rgba(0, 0, 0, 0.05));
    }

    .dpp-badge-header {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: var(--primary-light, #f0fdf4);
        color: var(--primary, #15803d);
        font-weight: 700;
        font-size: 0.8rem;
        padding: 4px 12px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 12px;
    }

    .dpp-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-main, #1f2937);
        margin: 0 0 16px 0;
        line-height: 1.3;
    }

    .dpp-grid {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--border, #e5e7eb);
    }

    .dpp-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .dpp-label {
        font-size: 0.825rem;
        font-weight: 700;
        color: var(--text-muted, #6b7280);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .dpp-value {
        font-size: 0.975rem;
        color: var(--text-main, #1f2937);
        line-height: 1.5;
    }

    .dpp-value.code-highlight {
        font-family: monospace;
        font-weight: 700;
        background-color: #f3f4f6;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
        width: fit-content;
    }

    /* Badge Status Klaim */
    .badge-claim {
        display: inline-block;
        padding: 4px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 50px;
        width: fit-content;
    }

    .claim-success {
        background-color: #dcfce7;
        color: #15803d;
    }

    .claim-pending {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .dpp-actions {
        padding-top: 20px;
        border-top: 1px solid var(--border, #e5e7eb);
    }

    .btn-dpp-action {
        width: 100%;
        text-align: center;
        padding: 12px 20px;
        font-size: 1rem;
        font-weight: 600;
        display: block;
        box-sizing: border-box;
    }
</style>

<div class="container dpp-container py-4">

    <!-- Alert Notifikasi Scan -->
    @if ($scanMessage)
        <div class="alert {{ $scanRewarded ? 'alert-success' : 'alert-info' }}"
             role="status" style="margin-bottom: 20px;">
            {{ $scanMessage }}
        </div>
    @endif

    <!-- Card DPP Utama -->
    <div class="dpp-card">
        <!-- Header Badge -->
        <div class="dpp-badge-header">
            🌱 EcoPack Digital Product Passport
        </div>

        <h1 class="dpp-title">{{ $product->name }}</h1>

        <!-- Grid Detail Produk -->
        <div class="dpp-grid">
            
            <div class="dpp-item">
                <span class="dpp-label">ID / Kode Produk</span>
                <span class="dpp-value code-highlight">{{ $product->product_code }}</span>
            </div>

            <div class="dpp-item">
                <span class="dpp-label">Deskripsi Produk</span>
                <span class="dpp-value">{{ $product->description ?: '-' }}</span>
            </div>

            <div class="dpp-item">
                <span class="dpp-label">Bahan Baku</span>
                <span class="dpp-value">{{ $product->materials ?: '-' }}</span>
            </div>

            <div class="dpp-item">
                <span class="dpp-label">Informasi Produksi</span>
                <span class="dpp-value">{{ $product->production_info ?: '-' }}</span>
            </div>

            <div class="dpp-item">
                <span class="dpp-label">Akhir Penggunaan / Daur Ulang</span>
                <span class="dpp-value">{{ $product->end_of_life ?: '-' }}</span>
            </div>

            <div class="dpp-item">
                <span class="dpp-label">Status Klaim QR</span>
                <div class="dpp-value">
                    <span class="badge-claim {{ $qr->claimed_at ? 'claim-success' : 'claim-pending' }}">
                        {{ $qr->claimed_at ? '🎉 Reward telah diklaim' : '⏳ Belum diklaim' }}
                    </span>
                </div>
            </div>

        </div>

        <!-- Tombol Aksi Pengguna -->
        <div class="dpp-actions">
            @auth
                @if (auth()->user()->role === 'user')
                    <a class="btn btn-dpp-action" href="{{ route('scan.open', ['token' => $qr->token]) }}">
                        📷 Scan QR dan Lihat DPP
                    </a>
                @endif
            @else
                <a class="btn btn-dpp-action" href="{{ route('login') }}">
                    🔐 Login untuk scan dan mendapatkan poin
                </a>
            @endauth
        </div>
    </div>

</div>

@endsection