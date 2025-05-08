<?php
namespace App\Enum;

enum TypeAbonnement: string
{
    case MENSUEL = 'mensuel';
    case TRIMESTRIEL = 'trimestriel';
    case SEMESTRIEL = 'semestriel';
    case ANNUEL = 'annuel';

    public function label(): string
    {
        return match($this) {
            self::MENSUEL => 'Mensuel',
            self::TRIMESTRIEL => 'Trimestriel',
            self::SEMESTRIEL => 'Semestriel',
            self::ANNUEL => 'Annuel',
        };
    }
}
