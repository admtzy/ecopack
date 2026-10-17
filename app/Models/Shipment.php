<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Shipment extends Model {
 protected $fillable=['redemption_id','tracking_number','status','shipped_at','delivered_at'];
 protected function casts(): array { return ['shipped_at'=>'datetime','delivered_at'=>'datetime']; }
}
