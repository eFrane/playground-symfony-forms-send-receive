<?php

namespace AppBundle\Model;

use DateTime;
use Doctrine\DBAL\Types\DateType;

class Task {
    /**
     * @var string
     */
    protected $task;

    /**
     * @var DateTime
     */
    protected $dueDate;

    /**
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param string $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }

    /**
     * @return DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param DateTime $dueDate
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
    }
}
