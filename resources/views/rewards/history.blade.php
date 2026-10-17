@extends('layouts.app')

@section('content')
<style>
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

    /* Badge Status Styling */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .badge-pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .badge-completed, .badge-shipped, .badge-processed, .badge-disetujui {
        background-color: #dcfce7;
        color: #15803d;
    }

    .badge-rejected, .badge-canceled, .badge-ditolak {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .badge-default {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .points-badge {
        display: inline-block;
        background-color: var(--primary-light, #f0fdf4);
        color: var(--primary, #15803d);
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-width: 200px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 16px !important;
        color: var(--text-muted, #6b7280);
    }
</style>

<div class="container">
    <!-- Header Halaman -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Riwayat Penukaran</h1>
            <p class="muted">Lacak status penukaran poin reward EcoPack Anda</p>
        </div>
        <div>
            <a href="{{ route('rewards.index') }}" class="btn-outline">
                ← Kembali ke katalog
            </a>
        </div>
    </div>

    <!-- Alert Sukses -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Tabel Riwayat Penukaran -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Reward</th>
                        <th>Jumlah</th>
                        <th>Poin</th>
                        <th>Penerima</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($redemptions as $item)
                        <tr>
                            <td style="font-weight: 600;">
                                {{ $item->reward->name ?? 'Reward tidak tersedia' }}
                            </td>
                            <td>{{ $item->quantity }} pcs</td>
                            <td>
                                <span class="points-badge">
                                    {{ number_format($item->points_spent) }} Pts
                                </span>
                            </td>
                            <td>
                                <strong>{{ $item->recipient_name }}</strong>
                                <br>
                                <span class="muted" style="font-size: 0.85rem;">📞 {{ $item->phone }}</span>
                            </td>
                            <td>
                                <div class="text-truncate-2" title="{{ $item->shipping_address }}">
                                    {{ $item->shipping_address }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match(strtolower($item->status ?? 'pending')) {
                                        'pending' => 'badge-pending',
                                        'completed', 'shipped', 'processed', 'disetujui' => 'badge-completed',
                                        'rejected', 'canceled', 'ditolak' => 'badge-rejected',
                                        default => 'badge-default'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $item->admin_note ? '' : 'muted' }}">
                                    {{ $item->admin_note ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                🎁 Belum ada riwayat penukaran reward.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection