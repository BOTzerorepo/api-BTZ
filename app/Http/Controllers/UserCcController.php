<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserCcController extends Controller
{
    public function updateCc(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->cc_emails = $request->input('cc_emails');
        $user->save();

        return response()->json([
            'message' => 'CC emails updated successfully',
            'cc_emails' => $user->cc_emails
        ]);
    }
    
    public function index()
    {
        try {
            $users = User::leftJoin('customers', 'users.cliente_id', '=', 'customers.id')
                ->leftJoin('transports', function ($join) {
                    $join->on('users.transport_id', '=', 'transports.id')
                        ->whereNull('users.cliente_id');
                })
                ->select('users.*', 'customers.registered_name as razonSocial', 'transports.razon_social as razonSocial')
                ->get();
            return response()->json([
                'data' => $users,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
