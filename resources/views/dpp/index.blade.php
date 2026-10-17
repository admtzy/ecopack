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
    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .status-approved {
        background-color: #dcfce7;
        color: #15803d;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .status-rejected {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .status-default {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .qr-badge {
        display: inline-block;
        background-color: var(--primary-light, #f0fdf4);
        color: var(--primary, #15803d);
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    /* Group Tombol Aksi */
    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
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

    .empty-state {
        text-align: center;
        padding: 40px 16px !important;
        color: var(--text-muted, #6b7280);
    }
</style>

<div class="container py-4">

    <!-- Header Halaman -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen Digital Product Passport</h1>
            <p class="muted">Kelola data paspor produk, status persetujuan, dan tautan QR Code EcoPack.</p>
        </div>
        <div>
            <a class="btn" href="{{ route('dpp.create') }}">
                + Tambah DPP
            </a>
        </div>
    </div>

    <!-- Card Tabel Data DPP -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Jumlah QR</th>
                        <th style="min-width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                        <tr>
                            <td style="font-weight: 600; font-family: monospace; font-size: 0.95rem;">
                                {{ $p->product_code }}
                            </td>

                            <td style="font-weight: 500; color: var(--text-main, #1f2937);">
                                {{ $p->name }}
                            </td>

                            <td>
                                @php
                                    $statusClass = match(strtolower($p->approval_status ?? 'pending')) {
                                        'approved' => 'status-approved',
                                        'pending' => 'status-pending',
                                        'rejected' => 'status-rejected',
                                        default => 'status-default'
                                    };
                                @endphp
                                <span class="badge-status {{ $statusClass }}">
                                    {{ $p->approval_status }}
                                </span>
                            </td>

                            <td>
                                <span class="qr-badge">
                                    {{ $p->qr_codes_count }} QR
                                </span>
                            </td>

                            <td>
                                <div class="action-group">
                                    <a href="{{ route('dpp.edit', $p) }}" class="btn-outline btn-sm">
                                        Edit
                                    </a>

                                    @if($p->qrCodes->first())
                                        <a target="_blank" 
                                           href="{{ route('dpp.show', $p->qrCodes->first()->token) }}" 
                                           class="btn-outline btn-sm"
                                           style="border-color: #0284c7; color: #0284c7;">
                                            👁️ Lihat DPP
                                        </a>
                                    @endif

                                    <form method="post" 
                                          action="{{ route('dpp.destroy', $p) }}" 
                                          onsubmit="return confirm('Hapus DPP ini?')" 
                                          style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-outline">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection