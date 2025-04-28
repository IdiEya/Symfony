<?php

namespace App\Entity;

enum Statut: string
{
    case A_VENIR = 'A_VENIR';
    case TERMINE = 'TERMINE';
    case COMPLET = 'COMPLET';
}