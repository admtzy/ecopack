<?php

namespace App\Http\Controllers;


use App\Models\DppProduct;
use App\Models\QrCode as QrCodeModel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class DppController extends Controller
{
    /**
     * Daftar DPP.
     */
    public function index()
    {
        $products = DppProduct::withCount('qrCodes')
            ->latest()
            ->get();

        return view('dpp.index', compact('products'));
    }

    /**
     * Form pembuatan DPP.
     */
    public function create()
    {
        return view('dpp.form', [
            'product' => new DppProduct(),
        ]);
    }

    /**
     * Simpan DPP baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_code' => [
                'required',
                'string',
                'max:80',
                'unique:dpp_products,product_code',
            ],
            'name' => 'required|string|max:180',
            'description' => 'nullable|string',
            'materials' => 'nullable|string',
            'production_info' => 'nullable|string',
            'end_of_life' => 'nullable|string',
        ]);

        $data['created_by'] = $request->user()->id;
        $data['approval_status'] = 'approved';

        $product = DppProduct::create($data);

        return redirect()
            ->route('dpp.qr.create', ['dpp' => $product->id])
            ->with(
                'success',
                'DPP berhasil dibuat. Tentukan jumlah QR yang akan digenerate.'
            );
    }

    /**
     * Form generate QR.
     */
    public function createQr(DppProduct $dpp)
    {
        return view('dpp.generate-qr', [
            'product' => $dpp,
            'qrCount' => $dpp->qrCodes()->count(),
        ]);
    }

    /**
     * Generate QR sesuai jumlah.
     */
    public function storeQr(Request $request, DppProduct $dpp)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        if ($dpp->approval_status !== 'approved') {
            return back()
                ->withErrors([
                    'quantity' => 'QR hanya dapat dibuat untuk DPP yang sudah disetujui.',
                ])
                ->withInput();
        }

        $quantity = (int) $data['quantity'];

        DB::transaction(function () use ($dpp, $quantity) {
            for ($i = 0; $i < $quantity; $i++) {
                QrCodeModel::create([
                    'dpp_product_id' => $dpp->id,
                    'token' => (string) Str::uuid(),
                    'active' => true,
                ]);
            }
        });

        return redirect()
            ->route('dpp.qr.create', ['dpp' => $dpp->id])
            ->with('success', "{$quantity} QR berhasil dibuat.");
    }

    /**
     * Download QR aktif dalam ZIP.
     *
     * Isi ZIP:
     * - qr/*.png
     * - daftar_qr.csv
     */
    public function downloadQrZip(DppProduct $dpp)
    {
        if ($dpp->approval_status !== 'approved') {
            abort(403, 'DPP belum disetujui.');
        }

        $qrCodes = $dpp->qrCodes()
            ->where('active', true)
            ->orderBy('id')
            ->get();

        if ($qrCodes->isEmpty()) {
            return back()->withErrors([
                'quantity' => 'Belum ada QR aktif untuk diunduh.',
            ]);
        }

        if (!class_exists(ZipArchive::class)) {
            abort(500, 'Ekstensi PHP Zip belum aktif.');
        }

        if (!extension_loaded('gd')) {
            abort(500, 'Ekstensi GD PHP belum aktif.');
        }

        /*
         * Gunakan implementasi PNG konkret, bukan QRGdImage
         * yang merupakan class abstrak.
         */
        $pngOutputClass =
            \chillerlan\QRCode\Output\QRGdImagePNG::class;

        if (!class_exists($pngOutputClass)) {
            throw new RuntimeException(
                'Class QRGdImagePNG tidak ditemukan pada library QR.'
            );
        }

        /*
         * Buat file ZIP sementara.
         */
        $tempBase = tempnam(sys_get_temp_dir(), 'ecopack-qr-');

        if ($tempBase === false) {
            abort(500, 'Gagal membuat file sementara.');
        }

        $zipPath = $tempBase . '.zip';

        if (!rename($tempBase, $zipPath)) {
            @unlink($tempBase);

            abort(500, 'Gagal menyiapkan file ZIP.');
        }

        $zip = new ZipArchive();

        $openResult = $zip->open(
            $zipPath,
            ZipArchive::CREATE | ZipArchive::OVERWRITE
        );

        if ($openResult !== true) {
            @unlink($zipPath);

            throw new RuntimeException(
                'Gagal membuka ZIP. Kode: ' . $openResult
            );
        }

        $csv = null;
        $zipClosed = false;

        $baseName = Str::slug($dpp->product_code) ?: 'produk';

        try {
            $totalDigits = max(
                3,
                strlen((string) $qrCodes->count())
            );

            /*
             * CSV sementara.
             */
            $csv = fopen('php://temp', 'w+b');

            if ($csv === false) {
                throw new RuntimeException(
                    'Gagal membuat CSV sementara.'
                );
            }

            // BOM UTF-8 untuk kompatibilitas Excel.
            fwrite($csv, "\xEF\xBB\xBF");

            fputcsv($csv, [
                'Nomor',
                'Nama File',
                'Kode Produk',
                'Nama Produk',
                'Token QR',
                'URL DPP',
            ]);

            /*
             * Versi 20 memberikan kapasitas lebih besar daripada
             * versi 10 untuk URL yang lebih panjang.
             */
            $options = new QROptions([
                'version' => 20,
                'scale' => 8,
                'outputBase64' => false,
                'outputInterface' => $pngOutputClass,
            ]);

            $qrGenerator = new QRCode($options);

            foreach ($qrCodes as $index => $qr) {
                $number = str_pad(
                    (string) ($index + 1),
                    $totalDigits,
                    '0',
                    STR_PAD_LEFT
                );

                $filename = "{$baseName}_{$number}.png";

                /*
                 * URL menuju halaman DPP publik.
                 */
                $dppUrl = route('scan.open', [
                    'token' => $qr->token,
                ]);

                $pngData = $qrGenerator->render($dppUrl);

                /*
                 * Validasi signature PNG.
                 */
                $pngSignature = "\x89PNG\r\n\x1a\n";

                if (
                    !is_string($pngData) ||
                    strlen($pngData) < 8 ||
                    substr($pngData, 0, 8) !== $pngSignature
                ) {
                    throw new RuntimeException(
                        "Output QR bukan PNG valid: {$filename}."
                    );
                }

                if (!$zip->addFromString(
                    "qr/{$filename}",
                    $pngData
                )) {
                    throw new RuntimeException(
                        "Gagal memasukkan {$filename} ke ZIP."
                    );
                }

                fputcsv($csv, [
                    $index + 1,
                    $filename,
                    $dpp->product_code,
                    $dpp->name,
                    $qr->token,
                    $dppUrl,
                ]);
            }

            /*
             * Masukkan CSV ke ZIP.
             */
            if (!rewind($csv)) {
                throw new RuntimeException(
                    'Gagal membaca CSV sementara.'
                );
            }

            $csvContents = stream_get_contents($csv);

            if ($csvContents === false) {
                throw new RuntimeException(
                    'Gagal membaca isi CSV.'
                );
            }

            if (!$zip->addFromString(
                'daftar_qr.csv',
                $csvContents
            )) {
                throw new RuntimeException(
                    'Gagal memasukkan CSV ke ZIP.'
                );
            }

            fclose($csv);
            $csv = null;

            /*
             * Tutup ZIP supaya central directory ditulis.
             */
            if (!$zip->close()) {
                throw new RuntimeException(
                    'Gagal menyelesaikan arsip ZIP.'
                );
            }

            $zipClosed = true;

            clearstatcache(true, $zipPath);

            if (
                !is_file($zipPath) ||
                filesize($zipPath) === 0
            ) {
                throw new RuntimeException(
                    'File ZIP kosong atau tidak ditemukan.'
                );
            }

            /*
             * Pastikan file ZIP dapat dibuka kembali.
             */
            $validator = new ZipArchive();
            $validationResult = $validator->open($zipPath);

            if ($validationResult !== true) {
                throw new RuntimeException(
                    'Validasi ZIP gagal. Kode: '
                    . $validationResult
                );
            }

            $expectedEntries = $qrCodes->count() + 1;
            $actualEntries = $validator->numFiles;

            /*
             * Pastikan jumlah entri sesuai.
             */
            if ($actualEntries !== $expectedEntries) {
                $validator->close();

                throw new RuntimeException(
                    "Isi ZIP tidak lengkap. Seharusnya {$expectedEntries} "
                    . "file, tetapi ditemukan {$actualEntries}."
                );
            }

            /*
             * Pastikan ZIP diawali signature PK yang benar.
             */
            $validator->close();

            $handle = fopen($zipPath, 'rb');

            if ($handle === false) {
                throw new RuntimeException(
                    'File ZIP tidak dapat dibaca.'
                );
            }

            $signature = fread($handle, 4);
            fclose($handle);

            if (
                $signature !== "PK\x03\x04" &&
                $signature !== "PK\x05\x06" &&
                $signature !== "PK\x07\x08"
            ) {
                throw new RuntimeException(
                    'Signature awal file ZIP tidak valid.'
                );
            }
        } catch (\Throwable $e) {
            if (is_resource($csv)) {
                fclose($csv);
            }

            if (!$zipClosed) {
                @$zip->close();
            }

            @unlink($zipPath);

            report($e);

            abort(
                500,
                'Gagal membuat ZIP QR. Periksa storage/logs/laravel.log.'
            );
        }

        /*
         * Siapkan respons unduhan.
         */
        $response = response()->download(
            $zipPath,
            "{$baseName}_qr-codes.zip",
            [
                'Content-Type' => 'application/zip',
                'Content-Disposition' =>
                    'attachment; filename="' .
                    "{$baseName}_qr-codes.zip" . '"',
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ]
        )->deleteFileAfterSend(true);

        /*
         * Hapus buffer output yang mungkin berisi karakter
         * tambahan sebelum file biner dikirim.
         */
        while (ob_get_level() > 0) {
            if (!@ob_end_clean()) {
                break;
            }
        }

        return $response;
    }

    /**
     * Form edit DPP.
     */
    public function edit(DppProduct $dpp)
    {
        return view('dpp.form', [
            'product' => $dpp,
        ]);
    }

    /**
     * Perbarui DPP.
     */
    public function update(Request $request, DppProduct $dpp)
    {
        $data = $request->validate([
            'product_code' => [
                'required',
                'string',
                'max:80',
                'unique:dpp_products,product_code,' . $dpp->id,
            ],
            'name' => 'required|string|max:180',
            'description' => 'nullable|string',
            'materials' => 'nullable|string',
            'production_info' => 'nullable|string',
            'end_of_life' => 'nullable|string',
            'approval_status' => 'required|in:pending,approved,rejected',
        ]);

        $dpp->update($data);

        return redirect()
            ->route('dpp.index')
            ->with('success', 'DPP berhasil diperbarui.');
    }

    /**
     * Hapus DPP.
     */
    public function destroy(DppProduct $dpp)
    {
        $dpp->delete();

        return redirect()
            ->route('dpp.index')
            ->with('success', 'DPP berhasil dihapus.');
    }

    /**
     * Detail DPP publik berdasarkan token QR.
     */
    public function showQr(string $token)
    {
        $qr = QrCodeModel::with('product')
            ->where('token', $token)
            ->where('active', true)
            ->firstOrFail();

        if (
            !$qr->product ||
            $qr->product->approval_status !== 'approved'
        ) {
            abort(404);
        }

        return view('dpp.show', [
            'qr' => $qr,
            'product' => $qr->product,
        ]);
    }
}
