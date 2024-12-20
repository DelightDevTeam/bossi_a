<?php

namespace Database\Seeders;

use App\Enums\TransactionName;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $admin = $this->createUser(UserType::Admin, 'Owner', 'bossi', '09123456789');
        (new WalletService)->deposit($admin, 10 * 100_000, TransactionName::CapitalDeposit);

        $agent_1 = $this->createUser(UserType::Agent, 'Agent 1', 'A898737', '09112345674', $admin->id, 'vH4HueE9');
        (new WalletService)->transfer($admin, $agent_1, 5 * 100_000, TransactionName::CreditTransfer);

        $player_1 = $this->createUser(UserType::Player, 'Player 1', 'P111111', '09111111111', $agent_1->id);
        (new WalletService)->transfer($agent_1, $player_1, 30000, TransactionName::CreditTransfer);

        $systemWallet = $this->createUser(UserType::SystemWallet, 'SystemWallet', 'systemWallet', '09222222222');
        (new WalletService)->deposit($systemWallet, 50 * 100_0000, TransactionName::CapitalDeposit);

    }

    private function createUser(UserType $type, $name, $user_name, $phone, $parent_id = null, $referral_code = null)
    {
        return User::create([
            'name' => $name,
            'user_name' => $user_name,
            'phone' => $phone,
            'password' => Hash::make('delightmyanmar'),
            'agent_id' => $parent_id,
            'status' => 1,
            'referral_code' => $referral_code,
            'is_changed_password' => 1,
            'type' => $type->value,
        ]);
    }
}
