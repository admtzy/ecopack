<style>
    .form-group {
        margin-bottom: 20px;
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

    /* Preview Gambar */
    .image-preview-box {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 10px;
        padding: 10px 14px;
        background-color: #f9fafb;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 8px;
    }

    .image-preview-box img {
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid var(--border, #e5e7eb);
    }

    .image-preview-info {
        font-size: 0.85rem;
        color: var(--text-muted, #6b7280);
    }

    /* Custom Checkbox */
    .checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--text-main, #1f2937);
        cursor: pointer;
        user-select: none;
    }

    .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin: 0;
        accent-color: var(--primary, #15803d);
        cursor: pointer;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--border, #e5e7eb);
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }
</style>

<!-- Alert Error Validasi -->
@if ($errors->any())
    <div class="alert alert-error">
        <strong>Periksa kembali data inputan Anda:</strong>
        <ul style="margin-top: 8px; margin-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf

    @if ($method === 'PUT')
        @method('PUT')
    @endif

    <!-- Nama Reward -->
    <div class="form-group">
        <label>Nama reward <span class="required">*</span></label>
        <input name="name"
               type="text"
               value="{{ old('name', $reward->name) }}"
               placeholder="Contoh: Tumbler Ramah Lingkungan EcoPack"
               maxlength="150" 
               required>
    </div>

    <!-- Deskripsi -->
    <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="description" 
                  placeholder="Jelaskan detail produk reward ini...">{{ old('description', $reward->description) }}</textarea>
    </div>

    <!-- Gambar -->
    <div class="form-group">
        <label>Gambar Produk</label>
        <input type="file" 
               name="image"
               accept="image/jpeg,image/png,image/webp">

        @if ($reward->image_path)
            <div class="image-preview-box">
                <img src="{{ asset('storage/' . $reward->image_path) }}"
                     alt="{{ $reward->name }}" 
                     width="80" 
                     height="80">
                <div class="image-preview-info">
                    <strong>Gambar Saat Ini</strong>
                    <p class="muted" style="margin: 2px 0 0 0;">Unggah gambar baru jika ingin mengganti.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Row Harga Poin & Stok -->
    <div class="form-row">
        <div class="form-group">
            <label>Harga poin <span class="required">*</span></label>
            <input type="number" 
                   name="points_required"
                   min="1"
                   placeholder="Contoh: 500"
                   value="{{ old('points_required', $reward->points_required) }}"
                   required>
        </div>

        <div class="form-group">
            <label>Stok <span class="required">*</span></label>
            <input type="number" 
                   name="stock"
                   min="0"
                   placeholder="Contoh: 50"
                   value="{{ old('stock', $reward->stock ?? 0) }}"
                   required>
        </div>
    </div>

    <!-- Checkbox Status Aktif -->
    <div class="form-group" style="margin-top: 8px;">
        <label class="checkbox-label">
            <input type="checkbox" 
                   name="active" 
                   value="1"
                   @checked(old('active', $reward->exists ? $reward->active : true))>
            Tampilkan dalam katalog (Aktif)
        </label>
    </div>

    <!-- Tombol Simpan & Batal -->
    <div class="form-actions">
        <a href="{{ route('admin.rewards.index') }}" class="btn-outline">Batal</a>
        <button type="submit">Simpan</button>
    </div>
</form>