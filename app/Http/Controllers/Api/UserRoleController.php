<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\LogsUserAction;
use Illuminate\Support\Facades\Auth;

class UserRoleController extends Controller
{
    use LogsUserAction;

    public function makeAdmin(User $user)
    {
        if ($user->role !== 'admin') {
            $oldRole = $user->role;
            $user->role = 'admin';
            $user->save();

            $this->logUserAction("Promosso utente a admin", [
                'user_id' => $user->id,
                'email' => $user->email,
                'previous_role' => $oldRole,
                'new_role' => 'admin',
                'changed_by' => Auth::id()
            ]);
        }

        return response()->json([
            'message' => "L'utente {$user->email} è ora un admin."
        ]);
    }

    public function removeAdmin(User $user)
    {
        if ($user->role === 'admin') {
            $oldRole = $user->role;
            $user->role = 'operator';
            $user->save();

            $this->logUserAction("Revocato ruolo admin a utente", [
                'user_id' => $user->id,
                'email' => $user->email,
                'previous_role' => $oldRole,
                'new_role' => 'operator',
                'changed_by' => Auth::id()
            ]);
        }

        return response()->json([
            'message' => "L'utente {$user->email} non è più admin."
        ]);
    }
}
