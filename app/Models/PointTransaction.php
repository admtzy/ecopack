<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PointTransaction extends Model {
 protected $fillable=['user_id','points','type','description','reference_type','reference_id'];
}
