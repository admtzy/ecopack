<?php
namespace App\Models; 


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Redemption extends Model
{
    protected $fillable = [
        'user_id',
        'reward_id',
        'quantity',
        'points_spent',
        'recipient_name',
        'phone',
        'shipping_address',
        'status',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'points_spent' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }
}
