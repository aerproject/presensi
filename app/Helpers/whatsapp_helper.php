<?php

use Config\WhatsApp;

function kirimPesanWhatsApp($nomorTujuan, $pesan)
{
    $client = \Config\Services::curlrequest();

    $response = $client->post(WhatsApp::$url, [
        'headers' => [
            'Authorization' => WhatsApp::$token,
        ],
        'form_params' => [
            'phone' => $nomorTujuan,
            'message' => $pesan
        ]
    ]);

    return $response->getStatusCode() === 200;
}
