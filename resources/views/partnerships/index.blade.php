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

    /* Form Keputusan Admin di Dalam Tabel */
    .action-form {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 180px;
    }

    .action-form select,
    .action-form input {
        margin: 0;
        padding: 6px 10px;
        font-size: 0.85rem;
    }

    .action-form button {
        padding: 6px 12px;
        font-size: 0.85rem;
        width: 100%;
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

    .badge-approved, .badge-disetujui, .badge-accepted {
        background-color: #dcfce7;
        color: #15803d;
    }

    .badge-rejected, .badge-ditolak {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .badge-default {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-width: 180px;
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
        <h1 class="page-title">Pengajuan Kemitraan</h1>
        <p class="muted">Tinjau dan berikan keputusan untuk pengajuan kemitraan masuk.</p>
    </div>

    <!-- Alert / Pesan Sukses -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Tabel Pengajuan -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nama Mitra</th>
                        <th>Alamat</th>
                        <th>Jenis Kemitraan</th>
                        <th>Jumlah Kebutuhan</th>
                        <th>Periode</th>
                        <th>Detail Kebutuhan</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                        <th style="min-width: 200px;">Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i)
                        <tr>
                            <td style="font-weight: 600;">{{ $i->company?->name ?? '-' }}</td>
                            <td>
                                <div class="text-truncate-2" title="{{ $i->company?->address }}">
                                    {{ $i->company?->address ?? '-' }}
                                </div>
                            </td>
                            <td>
                                @switch($i->request_type)
                                    @case('purchase') Pembelian Produk @break
                                    @case('distribution') Kerja Sama Distribusi @break
                                    @case('environmental_program') Program Lingkungan @break
                                    @default {{ $i->request_type }}
                                @endswitch
                            </td>
                            <td><strong>{{ number_format($i->quantity, 0, ',', '.') }}</strong> <span class="muted">pcs</span></td>
                            <td>{{ $i->period }}</td>
                            <td>
                                <div class="text-truncate-2" title="{{ $i->details }}">
                                    {{ $i->details ?: '-' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match(strtolower($i->status ?? 'pending')) {
                                        'pending' => 'badge-pending',
                                        'approved', 'disetujui', 'accepted' => 'badge-approved',
                                        'rejected', 'ditolak' => 'badge-rejected',
                                        default => 'badge-default'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($i->status ?? 'pending') }}
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate-2 {{ $i->admin_note ? '' : 'muted' }}" title="{{ $i->admin_note }}">
                                    {{ $i->admin_note ?: '-' }}
                                </div>
                            </td>
                            <td>
                                <form
                                    method="POST"
                                    action="{{ route('partnerships.update', $i) }}"
                                    class="action-form"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <select name="status" required>
                                        <option value="approved">Setujui</option>
                                        <option value="rejected">Tolak</option>
                                    </select>

                                    <input
                                        type="text"
                                        name="admin_note"
                                        value="{{ old('admin_note', $i->admin_note) }}"
                                        placeholder="Catatan admin"
                                    >

                                    <button type="submit">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                🍃 Belum ada pengajuan kemitraan yang perlu ditinjau.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection