<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductRequest extends Model {
 protected $fillable=['company_id','product_name','quantity','period','details','status'];
}
