<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserRoleController extends Controller
{
    public function makeAdmin(User $user)
    {
        if ($user->role !== 'admin') {
            $user->role = 'admin';
            $user->save();
        }

        return response()->json([
            'message' => "L'utente {$user->email} è ora un admin."
        ]);
    }

    public function removeAdmin(User $user)
    {
        if ($user->role === 'admin') {
            $user->role = 'operator';
            $user->save();
        }

        return response()->json([
            'message' => "L'utente {$user->email} non è più admin."
        ]);
    }
}
