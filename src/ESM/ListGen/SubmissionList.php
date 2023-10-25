<?php

namespace App\ESM\ListGen;

use App\ESM\Entity\Project;
use App\ESM\Entity\Submission;
use App\ESM\Service\ListGen\AbstractListGenType;

/**
 * Class MeetingList.
 */
class SubmissionList extends AbstractListGenType
{
	/**
	 * @return mixed
	 */
	public function getList(string $locale, Project $project)
	{
		$repository = $this->em->getRepository(Submission::class);
		$url        = 'project.submission.index.ajax';
		$urlArgs    = ['id' => $project->getId()];

		$list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
			->setClass('table')
			->setRowData(['id' => 'id'])
			->addHiddenData([
				'field' => 's',
				'alias' => 'submission',
			])
			->addHiddenData([
				'field' => 's.id',
			])
			->setRepository($repository)
			->setRepositoryMethod('indexListGenProjectSubmission', [$project->getId()])
			->setExportFileName('submissions')
			->addConstantSort('s.id', 'ASC');

		$list
			->addColumn([
				'label' => 'entity.Submission.field.country',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return $row['submission']->getCountry()->getName();
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.country',
			])
			->addColumn([
				'label' => 'entity.Submission.field.typeSubmission',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return $row['submission']->getTypeSubmission() ? $row['submission']->getTypeSubmission() : '';
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.typeSubmission',
			])
			->addColumn([
				'label' => 'entity.Submission.field.details',
				'formatter' => function ($row) {
					if (null !== $row['submission']->getTypeSubmission()) {
						if (3 === $row['submission']->getTypeSubmission()->getId()) {
							return $row['submission']->getTypeSubmission();
						} elseif (2 === $row['submission']->getTypeSubmission()->getId()) {
							return $row['submission']->getAmendmentNumber();
						} else {
							return '';
						}
					} else {
						return '';
					}

				},
				'formatter_csv' => 'formatter',
				'field' => 's.amendmentNumber',
			])
			->addColumn([
				'label' => 'entity.Submission.field.authorityType',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return $row['submission']->getTypeSubmissionRegulatory() ? $row['submission']->getTypeSubmissionRegulatory() : '';
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.typeSubmissionRegulatory',
			])
			->addColumn([
				'label' => 'entity.Submission.field.authorityName',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return $row['submission']->getNameSubmissionRegulatory() ? $row['submission']->getNameSubmissionRegulatory() : '';
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.nameSubmissionRegulatory',
			])
			->addColumn([
				'label' => 'entity.Submission.field.estimatedSubmissionAt',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return null === $row['submission']->getEstimatedSubmissionAt() ? '' : $row['submission']->getEstimatedSubmissionAt()->format('d/m/Y');
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.estimatedSubmissionAt',
			])
			->addColumn([
				'label' => 'entity.Submission.field.submissionAt',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return null === $row['submission']->getSubmissionAt() ? '' : $row['submission']->getSubmissionAt()->format('d/m/Y');
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.submissionAt',
			])
			->addColumn([
				'label' => 'entity.Submission.field.question',
				'field' => 's.question',
			])
			->addColumn([
				'label' => 'entity.Submission.field.comment',
				'field' => 's.comment',
				'formatter' => function ($row) use ($project) {
					if ($row['comment'] === null || $row['comment'] === "") {
						return '';
					} else {
						return substr($row['comment'], 0, 30) . '[...]';
					}
				},
			])
			->addColumn([
				'label' => 'entity.Submission.field.admissibilityAt',
				'translation_args' => ['%count%' => 1],
				'formatter' => function ($row) use ($project) {
					return null === $row['submission']->getAdmissibilityAt() ? '' : $row['submission']->getAdmissibilityAt()->format('d/m/Y');
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.admissibilityAt',
			])
			->addColumn([
				'label' => 'entity.Submission.field.fileNumber',
				'field' => 's.fileNumber',
			])
			->addColumn([
				'label' => 'entity.Submission.field.authorizationDeadlineAt',
				'formatter' => function ($row) use ($project) {
					return null === $row['submission']->getAuthorizationDeadlineAt() ? '' : $row['submission']->getAuthorizationDeadlineAt()->format('d/m/Y');
				},
				'formatter_csv' => 'formatter',
				'sortable' => false,
			])
			->addColumn([
				'label' => 'entity.Submission.field.authorizationAt',
				'formatter' => function ($row) use ($project) {
					return null === $row['submission']->getAuthorizationAt() ? '' : $row['submission']->getAuthorizationAt()->format('d/m/Y');
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.authorizationAt',
			])
			->addAction([ // enable/disable
				'displayer' => function ($row, $security) {
					return $security->isGranted('ROLE_SUBMISSION_READ');
				},
				'href' => function ($row) use ($project) {
					return $this->router->generate('project.submission.show', ['id' => $project->getId(), 'idSubmission' => $row['submission']->getId()]);
				},
				'formatter' => function ($row) {
					return '<i class="fa fa-eye c-grey"></i>';
				},
			])
			->addFilter([
				'label' => 'entity.Submission.field.country',
				'field' => 'c.id',
				'selectLabel' => 'c.name',
			], 'select')
			->addFilter([
				'label' => 'entity.Submission.field.typeSubmission',
				'field' => 'ts.id',
				'selectLabel' => 'ts.label',
			], 'select')
			->addFilter([
				'label' => 'entity.Submission.field.authorityName',
				'field' => 't.id',
				'selectLabel' => 't.name',
			], 'select')
			->addFilter([
				'label' => 'entity.Submission.field.authorityType',
				'field' => 'n.id',
				'selectLabel' => 'n.name',
			], 'select');

		if ($this->security->isGranted('ROLE_SUBMISSION_ARCHIVE')) {
			$list->addColumn([
				'label' => 'entity.status.label',
				'formatter' => function ($row) {
					return null === $row['submission']->getDeletedAt() ? 'Non archivé' : 'Archivé';
				},
				'formatter_csv' => 'formatter',
				'sortField' => 's.deletedAt',
			]);
			$list->addFilter([
				'label' => 'filter.archived.label',
				'translation_domain' => 'ListGen',
				'field' => 's.deletedAt',
				'defaultValues' => [0],
			], 'archived');
		}

		return $list;
	}
}
