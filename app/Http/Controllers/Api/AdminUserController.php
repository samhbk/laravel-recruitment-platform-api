<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AdminUserController extends Controller
{
    private const PER_PAGE = 25;

    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::query()
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE);

        return UserResource::collection($users);
    }
}
