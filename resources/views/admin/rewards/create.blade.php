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
</style>

<div class="container form-container">
    <!-- Header Halaman -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Reward Baru</h1>
            <p class="muted">Tambahkan barang atau penawaran reward baru ke dalam katalog EcoPack.</p>
        </div>
        <div>
            <a href="{{ route('admin.rewards.index') }}" class="btn-outline">
                ← Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Card Form Tambah Reward -->
    <div class="card">
        <div class="card-form-header">
            <h2>Informasi Reward</h2>
        </div>

        @include('admin.rewards.form', [
            'reward' => $reward,
            'action' => route('admin.rewards.store'),
            'method' => 'POST',
        ])
    </div>
</div>
@endsection