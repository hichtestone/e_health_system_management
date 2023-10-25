<?php

namespace App\ETMF\Repository;

use App\ETMF\Entity\DocumentVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentVersion[]    findAll()
 * @method DocumentVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentVersionRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, DocumentVersion::class);
	}

	/**
	 * @return QueryBuilder
	 */
	public function indexListGen(): QueryBuilder
	{
		return $this->createQueryBuilder('document_version');
	}

    /**
     * @param $sponsors
     * @param $projects
     * @param $zones
     * @param $sections
     * @param $artefacts
     * @param $countries
     * @param $centers
     * @param $tags
     * @param $status
     * @return array
     */
	public function searchEngine($sponsors, $projects, $zones, $sections, $artefacts, $countries, $centers, $tags, $status): array
	{
		$qb = $this->createQueryBuilder('document_version')
			->leftJoin('document_version.document', 'document')
			->leftJoin('document.project', 'project')
			->leftJoin('document.sponsor', 'sponsor')
			->leftJoin('document.zone', 'zone')
			->leftJoin('document.section', 'section')
			->leftJoin('document.artefact', 'artefact')
			->leftJoin('document.countries', 'countries')
			->leftJoin('document.centers', 'centers')
			->leftJoin('document.tags', 'tags');


		if (!empty($sponsors)) {
			$qb->andWhere('sponsor.id IN (:sponsorsIDs)');
			$qb->setParameter('sponsorsIDs', $sponsors);
		}

        if (!empty($projects)) {
            $qb->andWhere('project.id IN (:projectIDs)');
            $qb->setParameter('projectIDs', $projects);
        }

        if (!empty($zones)) {
            $qb->andWhere('zone.id IN (:zoneIDs)');
            $qb->setParameter('zoneIDs', $zones);
        }

        if (!empty($sections)) {
            $qb->andWhere('section.id IN (:sectionIDs)');
            $qb->setParameter('sectionIDs', $sections);
        }

        if (!empty($artefacts)) {
            $qb->andWhere('artefact.id IN (:artefactIDs)');
            $qb->setParameter('artefactIDs', $artefacts);
        }

        if (!empty($countries)) {
            $qb->andWhere('countries.id IN (:countriesIDs)');
            $qb->setParameter('countriesIDs', $countries);
        }

        if (!empty($centers)) {
            $qb->andWhere('centers.id IN (:centersIDs)');
            $qb->setParameter('centersIDs', $centers);
        }

        if (!empty($tags)) {
            $qb->andWhere('tags.id IN (:tagsIDs)');
            $qb->setParameter('tagsIDs', $tags);
        }

        if (!empty($status)) {
            $qb->andWhere('document_version.status IN (:statusIDs)');
            $qb->setParameter('statusIDs', $status);
        }

		return $qb->getQuery()->getResult();
	}
}
