<?php

namespace App\ESM\Service\AuditTrail;

interface AuditrailableInterface
{
    public function getFieldsToBeIgnored(): array;
}
