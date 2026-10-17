@extends('layouts.app')

@section('content')
<style>
    .reward-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .balance-card {
        background: linear-gradient(135deg, var(--primary, #15803d), #166534);
        color: white;
        padding: 20px 24px;
        border-radius: var(--radius, 12px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        box-shadow: var(--shadow, 0 4px 6px -1px rgba(0, 0, 0, 0.05));
    }

    .balance-info p {
        font-size: 0.875rem;
        opacity: 0.9;
        margin: 0;
    }

    .balance-info h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .reward-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-top: 20px;
    }

    .reward-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border, #e5e7eb);
        border-radius: var(--radius, 12px);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow, 0 2px 4px rgba(0,0,0,0.05));
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .reward-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .reward-image-box {
        position: relative;
        width: 100%;
        height: 180px;
        background-color: #f3f4f6;
    }

    .reward-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .reward-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .reward-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-main, #1f2937);
        margin-bottom: 6px;
    }

    .reward-desc {
        font-size: 0.875rem;
        color: var(--text-muted, #6b7280);
        margin-bottom: 16px;
        flex: 1;
    }

    .reward-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid var(--border, #e5e7eb);
        margin-bottom: 16px;
    }

    .reward-price {
        font-weight: 700;
        color: var(--primary, #15803d);
        font-size: 1rem;
    }

    .reward-stock {
        font-size: 0.85rem;
        color: var(--text-muted, #6b7280);
    }

    .redeem-form {
        background-color: #f9fafb;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid var(--border, #e5e7eb);
        margin-top: auto;
    }

    .redeem-form label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-main, #1f2937);
        margin-bottom: 4px;
    }

    .redeem-form input, 
    .redeem-form textarea {
        padding: 8px 10px;
        font-size: 0.875rem;
        margin-bottom: 10px;
    }

    .redeem-form textarea {
        min-height: 60px;
        resize: vertical;
    }

    .out-of-stock {
        text-align: center;
        padding: 12px;
        background-color: #f3f4f6;
        color: var(--text-muted, #6b7280);
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
        margin-top: auto;
    }

    .empty-rewards {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px 16px;
        background: white;
        border-radius: var(--radius, 12px);
        border: 1px solid var(--border, #e5e7eb);
        color: var(--text-muted, #6b7280);
    }
</style>

<div class="container">
    <!-- Header Halaman -->
    <div class="reward-header">
        <div>
            <h1 class="page-title" style="font-size: 1.5rem; font-weight: 700; margin: 0;">Reward EcoPack</h1>
            <p class="muted" style="margin: 0;">Tukarkan poin ramah lingkungan Anda dengan berbagai penawaran menarik.</p>
        </div>
        <a href="{{ route('rewards.history') }}" class="btn-outline">
            📜 Riwayat Penukaran
        </a>
    </div>

    <!-- Banner Saldo Poin -->
    <div class="balance-card">
        <div class="balance-info">
            <p>Saldo Poin Anda</p>
            <h2>{{ number_format($balance) }} <span style="font-size: 1rem; font-weight: 400;">Poin</span></h2>
        </div>
        <div style="font-size: 2rem;">🌱</div>
    </div>

    <!-- Alert Sukses -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Errors -->
    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Grid List Reward -->
    <div class="reward-grid">
        @forelse ($rewards as $reward)
            <div class="reward-card">
                @if ($reward->image_path)
                    <div class="reward-image-box">
                        <img
                            src="{{ asset('storage/' . $reward->image_path) }}"
                            alt="{{ $reward->name }}"
                            class="reward-image"
                        >
                    </div>
                @endif

                <div class="reward-content">
                    <h2 class="reward-title">{{ $reward->name }}</h2>
                    <p class="reward-desc">{{ $reward->description }}</p>

                    <div class="reward-meta">
                        <span class="reward-price">🎁 {{ number_format($reward->points_required) }} poin</span>
                        <span class="reward-stock">Stok: <strong>{{ $reward->stock }}</strong></span>
                    </div>

                    @if ($reward->stock > 0)
                        <form method="POST"
                              action="{{ route('rewards.redeem', $reward) }}"
                              class="redeem-form">
                            @csrf

                            <label>Jumlah</label>
                            <input type="number" name="quantity"
                                   min="1" max="{{ min(10, $reward->stock) }}"
                                   value="1" required>

                            <label>Nama penerima</label>
                            <input type="text" name="recipient_name"
                                   maxlength="100" placeholder="Nama lengkap penerima" required>

                            <label>Nomor HP</label>
                            <input type="tel" name="phone"
                                   maxlength="20" placeholder="08xxxxxxxxxx" required>

                            <label>Alamat lengkap</label>
                            <textarea name="shipping_address"
                                      maxlength="2000" placeholder="Alamat pengiriman reward" required></textarea>

                            <button type="submit" style="width: 100%; margin-top: 6px;">Tukar Poin</button>
                        </form>
                    @else
                        <div class="out-of-stock">
                            Stok habis.
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-rewards">
                🍃 Belum ada reward aktif yang dapat ditukarkan saat ini.
            </div>
        @endforelse
    </div>
</div>
@endsection