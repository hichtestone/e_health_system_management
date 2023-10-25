<?php

namespace App\ESM\Service\AuditTrail;

class AuditTrailService
{
    /**
     * @var string|null
     */
    private $reason;

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }
}
