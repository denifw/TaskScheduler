<?php
namespace Scheduler\Task;

/**
 * Class to handle daily scheduler.
 * @package    Scheduler
 * @subpackage Task
 * @author     Deni Firdaus Waruwu <deni.firdaus.w@gmail.com>
 * @copyright  2015-2016 Deni Firdaus Waruwu
 */
class DailyTask extends \Scheduler\AbstractTask
{

    /**
     * Function construct of class \scheduler\DailyTask.
     *
     * @param string $name To store name of the task.
     * @param string $action To store action of the task.
     */
    public function __construct($name, $action)
    {
        $this->setName($name);
        $this->setAction($action);
    }

    /**
     * Function to register task into windows task scheduler system.
     *
     * @return boolean
     */
    public function doRegister()
    {
        $listError = $this->validateSchtasksToRegisterDailyTask();
        if (count($listError) === 0) {
            $query = $this->generateRegisterQuery();
            $execute = new \Scheduler\TaskExecute($query);
            $this->message = $execute->getResponse();
            return $execute->isSuccess();
        } else {
            $this->message = $listError;
            return false;
        }
    }

    /**
     * Function to validate sch syntax to register daily task.
     *
     * @return array
     */
    private function validateSchtasksToRegisterDailyTask()
    {
        $errorMessage = [];
        if (empty(trim($this->getName())) === true or $this->getName() === null) {
            $errorMessage[] = "Task name is required.";
        }
        if (empty(trim($this->getAction())) === true or $this->getAction() === null) {
            $errorMessage[] = "Task action is required.";
        }
        if (empty(trim($this->getModifier())) === true or $this->getModifier() === null) {
            $errorMessage[] = "Modifier is required.";
        } else {
            if (is_numeric($this->modifier) === true) {
                $intModifier = (int)$this->modifier;
                if ($intModifier < 1 or $intModifier > 365) {
                    $errorMessage[] = "Value of Modifier is not valid. It's must be numeric with value 1 to 365.";
                }
            } else {
                $errorMessage[] = "Value of Modifier is not valid. It's must be numeric with value 1 to 365.";
            }
        }
        if ($this->startDateTime === null) {
            $errorMessage[] = "Start Date Time is required.";
        }
        if ($this->getEndDateTime() !== null and $this->getStartDateTime() === null) {
            # Compare the start and end datetime.
            if ($this->getEndDateTime() >= $this->getStartDateTime()) {
                # Get interval minute for startTime and endTime.
                $startTime = \DateTime::createFromFormat('H:i:s', $this->getStartDateTime()->format('H:i:s'));
                $endTime = \DateTime::createFromFormat('H:i:s', $this->getEndDateTime()->format('H:i:s'));
                $intervalHour = (integer)$startTime->diff($endTime)->format('%r%h');
                $minuteInterval = ($intervalHour * 60) + (integer)$startTime->diff($endTime)->format('%r%i');
                # Compare the endDate and StartDate.
                if ($this->getEndDateTime()->format('d/m/Y') === $this->getStartDateTime()->format('d/m/Y')) {
                    if ($minuteInterval <= 10) {
                        $errorMessage[] = "The duration (" . $minuteInterval . "m) between /ST and /ET must be greater than the repetition interval (10m)";
                    }
                } else {
                    if ($minuteInterval <= 10 and $minuteInterval >= 0) {
                        $errorMessage[] = "The duration (" . $minuteInterval . "m) between /ST and /ET must be greater than the repetition interval (10m)";
                    }
                }
            } else {
                $errorMessage[] = "The End date and Time must be later than the start date and time.";
            }
        }
        return $errorMessage;
    }

    /**
     * Function to generate query syntax for registering daily task.
     *
     * @return string
     */
    private function generateRegisterQuery()
    {
        $query = 'SCHTASKS /CREATE /SC DAILY';
        $query .= ' ' . $this->getSchSyntaxForModifier();
        $query .= ' ' . $this->getSchSyntaxForName();
        $query .= ' ' . $this->getSchSyntaxForAction();
        $query .= ' ' . $this->getSchSyntaxForStartDate();
        $query .= ' ' . $this->getSchSyntaxForStartTime();
        $query .= ' ' . $this->getSchSyntaxForEndDate();
        $query .= ' ' . $this->getSchSyntaxForEndTime();
        return $query;
    }

    /**
     * Function to update task in windows task scheduler system.
     *
     * @return boolean
     */
    public function doUpdate()
    {
        $listError = $this->validateSchtasksToRegisterDailyTask();
        if (count($listError) === 0) {
            $query = $this->generateUpdateQuery();
            $execute = new \Scheduler\TaskExecute($query);
            $this->message = $execute->getResponse();
            return $execute->isSuccess();
        } else {
            $this->message = $listError;
            return false;
        }
    }

    /**
     * Function to generate query syntax for updating daily task.
     *
     * @return string
     */
    private function generateUpdateQuery()
    {
        $query = 'SCHTASKS /CHANGE /SC DAILY';
        $query .= ' ' . $this->getSchSyntaxForModifier();
        $query .= ' ' . $this->getSchSyntaxForName();
        $query .= ' ' . $this->getSchSyntaxForAction();
        $query .= ' ' . $this->getSchSyntaxForStartDate();
        $query .= ' ' . $this->getSchSyntaxForStartTime();
        $query .= ' ' . $this->getSchSyntaxForEndDate();
        $query .= ' ' . $this->getSchSyntaxForEndTime();
        return $query;
    }
}
