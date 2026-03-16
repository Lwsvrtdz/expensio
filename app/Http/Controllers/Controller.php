<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Inputs\PageSize;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    public function actor(): User
    {
        $user = auth()->user();

        if (! $user) {
            throw CustomException::noUserFound();
        }

        return $user;
    }

    public function size(): int
    {
        return resolve(PageSize::class)->size();
    }
}
