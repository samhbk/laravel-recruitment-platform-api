<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Contracts\Auth\Guard;

final readonly class LoginUserAction
{
    public function __construct(
        private Guard $guard,
    ) {}

    /**
     * @return array{access_token: string, token_type: string, expires_in: int}|null
     */
    public function execute(array $credentials): ?array
    {
        if (! $token = $this->guard->attempt($credentials)) {
            return null;
        }

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard->factory()->getTTL() * 60,
        ];
    }
}
