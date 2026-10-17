<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class User extends Authenticatable {
 use HasFactory, Notifiable;
 protected $fillable=['name','email','password','role'];
 protected $hidden=['password','remember_token'];
 protected function casts(): array { return ['email_verified_at'=>'datetime','password'=>'hashed']; }
 public function company(): HasOne { return $this->hasOne(Company::class); }
 public function pointTransactions(): HasMany { return $this->hasMany(PointTransaction::class); }
 public function scans(): HasMany { return $this->hasMany(ScanLog::class); }
 public function redemptions(): HasMany { return $this->hasMany(Redemption::class); }
 public function getPointBalanceAttribute(): int
{
    return (int) $this->pointTransactions()
        ->selectRaw("
            COALESCE(SUM(
                CASE
                    WHEN type = 'credit' THEN points
                    WHEN type = 'debit' THEN -points
                    ELSE 0
                END
            ), 0) AS balance
        ")
        ->value('balance');
}
}
