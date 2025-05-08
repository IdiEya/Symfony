<?php

require __DIR__ . '/vendor/autoload.php';

use App\Service\SmsNotifier;

$sid = '';
$token = '';
$from = ''; // ex : '+1415XXXXXXX'

$smsNotifier = new SmsNotifier($sid, $token, $from);

try {
    $smsNotifier->sendSms('+21623374707', 'Bonjour depuis Twilio et PHP !');
    echo "SMS envoyé avec succès.\n";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
