@extends('layouts.app')

@section('content')
<style>
    .form-container {
        max-width: 720px;
        margin: 0 auto;
    }

    .form-header {
        margin-bottom: 24px;
    }

    .form-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main, #1f2937);
        margin-bottom: 6px;
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid var(--border, #e5e7eb);
    }
</style>

<div class="container form-container">
    <div class="card">
        <!-- Header Form -->
        <div class="form-header">
            <h1>Form Pengajuan Kemitraan</h1>
            <p class="muted">Lengkapi informasi mitra dan kebutuhan kerja sama EcoPack.</p>
        </div>

        <!-- Notification Errors -->
        @if($errors->any())
            <div class="alert alert-error">
                <strong>Periksa kembali data berikut:</strong>
                <ul style="margin-top: 8px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form utama -->
        <form method="POST" action="{{ route('partnerships.store') }}">
            @csrf

            <!-- Nama Mitra -->
            <div class="form-group">
                <label>Nama Mitra / Perusahaan <span class="required">*</span></label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', auth()->user()->company?->name ?? auth()->user()->name) }}"
                    placeholder="Masukkan nama perusahaan/organisasi"
                    required
                >
            </div>

            <!-- Alamat Mitra -->
            <div class="form-group">
                <label>Alamat Mitra <span class="required">*</span></label>
                <textarea 
                    name="address" 
                    placeholder="Masukkan alamat lengkap kantor atau lokasi operasional"
                    required
                >{{ old('address', auth()->user()->company?->address) }}</textarea>
            </div>

            <!-- Jenis Kemitraan -->
            <div class="form-group">
                <label>Jenis Kemitraan <span class="required">*</span></label>
                <select name="request_type" required>
                    <option value="purchase" @selected(old('request_type') === 'purchase')>
                        Pembelian Produk
                    </option>
                    <option value="distribution" @selected(old('request_type') === 'distribution')>
                        Kerja Sama Distribusi
                    </option>
                    <option value="environmental_program" @selected(old('request_type') === 'environmental_program')>
                        Program Lingkungan
                    </option>
                </select>
            </div>

            <!-- Row Jumlah & Periode Kebutuhan -->
            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Kebutuhan (pcs) <span class="required">*</span></label>
                    <input
                        type="number"
                        name="quantity"
                        min="1"
                        placeholder="Contoh: 1000"
                        value="{{ old('quantity') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Periode Kebutuhan <span class="required">*</span></label>
                    <select name="period" required>
                        <option value="daily" @selected(old('period') === 'daily')>Harian</option>
                        <option value="weekly" @selected(old('period') === 'weekly')>Mingguan</option>
                        <option value="monthly" @selected(old('period') === 'monthly')>Bulanan</option>
                        <option value="once" @selected(old('period', 'once') === 'once')>Satu Kali</option>
                        <option value="custom" @selected(old('period') === 'custom')>Khusus</option>
                    </select>
                </div>
            </div>

            <!-- Detail Kebutuhan -->
            <div class="form-group">
                <label>Detail Kebutuhan <span class="required">*</span></label>
                <textarea
                    name="details"
                    placeholder="Jelaskan produk, spesifikasi, tujuan kerja sama, atau kebutuhan lainnya..."
                    required
                >{{ old('details') }}</textarea>
            </div>

            <!-- Tombol Submit & Batal -->
            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="btn-outline">Batal</a>
                <button type="submit">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>
@endsection