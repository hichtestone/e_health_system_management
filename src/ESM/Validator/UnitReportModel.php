<?php

namespace App\ESM\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class UnitReportModel.
 */
class UnitReportModel extends Constraint
{
    protected $options = [];

    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public $message = 'Entity.unit_report_model';
}
