<?php

namespace App\Http\Controllers;

use App\Models\fcmToken;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function registerToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'user_id' => 'nullable|integer',
        ]);

        // Guardar el token en la base de datos
        fcmToken::updateOrCreate(
            ['token' => $request->token],
            ['user_id' => $request->user_id]
        );

        return response()->json(['message' => 'Token registrado correctamente.']);
    }
    public function updateToken(Request $request)
    {
        $expiredToken = $request->input('expired_token');
        $refreshedToken = $request->input('refreshed_token');

        // Buscar el token expirado en la base de datos
        $fcmToken = FcmToken::where('token', $expiredToken)->first();

        if ($fcmToken) {
            $fcmToken->token = $refreshedToken;
            $fcmToken->save();
            return response()->json(['message' => 'Token actualizado exitosamente'], 200);
        }

        return response()->json(['error' => 'Token no encontrado'], 404);
    }


    public function sendNotification($token, $title, $body)
    {
        $client = new Client();
        $response = $client->post('https://fcm.googleapis.com/v1/projects/botzero-test/messages:send', [
            'headers' => [
                'Authorization' => 'Bearer ya29.a0AcM612z0_vd_slfXIAeBusrOj_ltMX-Rw7pwqmDEMUIL_gT4jlHcb7-VepC8iZ2Wm3ma0qv0zWIPyzXB9Zy1ysvq_O5tcWJQpIEKET6xJPw4qjzAfkVktucBxCofhvIqe3BJTu2qY0gCXWL7dxS21kX889ig76_oEIqH1cn7aCgYKAbkSARMSFQHGX2Mik375ypZ87ny5vA3w7dBnsQ0175',
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                        "icon" => "ic_notification"
                    ]
                ]
            ]
        ]);

        return $response->getBody();
    }



    public function notifyUsers()
    {
        $tokens = FcmToken::all();

        foreach ($tokens as $token) {
            $this->sendNotification($token->token, 'Título', 'Cuerpo de la notificación', 'android');
        }

        return response()->json(['message' => 'Notificaciones enviadas.']);
    }
    public function takeUser($user){

        
    }
}
