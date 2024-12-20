<?php

namespace App\Models;

use App\Enums\UserType;
use App\Events\UserCreatedEvent;
use App\Models\Admin\Bank;
use App\Models\Report;
use App\Models\SeamlessTransaction;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Wallet
{
    use HasApiTokens, HasFactory, HasWalletFloat, Notifiable, HasRoles;

    private const PLAYER_ROLE = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'name',
        'profile',
        'email',
        'password',
        'profile',
        'phone',
        'balance',
        'max_score',
        'agent_id',
        'status',
        'type',
        'is_changed_password',
        'referral_code',
    ];

    protected $dispatchesEvents = [
        'created' => UserCreatedEvent::class,
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'type' => UserType::class,
    ];

    public function hasPermission($permission)
    {
        return $this->roles->flatMap->permissions->pluck('name')->contains($permission);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Other users that this user (a master) has created (agents)
    public function createdAgents()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    // The master that created this user (an agent)
    public function createdByMaster()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public static function adminUser()
    {
        return self::where('type', UserType::Admin)->first();
    }

    public function seamlessTransactions()
    {
        return $this->hasMany(SeamlessTransaction::class, 'user_id');
    }

    public function wagers()
    {
        return $this->hasMany(Wager::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function scopeRoleLimited($query)
    {
        $agent = $this->getAgent() ?? Auth::user();

        if (! Auth::user()->hasRole('Admin')) {
            return $query->where('users.agent_id', $agent->id);
        }

        return $query;
    }

    public function scopePlayer($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('role_id', self::PLAYER_ROLE);
        });
    }

    public static function getPlayersByAgentId(int $agentId)
    {
        return self::where('agent_id', $agentId)
            ->whereHas('roles', function ($query) {
                $query->where('title', '!=', 'Agent');
            })
            ->get();
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'agent_id');
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class, 'agent_id');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'payable');
    }

    private function isExistingAgent($userId)
    {
        $user = User::find($userId);
    
        return $user && $user->hasRole(3) ? $user->parent : null;
    }
    
    private function getAgent()
    {
        return $this->isExistingAgent(Auth::id());
    }
    
}