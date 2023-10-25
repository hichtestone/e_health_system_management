<?php

namespace App\ESM\Message;

/**
 * Class ConditionMessage
 * @package App\Message
 */
class ConditionMessage
{
    /**
     * @var int
     */
    private $condition_id;

    public function __construct(int $condition_id)
    {
        $this->condition_id = $condition_id;
    }

    /**
     * @return int
     */
    public function getConditionId(): int
    {
        return $this->condition_id;
    }
}
