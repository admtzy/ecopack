<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class QrCode extends Model {
 protected $fillable=['dpp_product_id','token','claimed_by','claimed_at','active'];
 protected function casts(): array { return ['claimed_at'=>'datetime','active'=>'boolean']; }
 public function product(): BelongsTo { return $this->belongsTo(DppProduct::class,'dpp_product_id'); }
 public function scans(): HasMany { return $this->hasMany(ScanLog::class,'qr_code_id'); }
}
