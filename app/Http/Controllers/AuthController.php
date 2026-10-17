<?php
namespace App\Http\Controllers;
use App\Models\{User,Company}; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
 public function showLogin(){return view('auth.login');}
 public function login(Request $r){$data=$r->validate(['email'=>'required|email','password'=>'required']); if(!Auth::attempt($data)) return back()->withErrors(['email'=>'Email atau password salah.'])->onlyInput('email'); $r->session()->regenerate(); return redirect()->route('dashboard');}
 public function showRegister(){return view('auth.register');}
 public function register(Request $r){$d=$r->validate(['name'=>'required|string|max:120','email'=>'required|email|unique:users,email','password'=>'required|string|min:8|confirmed','role'=>'required|in:user,mitra']); $u=User::create($d); if($u->role==='mitra') Company::create(['user_id'=>$u->id,'name'=>$u->name]); Auth::login($u); $r->session()->regenerate(); return redirect()->route('dashboard');}
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('login');}
}
