<?php

namespace App\ESM\Service\ListGen;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * Loggable trait.
 */
trait AuditTrailTrait
{
    private $icones = [
        'form.archive' => '<i class="fa fa-archive c-grey"></i>',
        'form.restaure' => '<i class="fa fa-archive c-blue-d"></i>',
    ];

    protected function getCamelCaseCategory(string $category): string
    {
        $conv = new CamelCaseToSnakeCaseNameConverter(null, false);

        return $conv->denormalize($category);
    }

    protected function renderAuditTrailCsv(?array $details): string
    {
		if (null == $details) {
			return '';
		}
		$arrProp = [];
		foreach ($details as $prop => $val) {
			if (is_array($val) && 2 === count($val) && array_key_exists('added', $val) && array_key_exists('removed', $val)) {
				$arrProp[] = "$prop:  added: ".implode(',', $val['added']).', removed: '.implode(',', $val['removed']).'';

			} else {
				if (is_array($val)) {
					$val1 = empty($val[0]) ? 'null' : $val[0];
					$val2 = empty($val[1]) ? 'null' : $val[1];
					$arrProp[] = "$prop: $val1 => $val2";
				} else {
					$arrProp[] = "$prop: $val";
				}
			}
		}

		return implode(', ', $arrProp);
    }

    /**
     * gère affichage html modif audit trail.
     */
    protected function renderAuditTrail(?array $details): string
    {
        if (null == $details) {
            return '';
        }
        $arrProp = [];
        foreach ($details as $prop => $val) {
            if (is_array($val) && 2 === count($val) && array_key_exists('added', $val) && array_key_exists('removed', $val)) {
                $arrProp[] = "<div><i>$prop: <br />&nbsp;&nbsp;added: </i><strong>".implode(',', $val['added']).'</strong><br />&nbsp;&nbsp;removed: <strong>'.implode(',', $val['removed']).'</strong></div>';
            } else {
                if (is_array($val)) {
                    $val1 = empty($val[0]) ? 'null' : $val[0];
                    $val2 = empty($val[1]) ? 'null' : implode(', ', array_slice($val, 1));
                    $arrProp[] = "<div><i>$prop: </i> <strong>$val1</strong> => <strong>$val2</strong></div>";
                } else {
                    $arrProp[] = "<div><i>$prop: </i> <strong>$val</strong></div>";
                }
            }
        }

        return implode('', $arrProp);
    }

    protected function setAuditTrailConfigPart1($rep, string $url, array $urlArgs, array $repositoryArgs = [])
    {
        $list = $this->lg->setAjaxUrl($this->router->generate($url, $urlArgs))
            ->setClass('table')
            ->setRowData([])
            ->addHiddenData([
                'field' => 'at',
                'alias' => 'audit_trail',
            ])
            ->addHiddenData([
                'field' => 'u.id',
            ])
            ->setRepository($rep)
            ->setRepositoryMethod('auditTrailListGen', $repositoryArgs)
            ->addConstantSort('at.date', 'DESC')
        ;

        // columns
        $list->addColumn([
            'label' => 'audit_trail.updated_at',
            'formatter' => function ($row) {
                return $row['audit_trail']->getDate()->format('d/m/Y H:i:s');
            },
            'formatter_csv' =>  function ($row) {
				return $row['audit_trail']->getDate() ? $row['audit_trail']->getDate()->format('d/m/Y H:i:s') : '';
			},
            'sortField' => 'at.date',
        ])
            ->addColumn([
                'label' => 'audit_trail.updated_by',
                'formatter' => function ($row) {
                    if (null !== $row['audit_trail']->getUser()) {
                        return $row['audit_trail']->getUser()->getFullName();
                    } else {
                        return 'Clinfile Bot';
                    }
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'u.last_name',
            ])
;

        return $list;
    }

    protected function setAuditTrailConfigPart2($list, string $entityTrans, array $entityTransArgs)
    {
        $list
            ->addColumn([
                'label' => $entityTrans,
                'translation_args' => $entityTransArgs,
                'formatter' => function ($row) {
                    return $row['audit_trail']->getEntity();
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'e.last_name',
            ])
            ->addColumn([
                'label' => 'audit_trail.type.label',
                'formatter' => function ($row) {
                    return $this->translator->trans('audit_trail.type.'.$row['audit_trail']->getModifType());
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'at.modif_type',
            ])
            ->addColumn([
                'label' => 'audit_trail.reason.label',
                'formatter' => function ($row) {
                    if (null === $row['audit_trail']->getReason()) {
                        return '';
                    }

                    return $this->translator->trans('audit_trail.reason.'.$row['audit_trail']->getReason());
                },
                'formatter_csv' => 'formatter',
                'sortField' => 'at.reason',
            ])
            ->addColumn([
                'label' => 'audit_trail.details',
                'formatter' => function ($row) {
                    return $this->renderAuditTrail($row['audit_trail']->getDetails());
                },
                'formatter_csv' => function ($row) {
                    return $this->renderAuditTrailCsv($row['audit_trail']->getDetails());
                },
                'sortable' => false,
            ])
    ;

        // filters
        $list->addFilter([
            'label' => 'audit_trail.updated_by',
            'field' => 'CONCAT(u.firstName,u.lastName)',
        ], 'string')
            ->addFilter([
                'label' => 'audit_trail.updated_at',
                'field' => 'at.date',
            ], 'dates')
        ;

        return $list;
    }

	/**
	 * ajoute les colonnes et les filtres standards pour une liste audit trail.
	 *
	 * @param $rep
	 * @param string $url
	 * @param array $urlArgs
	 * @param string $entityTrans
	 * @param array $entityTransArgs
	 * @param array $repositoryArgs
	 * @return mixed
	 */
    protected function setAuditTrailConfig($rep, string $url, array $urlArgs, string $entityTrans, array $entityTransArgs, array $repositoryArgs = [])
    {
        $list = $this->setAuditTrailConfigPart1($rep, $url, $urlArgs, $repositoryArgs);

        return $this->setAuditTrailConfigPart2($list, $entityTrans, $entityTransArgs);
    }
}
