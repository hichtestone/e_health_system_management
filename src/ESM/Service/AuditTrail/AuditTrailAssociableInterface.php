<?php

namespace App\ESM\Service\AuditTrail;

interface AuditTrailAssociableInterface
{
    public function getAuditTrailString(): string;
}
