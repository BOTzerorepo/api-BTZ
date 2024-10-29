<?php

namespace App\Http\Controllers;

use App\Models\fcmToken;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

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

        $file = new Filesystem();
        $archivoPath = storage_path('app/botzero-test-firebase-adminsdk-l750d-5108c493e1.json');

        if ($file->exists($archivoPath)) {

            
            $archivo = $file->get($archivoPath);
            $config = json_decode($archivo, true);

           
            if ($config !== null) {

                // Inicializa el cliente de Google para usar la cuenta de servicio
                $googleClient = new GoogleClient();
                $googleClient->setAuthConfig($config);
                $googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');
                

            } else {


                /* $archivoPath = storage_path('app/botzero-test-firebase-adminsdk-l750d-5108c493e1.json');
                $archivo = $file->get($archivoPath);
                $config = json_decode($archivo, true); */

                $googleClient = new GoogleClient();
                $googleClient->setAuthConfig($archivo);
                $googleClient->addScope('https://www.googleapis.com/auth/firebase.messaging');

                // Manejar error de JSON inválido
            }
        } else {
            // Manejar error de archivo no encontrado
            return "Error: Archivo no encontrado";
        }

/* 
        $file = new Filesystem();
        $archivo = $file->get(storage_path('/app/botzero-test-firebase-adminsdk-l750d-5108c493e1.json'));
      
        // Inicializa el cliente de Google para usar la cuenta de servicio
        $googleClient = new GoogleClient();
        $googleClient->setAuthConfig(json_decode($archivo, true));
        //$googleClient->setAuthConfig($archivo);
        
 */
        // Obtiene el token de acceso
        $accessToken = $googleClient->fetchAccessTokenWithAssertion()['access_token'];

        // Configura el cliente HTTP con Guzzle y el token de acceso en los headers
        $client = new Client();

        // Realiza la solicitud de envío de notificación
        $response = $client->post('https://fcm.googleapis.com/v1/projects/botzero-test/messages:send', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
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
