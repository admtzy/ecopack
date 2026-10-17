<?php
namespace App\Http\Controllers;
use App\Models\{User,DppProduct,QrCode,ScanLog,Reward,Redemption,PartnershipRequest};
class DashboardController extends Controller { public function index(){ $u=auth()->user(); if($u->role==='admin') return view('admin.dashboard',['users'=>User::count(),'products'=>DppProduct::count(),'scans'=>ScanLog::count(),'redemptions'=>Redemption::count(),'requests'=>PartnershipRequest::count()]); if($u->role==='mitra') return view('mitra.dashboard',['requests'=>$u->company?->partnershipRequests()->latest()->get() ?? collect()]); return view('user.dashboard',['balance'=>$u->point_balance,'scans'=>$u->scans()->with('qrCode.product')->latest()->take(10)->get(),'redemptions'=>$u->redemptions()->latest()->take(5)->get()]); } }
