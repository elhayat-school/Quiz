<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function authenticate(string $role = 'student'): User
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);

        return $user;
    }
}
