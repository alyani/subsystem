<?php

namespace Alyani\Subsystem\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data() as $value) {
            User::query()
                ->updateOrCreate(['mobile' => $value['mobile']], $value);
        }
    }

    public function data(): array
    {
        return [
            [
                'name' => 'John Doe',
                'family' => 'Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('1234'),
                'country_code' => 98,
                'mobile' => '+989360000000',
                'company_name' => 'Example Co.',
                'gender' => 'male',
                'national_code' => '1234567890',
                'phone' => '0123456789',
                'birth_date' => 631152000, // example timestamp for 1990-01-01
                'avatarSID' => 'avatar_123',
                'balance' => 100000,
                'currency' => 'IRR',
                'status' => 'waitingForSetProfile', // Based on User::STATUS
                'referee_user_id' => 0,
                'referred_users_count' => 10,
                'referral_code' => 'REF123',
            ],
            [
                'name' => 'Jane Smith',
                'family' => 'Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('1234'),
                'country_code' => 98,
                'mobile' => '+989380000001',
                'company_name' => 'Tech Innovations',
                'gender' => 'female', // Based on User::GENDER
                'national_code' => '9876543210',
                'phone' => '0987654321',
                'birth_date' => 631152000, // example timestamp for 1990-01-01
                'avatarSID' => 'avatar_456',
                'balance' => 200000,
                'currency' => 'IRR',
                'status' => 'waitingForSetProfile', // Based on Enums\UserStatus;
                'referee_user_id' => 1,
                'referred_users_count' => 5,
                'referral_code' => 'REF456',
            ],

        ];
    }
}
