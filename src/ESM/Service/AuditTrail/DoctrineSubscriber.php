<?php

namespace App\ESM\Service\AuditTrail;

use App\ESM\Entity\Deviation;
use App\ESM\Entity\DeviationAction;
use App\ESM\Entity\DeviationCorrection;
use App\ESM\Entity\DeviationReview;
use App\ESM\Entity\DeviationSample;
use App\ESM\Entity\DeviationSampleAction;
use App\ESM\Entity\DeviationSampleCorrection;
use App\ESM\Entity\DeviationSystem;
use App\ESM\Entity\DeviationSystemAction;
use App\ESM\Entity\DeviationSystemCorrection;
use App\ESM\Entity\DeviationSystemReview;
use App\ESM\Entity\DocumentTracking;
use App\ESM\Entity\Funding;
use App\ESM\Entity\ReportVisit;
use App\ESM\Entity\Service;
use App\ESM\Entity\User;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\UnitOfWork;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DoctrineSubscriber
 * @package App\ESM\Service\AuditTrail
 */
class DoctrineSubscriber implements EventSubscriber
{
	public CONST ACTION_INSERT = 'insert';
	public CONST ACTION_UPDATE = 'update';
	public CONST ACTION_DELETE = 'delete';

	public CONST ACTIONS = [
		self::ACTION_INSERT,
		self::ACTION_UPDATE,
		self::ACTION_DELETE
	];

    /**
     * @var UserInterface
     */
    private $activeUser;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuditTrailService
     */
    private $auditTrailService;

    /**
     * DoctrineSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param AuditTrailService $auditTrailService
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuditTrailService $auditTrailService)
    {
        $this->tokenStorage = $tokenStorage;
        $this->auditTrailService = $auditTrailService;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

	/**
	 * @param OnFlushEventArgs $args
	 * @throws AuditTrailException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function onFlush(OnFlushEventArgs $args): void
	{
		$toBePersisted = [];

		if (null !== $this->tokenStorage->getToken()) {
			$this->activeUser = $this->tokenStorage->getToken()->getUser();
		}

		$em = $args->getEntityManager();
		$uow = $em->getUnitOfWork();

		// inserts
		foreach ($uow->getScheduledEntityInsertions() as $entity) {

			if (!$this->mustBeIgnored(self::ACTION_INSERT, $entity)) {

				$auditTrailEntity = $this->auditTrailInsert($uow, $entity);

				if (null !== $auditTrailEntity) {
					$toBePersisted[spl_object_id($entity)] = $auditTrailEntity;
				}
			}
		}

		// updates
		foreach ($uow->getScheduledEntityUpdates() as $entity) {

			if (!$this->mustBeIgnored(self::ACTION_UPDATE, $entity)) {

				$auditTrailEntity = $this->auditTrailUpdate($uow, $entity);

				if (null !== $auditTrailEntity) {
					$toBePersisted[spl_object_id($entity)] = $auditTrailEntity;
				}
			}
		}

		// champs OneToMany ou ManyToMany
		foreach ($uow->getScheduledCollectionUpdates() as $collection) {

			$entity = $collection->getOwner();
			$mapping = $collection->getMapping();

			if (!$this->mustBeIgnored(self::ACTION_UPDATE, $entity)) {

				$auditTrailEntity = $this->getAuditTrailEntity($entity);

				if (null !== $auditTrailEntity) {

					if (!in_array($mapping['fieldName'], $entity->getFieldsToBeIgnored(), true)) {

						$before = $collection->getSnapShot();

						if (array_key_exists(spl_object_id($entity), $toBePersisted)) {

							$auditTrailEntity = $toBePersisted[spl_object_id($entity)];
							$details = $auditTrailEntity->getDetails();
							$details[$mapping['fieldName']] = [];

							// added
							foreach ($collection as $assocEntity) {
								if (!in_array($assocEntity, $before, true)) {
									$details[$mapping['fieldName']][] = $this->getChangeString($assocEntity);
								}
							}

							if (!empty($details[$mapping['fieldName']])) {
								$auditTrailEntity->setDetails($details);
							}

						} else {

							$auditTrailEntity->setModifType(2);
							$details = [$mapping['fieldName'] => ['added' => [], 'removed' => []]];

							// added
							foreach ($collection as $assocEntity) {
								if (!in_array($assocEntity, $before, true)) {
									$details[$mapping['fieldName']]['added'][] = $this->getChangeString($assocEntity);
								}
							}

							// removed
							foreach ($before as $assocEntity) {
								if (!$collection->contains($assocEntity)) {
									$details[$mapping['fieldName']]['removed'][] = $this->getChangeString($assocEntity);
								}
							}

							// si modifs
							if (!empty($details[$mapping['fieldName']]['added']) || !empty($details[$mapping['fieldName']]['removed'])) {
								$auditTrailEntity->setDetails($details);
								$toBePersisted[spl_object_id($entity)] = $auditTrailEntity;
							}
						}
					}
				}
			}
		}

		if (count($toBePersisted) > 0) {

			foreach ($toBePersisted as $auditTrailEntity) {
				$em->persist($auditTrailEntity);
			}

			$em->getEventManager()->removeEventListener([Events::onFlush], $this);
			$em->flush();
			$em->getEventManager()->addEventListener([Events::onFlush], $this);
		}
	}

	/**
	 * @param mixed $change
	 * @param null $entity
	 * @param null $property
	 * @return string
	 * @throws AuditTrailException
	 * @throws Exception
	 */
    private function getChangeString($change, $entity = null, $property = null): string
    {
        if (is_object($change)) {

            if (method_exists($change, 'getAuditTrailString')) {
                return $change->getAuditTrailString().' ('.$change->getId().')';
            }

            if ($change instanceof DateTime) {
                return $change->format('Y-m-d H:i:s');
            }

            if (method_exists($change, '__toString')) {
                return (string) $change.' ('.$change->getId().')';
            }

            throw new AuditTrailException('Audit trail error: string conversion impossible for object '.get_class($change));

        } elseif (is_array($change)) {

        	if ($this->mustBeConverted($entity, $property)) {
				return $this->getConvertedValue($entity, $property, $change);
			} else {
				return implode(',', $change);
			}

        } elseif ($this->mustBeConverted($entity, $property)) {

        	return $this->getConvertedValue($entity, $property, $change);

		}

        return $change ?? '';
    }

