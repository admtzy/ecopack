@extends('layouts.app')

@section('content')
<style>
    .page-header {
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main, #1f2937);
        margin-bottom: 4px;
    }

    /* Grid Dashboard */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    /* Card Saldo */
    .balance-card {
        background: linear-gradient(135deg, var(--primary, #15803d), #166534);
        color: white;
        padding: 24px;
        border-radius: var(--radius, 12px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow, 0 4px 6px -1px rgba(0, 0, 0, 0.05));
    }

    .balance-card .info {
        margin-bottom: 16px;
    }

    .balance-card p {
        font-size: 0.875rem;
        opacity: 0.9;
        margin: 0 0 4px 0;
    }

    .balance-card h2 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }

    .balance-card .btn-light {
        background-color: white;
        color: var(--primary, #15803d);
        font-weight: 600;
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 6px;
        text-align: center;
        display: inline-block;
        transition: background-color 0.2s;
    }

    .balance-card .btn-light:hover {
        background-color: var(--primary-light, #f0fdf4);
    }

    /* Card Quick Action / Scan */
    .scan-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border, #e5e7eb);
        padding: 24px;
        border-radius: var(--radius, 12px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow, 0 2px 4px rgba(0, 0, 0, 0.05));
    }

    .scan-card h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main, #1f2937);
        margin: 0 0 8px 0;
    }

    .scan-card p {
        font-size: 0.9rem;
        color: var(--text-muted, #6b7280);
        margin: 0 0 20px 0;
    }

    /* Badge Status Reward */
    .badge-reward {
        display: inline-block;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
    }

    .badge-success {
        background-color: #dcfce7;
        color: #15803d;
    }

    .badge-muted {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .empty-state {
        text-align: center;
        padding: 40px 16px !important;
        color: var(--text-muted, #6b7280);
    }
</style>

<div class="container">
    <!-- Header Dashboard -->
    <div class="page-header">
        <h1 class="page-title">Dashboard Pengguna</h1>
        <p class="muted">Selamat datang! Pantau saldo poin dan aktivitas pemindaian produk Anda.</p>
    </div>

    <!-- Ringkasan Saldo & Quick Scan -->
    <div class="dashboard-grid">
        <!-- Card Saldo Poin -->
        <div class="balance-card">
            <div class="info">
                <p>Saldo Poin Saya</p>
                <h2>{{ number_format($balance) }} <span style="font-size: 1rem; font-weight: 400;">Pts</span></h2>
            </div>
            <div>
                <a class="btn-light" href="{{ route('rewards.index') }}">
                    🎁 Lihat Reward
                </a>
            </div>
        </div>

        <!-- Card Scan QR -->
        <div class="scan-card">
            <div>
                <h2>Scan QR Produk</h2>
                <p>Gunakan kamera untuk memindai QR produk EcoPack dan dapatkan poin hadiah.</p>
            </div>
            <div>
                <a href="{{ route('scan.camera') }}" class="btn" style="width: 100%; text-align: center;">
                    📷 Buka Kamera
                </a>
            </div>
        </div>
    </div>

    <!-- Card Riwayat Pemindaian -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border, #e5e7eb);">
            <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--text-main, #1f2937);">
                Riwayat Scan
            </h2>
        </div>

        @if($scans->isEmpty())
            <div class="empty-state">
                🍃 Belum ada riwayat scan. Silakan pindai QR produk menggunakan kamera.
            </div>
        @else
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Waktu</th>
                            <th>Reward</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scans as $s)
                            <tr>
                                <td style="font-weight: 600;">
                                    {{ $s->qrCode?->product?->name ?? '-' }}
                                </td>
                                <td>{{ $s->created_at->format('d-m-Y H:i') }}</td>
                                <td>
                                    @if($s->rewarded)
                                        <span class="badge-reward badge-success">
                                            🎉 100 poin
                                        </span>
                                    @else
                                        <span class="badge-reward badge-muted">
                                            Tidak mendapat poin
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection