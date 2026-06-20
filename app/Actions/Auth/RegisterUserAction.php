<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Domain\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Hash;

final readonly class RegisterUserAction
{
    public function __construct(
        private Guard $guard,
    ) {}

    /**
     * @return array{user: User, access_token: string, token_type: string, expires_in: int}
     */
    public function execute(string $name, string $email, string $password, UserRole $role = UserRole::JobSeeker): array
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);

        $token = $this->guard->login($user);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard->factory()->getTTL() * 60,
        ];
    }
}
