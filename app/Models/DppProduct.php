<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class DppProduct extends Model {
 protected $fillable=['product_code','name','description','materials','production_info','end_of_life','approval_status','created_by'];
 public function qrCodes(): HasMany { return $this->hasMany(QrCode::class,'dpp_product_id'); }
}
