<?php

namespace App\ESM\Service\Utils;

use App\ESM\Entity\PatientData;
use App\ESM\Entity\Project;
use App\ESM\Entity\VisitPatient;
use App\ESM\Entity\VisitPatientStatus;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MonitoringDateAndStatus
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     */
    public function monitoringDateAndStatus(Project $project, array $param, string $variable, string $key, bool $update): void
    {
        $id = $this->entityManager->getRepository(PatientData::class)->getPatientVariableByPatientCenter($project->getId(), $param['patient'], $param['center'], $key);
        if ($id && $update) {
            $patientData = $this->entityManager->getRepository(PatientData::class)->find($id);
            $patientData->setVariableValue('' === $variable ? '' : $variable);
            $this->entityManager->persist($patientData);
        }
        // UPDATE STATUT VISIT
        $visitsPatient = $this->entityManager->getRepository(VisitPatient::class)->getVisitPatientByPatient($param['idPatient'], $param['center']);

        foreach ($visitsPatient as $visitPatient) {
            if ($key === $visitPatient['label']) {
                // DATE REFERENCE
                $dateRef = $this->entityManager->getRepository(VisitPatient::class)->getVariableValueVisit($visitPatient['patient'], $visitPatient['visit'], $project->getId());

                $visitPatientEntity = $this->entityManager->getRepository(VisitPatient::class)->find($visitPatient['id']);

                // DATE REEL
                // $occuredAt = $update ? $variable : '';
                // DATE APPROXIMATIVE + (+/- DELAI)
                $delayApprox = $visitPatientEntity->getVisit()->getDelay() + $visitPatientEntity->getVisit()->getDelayApprox();
                $badge = $monitoredAt = $ref = '';
                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);

                if ('Date de signature du consentement' === isset($dateRef['ref']) ? $dateRef['ref'] : '') {
                    $ref = isset($param['variables']['Date de signature du consentement']['1']) ? $param['variables']['Date de signature du consentement']['1'] : '';
                }

                if ('Date d\'inclusion' === isset($dateRef['ref']) ? $dateRef['ref'] : '') {
                    $ref = isset($param['variables']['Date d\'inclusion']['1']) ? $param['variables']['Date d\'inclusion']['1'] : '';
                }

                if (!$dateRef) {
                    // DATE REEL
                    $occuredAt = $update ? $variable : '';
                    if ('' !== $occuredAt) {
                        $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                        $badge = '';
                    } else {
                        $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                        $badge = '';
                    }
                } else {
                    // la date de référence a une date réelle
                    $ref = isset($dateRef['val']) ? $dateRef['val'] : '';
                    if ('' !== $ref) {
                        $monitoredAt = date('Y-m-d', strtotime($ref.' + '.$delayApprox.' days '));
                        $occuredAt = $update ? $variable : '';
                        if ('' !== $occuredAt) {
                            // date réelle (de la visite) saisie
                            if ((new \DateTime($occuredAt))->format('Y-m-d') > (new \DateTime($monitoredAt))->format('Y-m-d')) {
                                // Date réelle > date prévisionnelle --> badge "retard" + statut "à monitorer"
                                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                                $badge = 'retard';
                            }

                            if ((new \DateTime($occuredAt))->format('Y-m-d') <= (new \DateTime($monitoredAt))->format('Y-m-d')) {
                                //Date réelle <= date prévisionnelle --> badge vide + statut "à monitorer"
                                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                                $badge = '';
                            }
                        } else {
                            // date réelle (de la visite) vide
                            if ((new \DateTime())->format('Y-m-d') > (new \DateTime($monitoredAt))->format('Y-m-d')) {
                                //Date du jour > date prévisionnelle --> statut "Non-faite" + sans badge
                                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(3);
                                $badge = '';
                            }

                            if ((new \DateTime())->format('Y-m-d') <= (new \DateTime($monitoredAt))->format('Y-m-d')) {
                                //Date du jour <= date prévisionelle --> statut vide + sans badge
                                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                                $badge = '';
                            }
                        }
                    } else {
                        // la date de référence n'a pas de date réelle
                        $occuredAt = $update ? $variable : '';
                        if ('' !== $occuredAt) {
                            // date réelle saisie --> statut "à monitorer" + badge "ref manquante"
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                            $badge = 'Référence manquante';
                        } else {
                            // date réelle vide --> statut vide + badge "ref manquante"
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                            $badge = 'Référence manquante';
                        }
                    }
                }

                if ('2' === $visitPatient['status']) {
                    $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(2);
                }

                $visitPatientEntity->setMonitoredAt('' === $monitoredAt ? null : new \DateTime($monitoredAt));
                $visitPatientEntity->setOccuredAt('' === $occuredAt ? null : new \DateTime($occuredAt));
                $visitPatientEntity->setStatus($status);
                $visitPatientEntity->setBadge($badge);
                $this->entityManager->persist($visitPatientEntity);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @throws Exception
     */
    public function monitoringDateAndStatusVisit(Project $project, array $param, string $variable, string $key): void
    {
        $id = $this->entityManager->getRepository(PatientData::class)->getPatientVariableByPatientCenter($project->getId(), $param['patient'], $param['center'], $key);

        if ($id) {
            $patientData = $this->entityManager->getRepository(PatientData::class)->find($id);
            $patientData->setVariableValue('' === $variable ? '' : $variable);
            $this->entityManager->persist($patientData);
        }
        // UPDATE STATUT VISIT
        $visitsPatient = $this->entityManager->getRepository(VisitPatient::class)->getVisitPatientByPatient($param['idPatient'], $param['center']);
        foreach ($visitsPatient as $visitPatient) {
            // DATE REFERENCE
            $dateRef = $this->entityManager->getRepository(VisitPatient::class)->getVariableValueVisit($visitPatient['patient'], $visitPatient['visit'], $project->getId());
            $visitPatientEntity = $this->entityManager->getRepository(VisitPatient::class)->find($visitPatient['id']);

            $monitoredAt = $visitPatient['monitoredAt'];

            // DATE APPROXIMATIVE + (+/- DELAI)
            $delayApprox = $visitPatientEntity->getVisit()->getDelay() + $visitPatientEntity->getVisit()->getDelayApprox();
            $badge = $ref = '';
            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);

            if (false === $dateRef) {
                // DATE REEL
                $variable = '' != $variable ? $variable : null;
                $occuredAt = ($key === $visitPatient['label']) ? $variable : $visitPatient['occuredAt'];

                if (null !== $occuredAt) {
                    $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                    $badge = '';
                } else {
                    $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                    $badge = '';
                }
            } else {
                // la date de référence a une date réelle
                if ('Date de signature du consentement' === $dateRef['ref'] || 'Date d\'inclusion' === $dateRef['ref']) {
                    $ref = $dateRef['val'];
                } else {
                    $ref = ($key === $dateRef['ref']) ? $variable : $dateRef['val'];
                }

                if ('' !== $ref) {
                    $monitoredAt = date('Y-m-d', strtotime($ref.' + '.$delayApprox.' days '));

                        $variable = '' != $variable ? $variable : null;
                        $occuredAt = ($key === $visitPatient['label']) ? $variable : $visitPatient['occuredAt'];

                    if (null !== $occuredAt) {
                        // date réelle (de la visite) saisie
                        if ((new \DateTime($occuredAt))->format('Y-m-d') > (new \DateTime($monitoredAt))->format('Y-m-d')) {
                            // Date réelle > date prévisionnelle --> badge "retard" + statut "à monitorer"
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                            $badge = 'retard';
                        }

                        if ((new \DateTime($occuredAt))->format('Y-m-d') <= (new \DateTime($monitoredAt))->format('Y-m-d')) {
                            //Date réelle <= date prévisionnelle --> badge vide + statut "à monitorer"
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                            $badge = '';
                        }
                    } else {

                        // date réelle (de la visite) vide
                        if ((new \DateTime())->format('Y-m-d') > (new \DateTime($monitoredAt))->format('Y-m-d')) {
                            //Date du jour > date prévisionnelle --> statut "Non-faite" + sans badge
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(3);
                            $badge = '';
                        }

                        if ((new \DateTime())->format('Y-m-d') <= (new \DateTime($monitoredAt))->format('Y-m-d')) {
                            //Date du jour <= date prévisionelle --> statut vide + sans badge
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                            $badge = '';
                        }
                    }
                } else {
                    // la date de référence n'a pas de date réelle

                    $variable = '' != $variable ? $variable : null;
                    $occuredAt = ($key === $visitPatient['label']) ? $variable : $visitPatient['occuredAt'];

                    if (null !== $occuredAt) {
                        // date réelle saisie --> statut "à monitorer" + badge "ref manquante"
                        $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                        $badge = 'Référence manquante';
                    } else {
                        // date réelle vide --> statut vide + badge "ref manquante"
                        $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                        $badge = 'Référence manquante';
                    }
                }
            }

            if ('2' === $visitPatient['status']) {
                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(2);
            }

            $visitPatientEntity->setMonitoredAt(null === $monitoredAt ? null : new \DateTime($monitoredAt));
            $visitPatientEntity->setOccuredAt(null === $occuredAt ? null : new \DateTime($occuredAt));
            $visitPatientEntity->setStatus($status);
            $visitPatientEntity->setBadge($badge);
            $this->entityManager->persist($visitPatientEntity);
        }

        $this->entityManager->flush();
    }


    /**
     * @throws Exception
     */
    public function monitoringDateAndStatusVisitUpdate(Project $project, array $param, string $variable, string $key): void
    {
        $id = $this->entityManager->getRepository(PatientData::class)->getPatientVariableByPatientCenter($project->getId(), $param['patient'], $param['center'], $key);

        if ($id) {
            $patientData = $this->entityManager->getRepository(PatientData::class)->find($id);
            $patientData->setVariableValue('' === $variable ? '' : $variable);
            $this->entityManager->persist($patientData);
        }
        // UPDATE STATUT VISIT
        $visitsPatient = $this->entityManager->getRepository(VisitPatient::class)->getVisitPatientByPatient($param['idPatient'], $param['center']);

        foreach ($visitsPatient as $visitPatient) {
            // DATE REFERENCE
            $dateRef = $this->entityManager->getRepository(VisitPatient::class)->getVariableValueVisit($visitPatient['patient'], $visitPatient['visit'], $project->getId());

            $visitPatientEntity = $this->entityManager->getRepository(VisitPatient::class)->find($visitPatient['id']);

            $monitoredAt = $visitPatient['monitoredAt'];

            // DATE APPROXIMATIVE + (+/- DELAI)
            $delayApprox = $visitPatientEntity->getVisit()->getDelay() + $visitPatientEntity->getVisit()->getDelayApprox();
            $badge = $ref = '';
            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);

            if ('Date de signature du consentement' === isset($dateRef['ref']) ? $dateRef['ref'] : '') {
                $ref = isset($param['variables']['Date de signature du consentement']['1']) ? $param['variables']['Date de signature du consentement']['1'] : '';
            }

            if ('Date d\'inclusion' === isset($dateRef['ref']) ? $dateRef['ref'] : '') {
                $ref = isset($param['variables']['Date d\'inclusion']['1']) ? $param['variables']['Date d\'inclusion']['1'] : '';
            }

            if (false === $dateRef) {
                // DATE REEL
                $occuredAt = ($key === $visitPatient['label']) ? $variable : $visitPatient['occuredAt'];
                if (null !== $occuredAt) {
                    $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                    $badge = '';
                } else {
                    $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                    $badge = '';
                }
            } else {
                // la date de référence a une date réelle
                $ref = ($key === $dateRef['ref']) ? $variable : $dateRef['val'];

                if ('' !== $ref) {
                    $monitoredAt = date('Y-m-d', strtotime($ref.' + '.$delayApprox.' days '));
                    $occuredAt = ($key === $visitPatient['label']) ? $variable : $visitPatient['occuredAt'];

                    if (null !== $occuredAt) {
                        // date réelle (de la visite) saisie
                        if ((new \DateTime($occuredAt))->format('Y-m-d') > (new \DateTime($monitoredAt))->format('Y-m-d')) {
                            // Date réelle > date prévisionnelle --> badge "retard" + statut "à monitorer"
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                            $badge = 'retard';
                        }

                        if ((new \DateTime($occuredAt))->format('Y-m-d') <= (new \DateTime($monitoredAt))->format('Y-m-d')) {
                            //Date réelle <= date prévisionnelle --> badge vide + statut "à monitorer"
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                            $badge = '';
                        }
                    } else {
                        // date réelle (de la visite) vide
                        if ((new \DateTime())->format('Y-m-d') > (new \DateTime($monitoredAt))->format('Y-m-d')) {
                            //Date du jour > date prévisionnelle --> statut "Non-faite" + sans badge
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(3);
                            $badge = '';
                        }

                        if ((new \DateTime())->format('Y-m-d') <= (new \DateTime($monitoredAt))->format('Y-m-d')) {
                            //Date du jour <= date prévisionelle --> statut vide + sans badge
                            $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                            $badge = '';
                        }
                    }
                } else {
                    // la date de référence n'a pas de date réelle
                    $occuredAt = ($key === $visitPatient['label']) ? $variable : $visitPatient['occuredAt'];
                    if (null !== $occuredAt) {
                        // date réelle saisie --> statut "à monitorer" + badge "ref manquante"
                        $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(1);
                        $badge = 'Référence manquante';
                    } else {
                        // date réelle vide --> statut vide + badge "ref manquante"
                        $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(4);
                        $badge = 'Référence manquante';
                    }
                }
            }

            if ('2' === $visitPatient['status']) {
                $status = $this->entityManager->getRepository(VisitPatientStatus::class)->find(2);
            }

            $visitPatientEntity->setMonitoredAt(null === $monitoredAt ? null : new \DateTime($monitoredAt));
            $visitPatientEntity->setOccuredAt(null === $occuredAt ? null : new \DateTime($occuredAt));
            $visitPatientEntity->setStatus($status);
            $visitPatientEntity->setBadge($badge);
            $this->entityManager->persist($visitPatientEntity);
        }

        $this->entityManager->flush();
    }
}
