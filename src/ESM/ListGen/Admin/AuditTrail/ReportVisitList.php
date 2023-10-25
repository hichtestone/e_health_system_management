<?php

namespace App\ESM\ListGen\Admin\AuditTrail;

use App\ESM\Entity\ReportModel;
use App\ESM\Entity\ReportVisit;
use App\ESM\Service\ListGen\AbstractListGenType;
use App\ESM\Service\ListGen\AuditTrailTrait;
use App\ESM\Service\ListGen\ListGen;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReportVisitList extends AbstractListGenType
{
    use AuditTrailTrait;

	/**
	 * @var TranslatorInterface $translator
	 */
    protected $translator;

	/**
	 * @var EntityManagerInterface $entityManager
	 */
	protected $entityManager;

	/**
	 * @var UrlGeneratorInterface $router
	 */
	protected $router;

	/**
	 * @var Security $security
	 */
	protected $security;

    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, UrlGeneratorInterface $router, Security $security)
	{
		parent::__construct($translator, $entityManager, $router, $security);
	}

	/**
     * @return ListGen
     */
    public function getList(string $category)
    {
        $rep = $this->em->getRepository('App\\ESM\\Entity\\AuditTrail\\'.$this->getCamelCaseCategory($category.'_audit_trail'));
        $url = 'admin.audit_trail.generic.index.ajax';
        $urlArgs = ['category' => $category];
        $entityTrans = 'entity.'.$this->getCamelCaseCategory($category).'.label';
        $entityTransArgs = ['%count%' => 1];

        $list = $this->setAuditTrailConfigPart1($rep, $url, $urlArgs);
        $list->setExportFileName($category.'_audit_trail');



        $list->addColumn([
            'label' => 'Projet',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getProject();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'project.name',
        ]);

        $list->addColumn([
            'label' => 'Center',
            'formatter' => function ($row) {
                return $row['audit_trail']->getEntity()->getCenter() . ' - ' . $row['audit_trail']->getEntity()->getCenter()->getNumber();
            },
            'formatter_csv' => 'formatter',
            'sortField' => 'center.name',
        ]);

		$list->addColumn([
			'label' => 'Type de visite',
			'formatter' => function ($row) {
				return (null !== $row['audit_trail']->getEntity()->getVisitType()) ? $this->translator->trans(ReportVisit::VISIT_TYPE[$row['audit_trail']->getEntity()->getVisitType()]) . ' (' .$row['audit_trail']->getEntity()->getVisitType() . ')' : '';
			},
			'formatter_csv' => 'formatter',
			'sortField' => 'reportVisit.visitType',
		]);

		$list->addColumn([
			'label' => 'Statut de visite',
			'formatter' => function ($row) {
				return (null !== $row['audit_trail']->getEntity()->getVisitStatus() ) ? $this->translator->trans(ReportVisit::VISIT_STATUS[$row['audit_trail']->getEntity()->getVisitStatus()]) . ' (' .$row['audit_trail']->getEntity()->getVisitStatus() . ')' : '';
			},
			'formatter_csv' => 'formatter',
			'sortField' => 'reportVisit.visitStatus',
		]);

		$list->addColumn([
			'label' => 'Type de rapport',
			'formatter' => function ($row) {
				return (null !== $row['audit_trail']->getEntity()->getReportType()) ? $this->translator->trans(ReportVisit::REPORT_TYPE[$row['audit_trail']->getEntity()->getReportType()]) . ' (' .$row['audit_trail']->getEntity()->getReportType() . ')' : '';
			},
			'formatter_csv' => 'formatter',
			'sortField' => 'reportVisit.reportType',
		]);

		$list->addColumn([
			'label' => 'Statut de rapport',
			'formatter' => function ($row) {
				return (null !== $row['audit_trail']->getEntity()->getReportStatus()) ? $this->translator->trans(ReportVisit::REPORT_STATUS[$row['audit_trail']->getEntity()->getReportStatus()]) . ' (' .$row['audit_trail']->getEntity()->getReportStatus() . ')' : '';
			},
			'formatter_csv' => 'formatter',
			'sortField' => 'reportVisit.reportStatus',
		]);


        $this->setAuditTrailConfigPart2($list, $entityTrans, $entityTransArgs);

        return $list;
    }
}
