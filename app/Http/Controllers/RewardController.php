<?php
namespace App\Http\Controllers;


use App\Models\Reward;
use App\Models\Redemption;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RewardController extends Controller
{
    // =========================
    // PENGGUNA: KATALOG REWARD
    // =========================

    public function index()
    {
        $balance = (int) PointTransaction::where('user_id', auth()->id())
            ->sum('points');

        $rewards = Reward::where('active', true)
            ->orderBy('points_required')
            ->get();

        return view('rewards.index', compact('rewards', 'balance'));
    }

    // =========================
    // PENGGUNA: TUKAR POIN
    // =========================

    public function redeem(Request $request, Reward $reward)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:2000',
        ]);

        DB::transaction(function () use ($data, $reward) {
            // Kunci baris pengguna agar dua penukaran bersamaan
            // tidak sama-sama memakai saldo yang sama.
            $user = User::whereKey(auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            $rw = Reward::whereKey($reward->id)
                ->lockForUpdate()
                ->firstOrFail();

            $quantity = (int) $data['quantity'];

            if (!$rw->active) {
                throw ValidationException::withMessages([
                    'quantity' => 'Reward sudah tidak aktif.',
                ]);
            }

            if ($rw->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok reward tidak mencukupi.',
                ]);
            }

            $cost = $rw->points_required * $quantity;

            $balance = (int) PointTransaction::where('user_id', $user->id)
                ->sum('points');

            if ($balance < $cost) {
                throw ValidationException::withMessages([
                    'quantity' => 'Saldo poin kamu tidak mencukupi.',
                ]);
            }

            $redemption = Redemption::create([
                'user_id' => $user->id,
                'reward_id' => $rw->id,
                'quantity' => $quantity,
                'points_spent' => $cost,
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'shipping_address' => $data['shipping_address'],
                'status' => 'pending',
            ]);

            $rw->decrement('stock', $quantity);

            PointTransaction::create([
                'user_id' => $user->id,
                'points' => -$cost,
                'type' => 'debit',
                'description' => 'Penukaran ' . $rw->name,
                'reference_type' => 'redemption',
                'reference_id' => $redemption->id,
            ]);
        });

        return redirect()
            ->route('rewards.history')
            ->with('success', 'Penukaran berhasil diajukan.');
    }

    // =========================
    // PENGGUNA: RIWAYAT
    // =========================

    public function history()
    {
        $redemptions = Redemption::with('reward')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('rewards.history', compact('redemptions'));
    }

    // =========================
    // ADMIN: DAFTAR REWARD
    // =========================

    public function adminIndex()
    {
        $rewards = Reward::latest()->get();

        return view('admin.rewards.index', compact('rewards'));
    }

    public function create()
    {
        return view('admin.rewards.create', [
            'reward' => new Reward(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:5000',
            'points_required' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['active'] = $request->boolean('active');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')
                ->store('rewards', 'public');
        }

        unset($data['image']);

        Reward::create($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Reward berhasil ditambahkan.');
    }

    public function edit(Reward $reward)
    {
        return view('admin.rewards.edit', compact('reward'));
    }

    public function update(Request $request, Reward $reward)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:5000',
            'points_required' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['active'] = $request->boolean('active');

        if ($request->hasFile('image')) {
            if ($reward->image_path) {
                Storage::disk('public')->delete($reward->image_path);
            }

            $data['image_path'] = $request->file('image')
                ->store('rewards', 'public');
        }

        unset($data['image']);

        $reward->update($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Reward berhasil diperbarui.');
    }

    public function destroy(Reward $reward)
    {
        // Jangan menghapus reward yang sudah punya riwayat.
        if ($reward->redemptions()->exists()) {
            $reward->update(['active' => false]);

            return back()->with(
                'success',
                'Reward memiliki riwayat, sehingga dinonaktifkan.'
            );
        }

        if ($reward->image_path) {
            Storage::disk('public')->delete($reward->image_path);
        }

        $reward->delete();

        return back()->with('success', 'Reward berhasil dihapus.');
    }

    // =========================
    // ADMIN: DAFTAR PENUKARAN
    // =========================

    public function redemptions()
    {
        $items = Redemption::with(['user', 'reward'])
            ->latest()
            ->get();

        return view('admin.redemptions', compact('items'));
    }

    // =========================
    // ADMIN: STATUS DAN REFUND
    // =========================

    public function updateStatus(
        Request $request,
        Redemption $redemption
    ) {
        $data = $request->validate([
            'status' => 'required|in:processing,shipped,completed,rejected',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($data, $redemption) {
            $red = Redemption::whereKey($redemption->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($red->status, ['rejected', 'completed'], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Penukaran ini sudah berstatus final.',
                ]);
            }

            $next = $data['status'];

            if ($red->status === 'pending') {
                if ($next === 'rejected') {
                    $user = User::whereKey($red->user_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $alreadyRefunded = PointTransaction::where(
                        'reference_type', 'redemption'
                    )
                        ->where('reference_id', $red->id)
                        ->where('type', 'refund')
                        ->exists();

                    if (!$alreadyRefunded) {
                        PointTransaction::create([
                            'user_id' => $user->id,
                            'points' => $red->points_spent,
                            'type' => 'refund',
                            'description' => 'Refund penukaran #' . $red->id,
                            'reference_type' => 'redemption',
                            'reference_id' => $red->id,
                        ]);

                        Reward::whereKey($red->reward_id)
                            ->increment('stock', $red->quantity);
                    }
                } elseif ($next !== 'processing') {
                    throw ValidationException::withMessages([
                        'status' => 'Penukaran harus diproses atau ditolak terlebih dahulu.',
                    ]);
                }
            } elseif (
                $red->status === 'processing' && $next !== 'shipped'
            ) {
                throw ValidationException::withMessages([
                    'status' => 'Dari processing, status berikutnya harus shipped.',
                ]);
            } elseif (
                $red->status === 'shipped' && $next !== 'completed'
            ) {
                throw ValidationException::withMessages([
                    'status' => 'Dari shipped, status berikutnya harus completed.',
                ]);
            } else {
                throw ValidationException::withMessages([
                    'status' => 'Perubahan status tidak valid.',
                ]);
            }

            $red->update([
                'status' => $next,
                'admin_note' => $data['admin_note'] ?? null,
            ]);
        });

        return back()->with('success', 'Status penukaran berhasil diperbarui.');
    }
}
