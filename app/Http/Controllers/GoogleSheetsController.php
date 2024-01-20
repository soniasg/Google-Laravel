<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleSheetsController extends Controller
{
    public function redirectToGoogle()
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = $this->getClient();
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
        $client->setAccessToken($token);

        // Store the token in your database or session for future use

        // Now you can make Google Sheets API requests
        $sheetsService = new Google_Service_Sheets($client);
        $spreadsheetId = '1Yk_zdmsYlbYwsMDWczji6BdeMXMSC65DNM6_KmoD8w4';
        $range = 'Sheet1!A1:C6';

        $response = $sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        return view('view.blade.php', ['data' => $values]);
    }

    private function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName("Web client 1");
        $client->setClientId("490581939888-fbi3ntifraueldj5ohvh37nvnoc3m603.apps.googleusercontent.com");
        $client->setClientSecret("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");
        $client->setRedirectUri("http://localhost:8000/auth/google/callback");
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
        $client->setAccessType('offline');

        return $client;
    }
}