    /**
     * @param $entity
     * @return AbstractAuditTrailEntity|null
     */
    private function getAuditTrailEntity($entity): ?AbstractAuditTrailEntity
    {
        $auditTrailEntityClass = str_replace('\\Entity\\', '\\Entity\\AuditTrail\\', get_class($entity)).'AuditTrail';

        if (class_exists($auditTrailEntityClass)) {

            $auditTrailEntity = new $auditTrailEntityClass();

            if (null !== $this->activeUser) {
                $auditTrailEntity->setUser($this->activeUser);
            }

            $auditTrailEntity->setDate(new DateTime());
            $auditTrailEntity->setReason('todo');
            $auditTrailEntity->setEntity($entity);
            $auditTrailEntity->setReason($this->auditTrailService->getReason());

            return $auditTrailEntity;
        }

        return null;
    }

    /**
     * @param UnitOfWork $uow
     * @param $entity
     * @return AbstractAuditTrailEntity|null
     * @throws AuditTrailException
     */
    private function auditTrailInsert(UnitOfWork $uow, $entity): ?AbstractAuditTrailEntity
    {
        if ($auditTrailEntity = $this->getAuditTrailEntity($entity)) {
            $auditTrailEntity->setModifType(1);

            // filtre changeSet
            $changeSet = $uow->getEntityChangeSet($entity);
            $details = [];
            foreach ($changeSet as $property => $change) {
                if (!in_array($property, $entity->getFieldsToBeIgnored(), true)) {
                    if (null !== $change[1]) {
                        $details[$property] = $this->getChangeString($change[1]);
                    }
                }
            }

            $auditTrailEntity->setDetails($details);

            return $auditTrailEntity;
        }

        return null;
    }

    /**
     * @param UnitOfWork $uow
     * @param $entity
     * @return AbstractAuditTrailEntity|null
     * @throws AuditTrailException
     */
    private function auditTrailUpdate(UnitOfWork $uow, $entity): ?AbstractAuditTrailEntity
    {
        if ($auditTrailEntity = $this->getAuditTrailEntity($entity)) {
            $auditTrailEntity->setModifType(2);

            // filtre changeSet
            $changeSet = $uow->getEntityChangeSet($entity);
            $details = [];

            foreach ($changeSet as $property => $change) {
                if (!in_array($property, $entity->getFieldsToBeIgnored(), true)) {
                    $before = $this->getChangeString($change[0], $entity, $property);
                    $after = $this->getChangeString($change[1], $entity, $property);
                    $details[$property] = [$before, $after];
                }
            }

            if (!empty($details)) {
                $auditTrailEntity->setDetails($details);

                return $auditTrailEntity;
            }
        }

        return null;
    }

	/**
	 * @param $action
	 * @param $entity
	 * @return bool
	 */
	private function mustBeIgnored($action, $entity): bool
	{
		if ($entity instanceof Deviation) {

			if ($action === self::ACTION_INSERT) {

				if ($entity->getStatus() === Deviation::STATUS_DRAFT) {
					return true;
				}

			} elseif ($action === self::ACTION_UPDATE) {

				if ($entity->getStatus() === Deviation::STATUS_DRAFT) {
					return true;
				}
			}
		}
		elseif ($entity instanceof DeviationSystem) {

			if ($action === self::ACTION_INSERT) {

				if ($entity->getStatus() === Deviation::STATUS_DRAFT) {
					return true;
				}

			} elseif ($action === self::ACTION_UPDATE) {

				if ($entity->getStatus() === Deviation::STATUS_DRAFT) {
					return true;
				}
			}
		}
		elseif ($entity instanceof DeviationSample) {

			if ($action === self::ACTION_INSERT) {

				if ($entity->getStatus() === Deviation::STATUS_DRAFT) {
					return true;
				}

			} elseif ($action === self::ACTION_UPDATE) {

				if ($entity->getStatus() === Deviation::STATUS_DRAFT) {
					return true;
				}
			}
		}
		elseif ($entity instanceof User) {

			// ne pas auditTrail le user "anon." (action de l'utilisateur en mode deconnecté comme reset password)
			if ($this->activeUser === "anon.") {
				return true;
			}
		}

		return false;
    }

	/**
	 * @param $entity
	 * @param $field
	 * @return bool
	 */
	private function mustBeConverted($entity, $field): bool
	{
		if ($entity && $field) {

			$fieldsMustBeConverted = [
				'Funding' 					=> ['publicFunding'],
				'Service' 					=> ['addressInherited'],
				'DocumentTracking' 			=> ['level', 'toBeSent', 'toBeReceived'],
				'Deviation' 				=> ['status', 'grade', 'causality', 'efficiencyMeasure'],
				'DeviationCorrection' 		=> ['efficiencyMeasure'],
				'DeviationReview' 			=> ['status', 'type'],
				'DeviationAction' 			=> ['status', 'typeAction', 'typeManager', 'isDone'],
				'ReportVisit' 				=> ['visitType', 'visitStatus', 'reportStatus', 'reportStatus', 'reportType'],
				'DeviationSample' 			=> ['status', 'grade', 'causality', 'efficiencyMeasure'],
				'DeviationSampleCorrection' => ['efficiencyMeasure'],
				'DeviationSampleAction' 	=> ['status', 'typeAction', 'typeManager', 'isDone'],
				'DeviationSystem' 			=> ['status', 'grade', 'causality', 'efficiencyMeasure'],
				'DeviationSystemReview' 	=> ['status', 'type'],
				'DeviationSystemAction' 	=> ['status', 'typeAction', 'typeManager', 'isDone'],
				'DeviationSystemCorrection' => ['efficiencyMeasure'],
			];

			$entityName = (new \ReflectionClass($entity))->getShortName();

			if (array_key_exists($entityName, $fieldsMustBeConverted)) {

				return in_array($field, $fieldsMustBeConverted[$entityName], true) === true;

			} else {

				return false;
			}

		} else {

			return false;
		}
	}

