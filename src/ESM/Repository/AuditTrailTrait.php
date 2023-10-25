<?php

namespace App\ESM\Repository;

/**
 * AuditTrailer trait.
 *
 * Should be used inside entity where you need to track modifications log
 */
trait AuditTrailTrait
{
    public function auditTrailListGen()
    {
        $qb = $this->createQueryBuilder('at')
            ->innerJoin('at.entity', 'e')
            ->leftJoin('at.user', 'u')
            ;

        return $qb;
    }
}
