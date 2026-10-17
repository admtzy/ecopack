@extends('layouts.app')

@section('content')
<style>
    .form-container {
        max-width: 680px;
        margin: 0 auto;
    }

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

    .card-form-header {
        padding-bottom: 16px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border, #e5e7eb);
    }

    .card-form-header h2 {
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--text-main, #1f2937);
        margin: 0;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-main, #1f2937);
        margin-bottom: 6px;
    }

    .form-group label .required {
        color: #ef4444;
    }

    textarea {
        resize: vertical;
        min-height: 90px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--border, #e5e7eb);
    }
</style>

<div class="container form-container">
    <!-- Header Halaman -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $product->exists ? 'Edit' : 'Tambah' }} Digital Product Passport</h1>
            <p class="muted">Lengkapi data transparansi dan informasi keberlanjutan produk EcoPack.</p>
        </div>
        <div>
            <a href="{{ route('dpp.index') }}" class="btn-outline">
                ← Kembali ke DPP
            </a>
        </div>
    </div>

    <!-- Card Form Utama -->
    <div class="card">
        <div class="card-form-header">
            <h2>Detail Paspor Produk</h2>
        </div>

        <form method="post" action="{{ $product->exists ? route('dpp.update', $product) : route('dpp.store') }}">
            @csrf
            @if($product->exists)
                @method('PUT')
            @endif

            <!-- Kode Produk -->
            <div class="form-group">
                <label>Kode Produk <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="product_code" 
                    value="{{ old('product_code', $product->product_code) }}" 
                    placeholder="Contoh: ECO-BOX-001" 
                    required
                >
            </div>

            <!-- Nama Produk -->
            <div class="form-group">
                <label>Nama Produk <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $product->name) }}" 
                    placeholder="Contoh: Kemasan Biodegradable Singkong 500ml" 
                    required
                >
            </div>

            <!-- Deskripsi -->
            <div class="form-group">
                <label>Deskripsi Produk</label>
                <textarea 
                    name="description" 
                    placeholder="Jelaskan spesifikasi umum, fungsi, dan keunggulan produk ini..."
                >{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Bahan Baku -->
            <div class="form-group">
                <label>Bahan Baku</label>
                <textarea 
                    name="materials" 
                    placeholder="Contoh: Pati singkong (70%), PLA organik (25%), minyak nabati (5%)..."
                >{{ old('materials', $product->materials) }}</textarea>
            </div>

            <!-- Informasi Produksi -->
            <div class="form-group">
                <label>Informasi Produksi</label>
                <textarea 
                    name="production_info" 
                    placeholder="Informasi pabrikasi, jejak karbon produksi, standar ISO, atau lokasi pembuatan..."
                >{{ old('production_info', $product->production_info) }}</textarea>
            </div>

            <!-- Akhir Penggunaan / Daur Ulang -->
            <div class="form-group">
                <label>Akhir Penggunaan / Daur Ulang</label>
                <textarea 
                    name="end_of_life" 
                    placeholder="Petunjuk komposting mandiri, estimasi waktu terurai di tanah (misal: 60 hari), atau fasilitas daur ulang terdekat..."
                >{{ old('end_of_life', $product->end_of_life) }}</textarea>
            </div>

            <!-- Status Approval (Hanya Tampil Saat Edit) -->
            @if($product->exists)
                <div class="form-group">
                    <label>Status Persetujuan (Approval Status)</label>
                    <select name="approval_status">
                        <option value="pending">Pending</option>
                        <option value="approved" @selected($product->approval_status === 'approved')>Approved</option>
                        <option value="rejected" @selected($product->approval_status === 'rejected')>Rejected</option>
                    </select>
                </div>
            @endif

            <!-- Tombol Aksi -->
            <div class="form-actions">
                <a href="{{ route('dpp.index') }}" class="btn-outline">Batal</a>
                <button type="submit">Simpan DPP</button>
            </div>
        </form>
    </div>
</div>
@endsection