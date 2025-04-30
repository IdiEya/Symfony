<?php
namespace App\Enum;

enum ModePaiement: string
{
    case CARTE_BANCAIRE = 'CARTE_BANCAIRE';
    case ESPECES = 'ESPECES';
    case CHEQUE = 'CHEQUE';
    case PAYPAL = 'PAYPAL';

    public function label(): string
    {
        return match($this) {
            self::CARTE_BANCAIRE => 'Carte bancaire',
            self::ESPECES => 'Espèces',
            self::CHEQUE => 'Chèque',
            self::PAYPAL => 'PayPal', 
        };
    }
}