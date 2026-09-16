<?php

namespace App\Enum;

final class StatutCommande
{
    public const A_PAYER = 'A_PAYER';
    public const PAYEE = 'PAYEE';
    public const EN_PREPARATION = 'EN_PREPARATION';
    public const DISTRIBUEE = 'DISTRIBUEE';
    public const ANNULEE = 'ANNULEE';
    public const ECHEC = 'ECHEC';
    public const REFUSE = 'REFUSE';
}