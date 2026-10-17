<?php
namespace App\Models; 

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnershipRequest extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'request_type',
        'quantity',
        'period',
        'details',
        'status',
        'admin_note',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
