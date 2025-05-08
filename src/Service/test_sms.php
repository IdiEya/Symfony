<?php
require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

$sid = 'TON_SID_TWILIO';
$token = 'TON_TOKEN_TWILIO';
$from = '+1NUMERO_TWILIO'; // Ton numéro Twilio acheté
$to = '+21623374707'; // Numéro cible
$body = 'Test manuel SMS via Twilio';

$client = new Client($sid, $token);

try {
    $message = $client->messages->create($to, [
        'from' => $from,
        'body' => $body,
    ]);

    echo "Message SID: " . $message->sid;
} catch (\Exception $e) {
    echo "Erreur Twilio : " . $e->getMessage();
}
