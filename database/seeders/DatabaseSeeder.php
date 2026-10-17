<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder; use App\Models\{User,Company,DppProduct,QrCode,Reward}; use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder { public function run(): void {
User::create(['name'=>'EcoPack Admin','email'=>'admin@ecopack.test','password'=>'password','role'=>'admin']);
User::create(['name'=>'Demo User','email'=>'user@ecopack.test','password'=>'password','role'=>'user']);
$m=User::create(['name'=>'Demo Mitra','email'=>'mitra@ecopack.test','password'=>'password','role'=>'mitra']);
Company::create(['user_id'=>$m->id,'name'=>'PT Mitra Hijau','address'=>'Jember','contact_name'=>'PIC Demo','phone'=>'081234567890']);
$p=DppProduct::create(['product_code'=>'ECP-0001','name'=>'Kemasan Serat Alami','description'=>'Kemasan biodegradable EcoPack.','materials'=>'Serat singkong dan rumput gajah','production_info'=>'Informasi produksi contoh','end_of_life'=>'Ikuti petunjuk pengolahan limbah setempat','approval_status'=>'approved']);
QrCode::create(['dpp_product_id'=>$p->id,'token'=>Str::random(40)]);
Reward::create(['name'=>'Eco Tote Bag','description'=>'Tas pakai ulang','points_required'=>800,'stock'=>20,'active'=>true]);
Reward::create(['name'=>'Botol Minum','description'=>'Botol minum pakai ulang','points_required'=>1500,'stock'=>10,'active'=>true]);
} }
