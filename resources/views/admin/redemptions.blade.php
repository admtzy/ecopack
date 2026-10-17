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

    /* Badge Status Styling */
    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-processing {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .status-shipped {
        background-color: #f3e8ff;
        color: #6b21a8;
    }

    .status-completed {
        background-color: #dcfce7;
        color: #15803d;
    }

    .status-rejected {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .status-default {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    /* Form Ubah Status */
    .update-status-form {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 170px;
    }

    .update-status-form select,
    .update-status-form input {
        margin: 0;
        padding: 6px 10px;
        font-size: 0.85rem;
    }

    .update-status-form button {
        padding: 6px 12px;
        font-size: 0.85rem;
        width: 100%;
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

    .shipping-info {
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .shipping-info .recipient {
        font-weight: 600;
        color: var(--text-main, #1f2937);
    }

    .shipping-info .phone {
        color: var(--text-muted, #6b7280);
        font-size: 0.8rem;
    }

    .shipping-info .address {
        color: var(--text-main, #374151);
        margin-top: 2px;
    }

    .empty-state {
        text-align: center;
        padding: 40px 16px !important;
        color: var(--text-muted, #6b7280);
    }
</style>

<div class="container py-4">

    <!-- Header Halaman -->
    <div class="page-header">
        <h1 class="page-title">Pengelolaan Penukaran Hadiah</h1>
        <p class="muted">Tinjau, proses, dan perbarui status pengiriman klaim hadiah pengguna.</p>
    </div>

    <!-- Alert Sukses -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Error -->
    @if ($errors->any())
        <div class="alert alert-error">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin-top: 6px; margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Tabel Penukaran -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No.</th>
                        <th>User</th>
                        <th>Hadiah</th>
                        <th>Jumlah</th>
                        <th>Poin</th>
                        <th>Pengiriman</th>
                        <th>Status</th>
                        <th style="min-width: 180px;">Ubah Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td style="font-weight: 600; color: var(--text-main, #1f2937);">
                                {{ $item->user->name ?? 'User tidak ditemukan' }}
                            </td>

                            <td style="font-weight: 500;">
                                {{ $item->reward->name ?? 'Hadiah tidak ditemukan' }}
                            </td>

                            <td>
                                <strong>{{ $item->quantity }}</strong> <span class="muted">pcs</span>
                            </td>

                            <td>
                                <span class="points-badge">
                                    {{ number_format($item->points_spent) }} Pts
                                </span>
                            </td>

                            <td>
                                <div class="shipping-info">
                                    <div class="recipient">{{ $item->recipient_name ?? '-' }}</div>
                                    <div class="phone">📞 {{ $item->phone ?? '-' }}</div>
                                    <div class="address">{{ $item->shipping_address ?? '-' }}</div>
                                </div>
                            </td>

                            <td>
                                @php
                                    $statusClass = match(strtolower($item->status ?? 'pending')) {
                                        'pending' => 'status-pending',
                                        'processing' => 'status-processing',
                                        'shipped' => 'status-shipped',
                                        'completed' => 'status-completed',
                                        'rejected' => 'status-rejected',
                                        default => 'status-default'
                                    };
                                @endphp
                                <span class="badge-status {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td>
                                @if (in_array($item->status, ['pending', 'processing', 'shipped']))
                                    <form
                                        action="{{ route('admin.redemptions.update', $item) }}"
                                        method="POST"
                                        class="update-status-form"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <select
                                            name="status"
                                            required
                                        >
                                            @if ($item->status === 'pending')
                                                <option value="pending">Pending</option>
                                                <option value="processing">Diproses</option>
                                                <option value="rejected">Ditolak</option>
                                            @elseif ($item->status === 'processing')
                                                <option value="processing">Diproses</option>
                                                <option value="shipped">Dikirim</option>
                                                <option value="rejected">Ditolak</option>
                                            @elseif ($item->status === 'shipped')
                                                <option value="shipped">Dikirim</option>
                                                <option value="completed">Selesai</option>
                                            @endif
                                        </select>

                                        <input
                                            type="text"
                                            name="admin_note"
                                            placeholder="Catatan admin (opsional)"
                                        >

                                        <button
                                            type="submit"
                                        >
                                            Simpan
                                        </button>
                                    </form>
                                @else
                                    <span class="muted" style="font-size: 0.85rem;">
                                        Tidak dapat diubah
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                🎁 Belum ada klaim penukaran hadiah yang perlu diproses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection