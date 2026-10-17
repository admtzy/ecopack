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

    /* Badge Status */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .badge-active {
        background-color: #dcfce7;
        color: #15803d;
    }

    .badge-inactive {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    /* Group Tombol Aksi */
    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 6px;
    }

    .btn-danger-outline {
        background: transparent;
        color: #dc2626;
        border: 1px solid #fca5a5;
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;
    }

    .btn-danger-outline:hover {
        background-color: #fef2f2;
        color: #b91c1c;
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

    .empty-state {
        text-align: center;
        padding: 40px 16px !important;
        color: var(--text-muted, #6b7280);
    }
</style>

<div class="container">
    <!-- Header Halaman & Tombol Tambah -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Kelola Reward</h1>
            <p class="muted">Atur katalog reward, stok, dan status aktif untuk pengguna EcoPack.</p>
        </div>
        <div>
            <a href="{{ route('admin.rewards.create') }}" class="btn">
                + Tambah Reward
            </a>
        </div>
    </div>

    <!-- Alert Sukses -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Tabel Kelola Reward -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Harga Poin</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th style="min-width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rewards as $reward)
                        <tr>
                            <td style="font-weight: 600; color: var(--text-main, #1f2937);">
                                {{ $reward->name }}
                            </td>
                            <td>
                                <span class="points-badge">
                                    {{ number_format($reward->points_required) }} Pts
                                </span>
                            </td>
                            <td>
                                <strong>{{ $reward->stock }}</strong> <span class="muted">pcs</span>
                            </td>
                            <td>
                                <span class="badge {{ $reward->active ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $reward->active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('admin.rewards.edit', $reward) }}" class="btn-outline btn-sm">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.rewards.destroy', $reward) }}"
                                          style="display: inline;"
                                          onsubmit="return confirm('Hapus atau nonaktifkan reward ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-outline">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                🎁 Belum ada data reward. Klik "+ Tambah Reward" untuk membuat baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection