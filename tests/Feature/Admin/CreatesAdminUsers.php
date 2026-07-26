<?php

namespace Tests\Feature\Admin;

use App\Models\User;

trait CreatesAdminUsers
{
    private function makeAdminUser(string $role, array $overrides = []): User
    {
        static $counter = 0;
        $counter++;

        return User::create(array_merge([
            'nip' => '19800101201012' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'name' => 'Test ' . ucfirst($role),
            'email' => "{$role}{$counter}@example.com",
            'role' => $role,
            'position' => 'Sekretariat',
            'ref' => '-',
            'password' => bcrypt('password'),
        ], $overrides));
    }
}
