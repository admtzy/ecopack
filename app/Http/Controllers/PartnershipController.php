<?php
namespace App\Http\Controllers;


use App\Models\PartnershipRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    public function index()
    {
        $items = PartnershipRequest::with('company')
            ->latest()
            ->get();

        return view('partnerships.index', compact('items'));
    }

    public function create()
    {
        return view('partnerships.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'request_type' => 'required|in:purchase,distribution,environmental_program',
            'quantity'     => 'required|integer|min:1',
            'period'       => 'required|in:daily,weekly,monthly,once,custom',
            'details'      => 'required|string',
            'name'         => 'required|string|max:255',
            'address'      => 'required|string',
        ]);

        $company = Company::firstOrCreate(
            ['user_id' => auth()->id()],
            ['name' => auth()->user()->name]
        );

        // Simpan identitas mitra
        $company->update([
            'name' => $data['name'],
            'address' => $data['address'],
        ]);

        // Simpan detail pengajuan

        $company->partnershipRequests()->create([
            'user_id'      => auth()->id(),
            'request_type' => $data['request_type'],
            'quantity'     => $data['quantity'],
            'period'       => $data['period'],
            'details'      => $data['details'],
            'status'       => 'pending',
        ]);


        return redirect()
            ->route('dashboard')
            ->with('success', 'Pengajuan kemitraan berhasil dikirim.');
    }

    public function updateStatus(
        Request $request,
        PartnershipRequest $partnership
    ) {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $partnership->update($data);

        return back()->with(
            'success',
            'Status pengajuan diperbarui.'
        );
    }
}
