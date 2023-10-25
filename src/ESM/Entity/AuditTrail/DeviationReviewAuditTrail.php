<?php

namespace App\ESM\Entity\AuditTrail;

use App\ESM\Entity\DeviationReview;
use App\ESM\Repository\AuditTrail\DeviationReviewAuditTrailRepository;
use App\ESM\Service\AuditTrail\AbstractAuditTrailEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviationReviewAuditTrailRepository::class)
 */
class DeviationReviewAuditTrail extends AbstractAuditTrailEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="App\ESM\Entity\DeviationReview")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var DeviationReview
     */
    private $entity;

    public function getEntity(): DeviationReview
    {
        return $this->entity;
    }

    public function setEntity(DeviationReview $entity): void
    {
        $this->entity = $entity;
    }
}
