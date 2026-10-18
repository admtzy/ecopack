@extends('layouts.app')

@section('content')
<style>
    .auth-container {
        max-width: 420px;
        margin: 40px auto;
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

    .btn-block {
        width: 100%;
        margin-top: 8px;
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

    .demo-box {
        background-color: #f8fafc;
        border: 1px dashed var(--border, #cbd5e1);
        border-radius: 8px;
        padding: 10px 14px;
        margin-top: 16px;
        font-size: 0.85rem;
        text-align: center;
    }
</style>

<div class="auth-container">
    <div class="card">
        <!-- Header Login -->
        <div class="auth-header">
            <div style="font-size: 2.5rem; margin-bottom: 8px;">🌱</div>
            <h1>Login EcoPack</h1>
            <p class="muted">Masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <!-- Form Login -->
        <form method="post" action="{{ route('login.post') }}">
            @csrf

            <!-- Email Field -->
            <div class="form-group">
                <label>Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="nama@email.com"
                    required
                >
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label>Password</label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                >
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn btn-block">
                Login
            </button>
        </form>

        <!-- Footer / Link Daftar -->
        <div class="auth-footer">
            <p style="margin: 0;">
                Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
            </p>

            <!-- Informasi Akun Demo -->
            <!-- <div class="demo-box">
                <span class="muted">Demo: <strong>admin@ecopack.test</strong> / <strong>password</strong></span>
            </div> -->
        </div>
    </div>
</div>
@endsection