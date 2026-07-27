<?php

namespace Tests\Feature\Admin;

use App\Models\User;

trait CreatesAdminUsers
{
    /**
     * Roles administrator/sekretariat are 2FA-confirmed by default so that
     * tests unrelated to 2FA aren't blocked by the enforcement middleware.
     * Pass twoFactorConfirmed: false to get a fresh, unconfirmed account.
     */
    private function makeAdminUser(string $role, array $overrides = [], bool $twoFactorConfirmed = true): User
    {
        static $counter = 0;
        $counter++;

        $user = User::create(array_merge([
            'nip' => '19800101201012' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'name' => 'Test ' . ucfirst($role),
            'email' => "{$role}{$counter}@example.com",
            'role' => $role,
            'position' => 'Sekretariat',
            'ref' => '-',
            'password' => bcrypt('password'),
        ], $overrides));

        if ($twoFactorConfirmed && in_array($role, ['administrator', 'sekretariat'], true)) {
            $user->forceFill([
                'two_factor_secret' => encrypt('TESTSECRETKEYFORTESTINGONLY'),
                'two_factor_confirmed_at' => now(),
            ])->save();
        }

        return $user;
    }
}
