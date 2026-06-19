<?php

namespace Alyani\Subsystem\Models;

use Alyani\Subsystem\Enums\Currency;
use Alyani\Subsystem\Enums\Gender;
use Alyani\Subsystem\Enums\UserStatus;
use Alyani\Subsystem\Models\Traits\Finance\HasBalance;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasFactory;
    use SoftDeletes;
    use HasBalance;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'family',
        'nickname',
        'company_name',
        'gender',
        'phone',
        'birth_date',
        'avatarSID',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static $publicFields = [
        'id',
        'name',
        'family',
        'mobile',
    ];

    protected $dates = [
        'mobile_verified_at',
        'email_verified_at',
        'last_activity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'family' => 'string',
            'nickname' => 'string',
            'country_code' => 'integer',
            'mobile' => 'string',
            'company_name' => 'string',
            'gender' => Gender::class,
            'balance' => 'integer',
            'currency' => Currency::class,
            'national_code' => 'string',
            'phone' => 'string',
            'birth_date' => 'integer',
            'avatarSID' => 'string',
            'status' => UserStatus::class,
            'referee_user_id' => 'integer',
            'referral_code' => 'string',
            'referred_users_count' => 'integer',
            'score' => 'integer',
            'token_balance' => 'integer',
            'mobile_verified_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'last_activity' => 'datetime',
            'email' => 'string',
        ];
    }

    /**
     * Generate referalCode
     */
    public static function generateReferralCode()
    {
        $length = 5;
        while (true) {
            $code = substr(str_shuffle('123456789abcdefghijklmnopqrstuvw'), 1, $length++);
            $user = static::where('referral_code', $code)->first();
            if (!$user) {
                break;
            }
        }
        return $code;
    }

    public function information()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id');
    }

    /**
     * Get user's full name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->lastName . ' ' . $this->firstName,
        );
    }

    /**
     * exchange balance to display currency.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function balanceToDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => exchange($this->balance, $this->currency->value, $this->currency->display()),
        );
    }
}
