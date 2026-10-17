@extends('layouts.app')

@section('content')
<style>
    .page-header {
        margin-bottom: 28px;
    }

    .page-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-main, #1f2937);
        margin-bottom: 4px;
    }

    /* Grid Statistik Top */
    .stats-grid-top {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .stats-grid-bottom {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border, #e5e7eb);
        border-radius: var(--radius, 12px);
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: var(--shadow, 0 2px 4px rgba(0,0,0,0.04));
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.08);
    }

    .stat-info h6 {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted, #6b7280);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0 0 6px 0;
    }

    .stat-info h3 {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--text-main, #1f2937);
        margin: 0;
        line-height: 1;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        background-color: var(--primary-light, #f0fdf4);
        color: var(--primary, #15803d);
    }

    /* Grid Kartu Pengelolaan Admin */
    .admin-menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }

    .menu-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border, #e5e7eb);
        border-radius: var(--radius, 12px);
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--shadow, 0 2px 4px rgba(0, 0, 0, 0.04));
    }

    .menu-card-header {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 12px;
    }

    .menu-card-icon {
        font-size: 1.5rem;
        line-height: 1;
        padding: 10px;
        background-color: #f3f4f6;
        border-radius: 8px;
    }

    .menu-card h4 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-main, #1f2937);
        margin: 0 0 4px 0;
    }

    .menu-card p {
        font-size: 0.875rem;
        color: var(--text-muted, #6b7280);
        margin: 0 0 20px 0;
        line-height: 1.5;
    }

    .menu-card-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: auto;
    }

    .btn-warning-custom {
        background-color: #d97706;
        color: white;
    }

    .btn-warning-custom:hover {
        background-color: #b45309;
    }
</style>

<div class="container py-4">

    <!-- Header Dashboard -->
    <div class="page-header">
        <h1 class="page-title">Dashboard Admin EcoPack</h1>
        <p class="muted">Ringkasan aktivitas platform, manajemen produk, reward, dan mitra kemitraan.</p>
    </div>

    <!-- Ringkasan Statistik 3 Kolom Atas -->
    <div class="stats-grid-top">
        <div class="stat-card">
            <div class="stat-info">
                <h6>Pengguna</h6>
                <h3>{{ $users }}</h3>
            </div>
            <div class="stat-icon">👥</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h6>Produk DPP</h6>
                <h3>{{ $products }}</h3>
            </div>
            <div class="stat-icon">📦</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h6>Total Scan</h6>
                <h3>{{ $scans }}</h3>
            </div>
            <div class="stat-icon">📷</div>
        </div>
    </div>

    <!-- Ringkasan Statistik 2 Kolom Bawah -->
    <div class="stats-grid-bottom">
        <div class="stat-card">
            <div class="stat-info">
                <h6>Penukaran Hadiah</h6>
                <h3>{{ $redemptions }}</h3>
            </div>
            <div class="stat-icon">🎁</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h6>Pengajuan Mitra</h6>
                <h3>{{ $requests }}</h3>
            </div>
            <div class="stat-icon">🤝</div>
        </div>
    </div>

    <!-- Menu Pengelolaan Admin -->
    <div class="admin-menu-grid">

        <!-- Pengelolaan DPP -->
        <div class="menu-card">
            <div>
                <div class="menu-card-header">
                    <div class="menu-card-icon">🌱</div>
                    <div>
                        <h4>Pengelolaan Digital Product Passport</h4>
                    </div>
                </div>
                <p>
                    Kelola data produk dan QR Code EcoPack untuk keterbukaan rantai pasok.
                </p>
            </div>

            <div class="menu-card-actions">
                <a class="btn" href="{{ route('dpp.create') }}">
                    + Tambah DPP
                </a>
                <a class="btn-outline" href="{{ route('dpp.index') }}">
                    Kelola DPP
                </a>
            </div>
        </div>

        <!-- Pengelolaan Reward -->
        <div class="menu-card">
            <div>
                <div class="menu-card-header">
                    <div class="menu-card-icon">🏆</div>
                    <div>
                        <h4>Pengelolaan Reward</h4>
                    </div>
                </div>
                <p>
                    Tambahkan hadiah, atur kebutuhan poin, stok, gambar, dan status ketersediaan hadiah.
                </p>
            </div>

            <div class="menu-card-actions">
                <a class="btn" href="{{ route('admin.rewards.create') }}">
                    + Buat Reward
                </a>
                <a class="btn-outline" href="{{ route('admin.rewards.index') }}">
                    Kelola Reward
                </a>
            </div>
        </div>

        <!-- Penukaran Hadiah -->
        <div class="menu-card">
            <div>
                <div class="menu-card-header">
                    <div class="menu-card-icon">🚚</div>
                    <div>
                        <h4>Penukaran Hadiah</h4>
                    </div>
                </div>
                <p>
                    Periksa permintaan penukaran poin dari user, proses pesanan, perbarui status pengiriman, dan tangani penukaran yang ditolak.
                </p>
            </div>

            <div class="menu-card-actions">
                <a class="btn btn-warning-custom" href="{{ route('admin.redemptions') }}">
                    Kelola Penukaran Hadiah
                </a>
            </div>
        </div>

        <!-- Kemitraan -->
        <div class="menu-card">
            <div>
                <div class="menu-card-header">
                    <div class="menu-card-icon">🏢</div>
                    <div>
                        <h4>Pengelolaan Kemitraan</h4>
                    </div>
                </div>
                <p>
                    Lihat dan proses pengajuan kemitraan EcoPack dari calon mitra usaha.
                </p>
            </div>

            <div class="menu-card-actions">
                <a class="btn-outline" href="{{ route('partnerships.index') }}">
                    Kelola Pengajuan Mitra
                </a>
            </div>
        </div>

    </div>

</div>
@endsection