	/**
	 * @param $entity
	 * @param $field
	 * @param $value
	 * @return string
	 * @throws Exception
	 */
	private function getConvertedValue($entity, $field, $value): string
	{
		if ($entity instanceof Funding) {

			if ($field === 'publicFunding') {
				return $value === 1 ? 'Oui' : 'Non';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof Service) {

			if ($field === 'addressInherited') {
				return $value === 1 ? 'Oui' : 'Non';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DocumentTracking) {

			if ($field === 'level') {

				if ($value === 1) {
					return 'center';
				} elseif ($value === 2) {
					return 'interlocuteur';
				} else {
					throw new Exception('The value : ' . $value . 'is not translatable !');
				}

			} elseif ($field === 'toBeSent') {
				return $value ? 'Oui' : 'Non';
			} elseif ($field === 'toBeReceived') {
				return $value ? 'Oui' : 'Non';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . ' is not convertisable!');
			}

		} elseif ($entity instanceof ReportVisit) {
			if ($field === 'visitType') {
				return null !== $value ? ReportVisit::VISIT_TYPE[$value] : '';
			} elseif ($field === 'visitStatus') {
				return null !== $value ? ReportVisit::VISIT_STATUS[$value] : '';
			} elseif ($field === 'reportStatus') {
				return null !== $value ? ReportVisit::REPORT_STATUS[$value] : '';
			} elseif ($field === 'reportType') {
				return null !== $value ? ReportVisit::REPORT_TYPE[$value] : '';
			}
			else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . ' is not convertisable!');
			}

		} elseif ($entity instanceof Deviation) {

			if ($field === 'status') {
				return null !== $value ? Deviation::STATUS[$value] : '';
			} elseif ($field === 'grade') {
				return null !== $value ? Deviation::GRADES[$value] : '';
			} elseif ($field === 'causality') {
				$finalString = '';
				foreach ($value as $key => $val) {$finalString .= Deviation::CAUSALITY[$val] . ',';}
				return substr($finalString, 0, -1);
			} elseif ($field === 'efficiencyMeasure') {
				return null !== $value ? Deviation::EFFICIENCY_MEASURE[$value] : '';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationReview) {

			if ($field === 'status') {
				return null !== $value ? DeviationReview::STATUS[$value] : '';
			} elseif ($field === 'type') {
				return null !== $value ? DeviationReview::TYPE[$value] : '';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationAction) {

			if ($field === 'status') {
				return null !== $value ? DeviationAction::STATUS[$value] : '';
			} elseif ($field === 'typeAction') {
				return null !== $value ? DeviationAction::TYPE_ACTION[$value] : '';
			} elseif ($field === 'typeManager') {
				return null !== $value ? DeviationAction::TYPE_MANAGER[$value] : '';
			} elseif ($field === 'isDone') {
				return $value === 1 ? 'Oui' : 'Non';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationCorrection) {

			if ($field === 'efficiencyMeasure') {
				return Deviation::EFFICIENCY_MEASURE[$value];
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationSample) {

			if ($field === 'status') {
				return null !== $value ? Deviation::STATUS[$value] : '';
			} elseif ($field === 'grade') {
				return null !== $value ? Deviation::GRADES[$value] : '';
			} elseif ($field === 'causality') {
				$finalString = '';
				foreach ($value as $key => $val) {$finalString .= Deviation::CAUSALITY[$val] . ',';}
				return substr($finalString, 0, -1);
			} elseif ($field === 'efficiencyMeasure') {
				return null !== $value ? Deviation::EFFICIENCY_MEASURE[$value] : '';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationSampleAction) {

			if ($field === 'status') {
				return null !== $value ? DeviationAction::STATUS[$value] : '';
			} elseif ($field === 'typeAction') {
				return null !== $value ? DeviationAction::TYPE_ACTION[$value] : '';
			} elseif ($field === 'typeManager') {
				return null !== $value ? DeviationAction::TYPE_MANAGER[$value] : '';
			} elseif ($field === 'isDone') {
				return $value === 1 ? 'Oui' : 'Non';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationSampleCorrection) {

			if ($field === 'efficiencyMeasure') {
				return null !== $value ? Deviation::EFFICIENCY_MEASURE[$value] : '';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationSystem) {

			if ($field === 'status') {
				return null !== $value ? Deviation::STATUS[$value] : '';
			} elseif ($field === 'grade') {
				return null !== $value ? Deviation::GRADES[$value] : '';
			} elseif ($field === 'causality') {
				$finalString = '';
				foreach ($value as $key => $val) {$finalString .= Deviation::CAUSALITY[$val] . ',';}
				return substr($finalString, 0, -1);
			} elseif ($field === 'efficiencyMeasure') {
				return null !== $value ? Deviation::EFFICIENCY_MEASURE[$value] : '';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationSystemReview) {

			if ($field === 'status') {
				return null !== $value ? DeviationReview::STATUS[$value] : '';
			} elseif ($field === 'type') {
				return null !== $value ? DeviationReview::TYPE[$value] : '';

			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationSystemAction) {
			if ($field === 'status') {
				return null !== $value ? DeviationAction::STATUS[$value] : '';
			} elseif ($field === 'typeAction') {
				return null !== $value ? DeviationAction::TYPE_ACTION[$value] : '';
			} elseif ($field === 'typeManager') {
				return null !== $value ? DeviationAction::TYPE_MANAGER[$value] : '';
			} elseif ($field === 'isDone') {
				return $value === 1 ? 'Oui' : 'Non';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		} elseif ($entity instanceof DeviationSystemCorrection) {

			if ($field === 'efficiencyMeasure') {
				return null !== $value ? Deviation::EFFICIENCY_MEASURE[$value] : '';
			} else {
				throw new Exception('this field : ' . $field . ' of entity : ' . $entity . 'is not convertisable!');
			}

		}

		throw new Exception('nothing convert rule field exist !');
	}
}
