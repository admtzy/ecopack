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

    .card-title-box {
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border, #e5e7eb);
    }

    .card-title-box h2 {
        font-size: 1.15rem;
        font-weight: 600;
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
            <h1 class="page-title">Dashboard Mitra</h1>
            <p class="muted">Kelola dan pantau status pengajuan kemitraan Anda</p>
        </div>
        <div>
            <a class="btn" href="{{ route('partnerships.create') }}">
                + Ajukan Kemitraan
            </a>
        </div>
    </div>

    <!-- Alert / Pesan Sukses -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Tabel Riwayat -->
    <div class="card">
        <div class="card-title-box">
            <h2>Riwayat Pengajuan Kemitraan</h2>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Jenis Kemitraan</th>
                        <th>Nama Mitra</th>
                        <th>Jumlah Kebutuhan</th>
                        <th>Periode</th>
                        <th>Detail Kebutuhan</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $r)
                        <tr>
                            <td style="font-weight: 500;">
                                @switch($r->request_type)
                                    @case('purchase')
                                        Pembelian Produk
                                        @break
                                    @case('distribution')
                                        Kerja Sama Distribusi
                                        @break
                                    @case('environmental_program')
                                        Program Lingkungan
                                        @break
                                    @default
                                        {{ $r->request_type }}
                                @endswitch
                            </td>
                            <td>{{ $r->company?->name ?? '-' }}</td>
                            <td><strong>{{ number_format($r->quantity, 0, ',', '.') }}</strong> <span class="muted">pcs</span></td>
                            <td>
                                @switch($r->period)
                                    @case('daily') Harian @break
                                    @case('weekly') Mingguan @break
                                    @case('monthly') Bulanan @break
                                    @case('once') Satu Kali @break
                                    @case('custom') Khusus @break
                                    @default {{ $r->period }}
                                @endswitch
                            </td>
                            <td>
                                <div class="text-truncate-2" title="{{ $r->details }}">
                                    {{ $r->details ?: '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate-2" title="{{ $r->company?->address }}">
                                    {{ $r->company?->address ?? '-' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match(strtolower($r->status ?? 'pending')) {
                                        'pending' => 'badge-pending',
                                        'approved', 'disetujui', 'accepted' => 'badge-approved',
                                        'rejected', 'ditolak' => 'badge-rejected',
                                        default => 'badge-default'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($r->status ?? 'pending') }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $r->admin_note ? '' : 'muted' }}">
                                    {{ $r->admin_note ?: '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                🍃 Belum ada pengajuan kemitraan yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection