<?php

namespace App\Models;


use App\Enums\Ask;
use App\Enums\Status;
use App\Enums\Activity;
use App\Models\Address;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Scopes\BranchScope;
use Spatie\Permission\Models\Role;
use App\Traits\MultiTenantModelTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements HasMedia
{
    use InteractsWithMedia;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use MultiTenantModelTrait;
    use Notifiable;
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = "users";
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'phone',
        'branch_id',
        'country_code',
        'is_guest',
        'status',
        'email_verified_at',
        'whatsapp_phone_number',
        'referral_code',
        'birthday',
        'whatsapp_country_code',
        'discount_id_photo',
        'my_referral_code',
        'is_phone_verified',
        'is_email_verified',
        'is_whatsapp_verified',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id'                => 'integer',
        'name'              => 'string',
        'email'             => 'string',
        'password'          => 'string',
        'username'          => 'string',
        'phone'             => 'string',
        'branch_id'         => 'integer',
        'country_code'      => 'string',
        'is_guest'          => 'integer',
        'status'            => 'integer',
        'email_verified_at' => 'datetime',
        'whatsapp_phone_number' => 'string',
        'referral_code'         => 'string',
        'birthday'              => 'date',
        'whatsapp_country_code' => 'string',
        'discount_id_photo'     => 'string',
        'my_referral_code' => 'string',


    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $appends = ['myrole'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new BranchScope());

        static::creating(function ($user) {
            if (empty($user->my_referral_code)) {
                $user->my_referral_code = self::generateReferralCode();
            }
        });

        static::updating(function ($user) {
            if ($user->id === 1) {
                $user->status = Status::ACTIVE;
            }
        });
    }

    public function getMyRoleAttribute()
    {
        return $this->roles->pluck('id', 'id')->first();
    }

    public function getrole(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Role::class, 'id', 'myrole');
    }

    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function getFirstNameAttribute(): string
    {
        $name = explode(' ', $this->name, 2);
        return $name[0];
    }

    public function getLastNameAttribute(): string
    {
        $name = explode(' ', $this->name, 2);
        return !empty($name[1]) ? $name[1] : '';
    }

    public function getImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('profile'))) {
            return asset($this->getFirstMediaUrl('profile'));
        }
        return asset('images/default/profile.png');
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MessageHistory::class, 'user_id', 'id')->where('is_read', Ask::NO);
    }

    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites')->withTimestamps();
    }
    // Method to generate unique referral code
    public static function generateReferralCode()
    {
        $prefix = 'PEKING-'; 
        $code = $prefix . strtoupper(Str::random(6));

        while (self::where('my_referral_code', $code)->exists()) {
            $code = $prefix . strtoupper(Str::random(6));
        }

        return $code;
    }
}
