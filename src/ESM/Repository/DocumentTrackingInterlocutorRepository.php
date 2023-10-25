<?php

namespace App\ESM\Repository;

use App\ESM\Entity\DocumentTrackingInterlocutor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentTrackingInterlocutor|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentTrackingInterlocutor|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentTrackingInterlocutor[]    findAll()
 * @method DocumentTrackingInterlocutor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentTrackingInterlocutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentTrackingInterlocutor::class);
    }
}
