@extends('layouts.app')

@section('content')
<style>
    .auth-container {
        max-width: 480px;
        margin: 32px auto;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 24px;
    }

    .auth-header h1 {
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

    .btn-block {
        width: 100%;
        margin-top: 10px;
        padding: 12px;
        font-size: 1rem;
        font-weight: 600;
    }

    .auth-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--border, #e5e7eb);
        font-size: 0.9rem;
    }

    .auth-footer a {
        color: var(--primary, #15803d);
        font-weight: 600;
        text-decoration: none;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }
</style>

<div class="auth-container">
    <div class="card">
        <!-- Header Registrasi -->
        <div class="auth-header">
            <div style="font-size: 2.5rem; margin-bottom: 8px;">🌱</div>
            <h1>Registrasi EcoPack</h1>
            <p class="muted">Buat akun baru untuk mulai menggunakan platform EcoPack</p>
        </div>

        <!-- Form Registrasi -->
        <form method="post" action="{{ route('register.post') }}">
            @csrf

            <!-- Field Nama -->
            <div class="form-group">
                <label>Nama Lengkap <span class="required">*</span></label>
                <input 
                    type="text"
                    name="name" 
                    placeholder="Masukkan nama lengkap Anda"
                    required
                >
            </div>

            <!-- Field Email -->
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input 
                    type="email" 
                    name="email" 
                    placeholder="nama@email.com"
                    required
                >
            </div>

            <!-- Field Role -->
            <div class="form-group">
                <label>Role / Tipe Akun</label>
                <select name="role">
                    <option value="user">User / Pengguna</option>
                    <option value="mitra">Mitra Perusahaan</option>
                </select>
            </div>

            <!-- Field Password -->
            <div class="form-group">
                <label>Password (min. 8 karakter) <span class="required">*</span></label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                >
            </div>

            <!-- Field Konfirmasi Password -->
            <div class="form-group">
                <label>Konfirmasi Password <span class="required">*</span></label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="••••••••"
                    required
                >
            </div>

            <!-- Tombol Buat Akun -->
            <button type="submit" class="btn btn-block">
                Buat Akun
            </button>
        </form>

        <!-- Footer Tautan Login -->
        <div class="auth-footer">
            <p style="margin: 0;">
                Sudah memiliki akun? <a href="{{ route('login') }}">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection