<?php

namespace App\Http\Controllers;

use App\Models\QrCode as QrCodeModel;
use App\Models\ScanLog;
use App\Models\PointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    /**
     * Menampilkan halaman kamera scan.
     */
    public function camera()
    {
        return view('scan.camera');
    }

    /**
     * QR dibuka langsung menggunakan kamera bawaan HP.
     * Proses reward dilakukan sebelum diarahkan ke DPP.
     */
    public function open(Request $request, string $token)
    {
        $result = $this->processReward($request, $token);

        return redirect()
            ->route('dpp.show', [
                'token' => $result['token'],
            ])
            ->with('scan_message', $result['message'])
            ->with('scan_rewarded', $result['rewarded']);
    }

    /**
     * Endpoint untuk hasil scan dari kamera aplikasi.
     */
    public function scan(Request $request, string $token)
    {
        $result = $this->processReward($request, $token);

        return response()->json([
            'success' => true,
            'rewarded' => $result['rewarded'],
            'message' => $result['message'],
            'redirect' => route('dpp.show', [
                'token' => $result['token'],
            ]),
        ]);
    }

    /**
     * Memproses klaim reward dan mencatat semua scan.
     *
     * Satu QR hanya bisa menghasilkan reward satu kali,
     * meskipun dipindai oleh pengguna yang berbeda.
     */
    private function processReward(
        Request $request,
        string $token
    ): array {
        $user = $request->user();

        abort_unless(
            $user,
            401,
            'Silakan login untuk memindai QR.'
        );

        return DB::transaction(function () use ($token, $user) {
            $qr = QrCodeModel::query()
                ->where('token', $token)
                ->where('active', true)
                ->lockForUpdate()
                ->firstOrFail();

            $qr->load('product');

            if (
                !$qr->product ||
                $qr->product->approval_status !== 'approved'
            ) {
                abort(
                    404,
                    'Produk tidak ditemukan atau belum disetujui.'
                );
            }

            $rewarded = false;

            if ($qr->claimed_at === null) {
                // Tandai QR sebagai sudah diklaim.
                $qr->claimed_by = $user->id;
                $qr->claimed_at = now();
                $qr->save();

                // Tambahkan transaksi kredit 100 poin.
                PointTransaction::create([
                    'user_id' => $user->id,
                    'points' => 100,
                    'type' => 'credit',
                    'description' =>
                        'Reward scan pertama QR '
                        . $qr->product->product_code,
                    'reference_type' => 'qr_code',
                    'reference_id' => $qr->id,
                ]);

                $rewarded = true;
            }

            // Semua scan valid dicatat, termasuk scan berulang.
            ScanLog::create([
                'user_id' => $user->id,
                'qr_code_id' => $qr->id,
                'rewarded' => $rewarded,
            ]);

            return [
                'token' => $qr->token,
                'rewarded' => $rewarded,
                'message' => $rewarded
                    ? 'Selamat! Kamu mendapatkan 100 poin.'
                    : 'QR ini sudah pernah diklaim. Kamu tidak mendapatkan poin tambahan, tetapi tetap dapat melihat informasi DPP.',
            ];
        });
    }
}
