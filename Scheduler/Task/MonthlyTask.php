<?php
namespace Scheduler\Task;

/**
 * Class to handle monthly task.
 * @package    Scheduler
 * @subpackage Task
 * @author     Deni Firdaus Waruwu <deni.firdaus.w@gmail.com>
 * @copyright  2015-2016 Deni Firdaus Waruwu
 */
class MonthlyTask extends \Scheduler\AbstractTask
{

    /**
     * Function construct of class MonthlyTask
     *
     * @param string $name To store name of the task.
     * @param string $action To store action if the task.
     */
    public function __construct($name, $action)
    {
        $this->setName($name);
        $this->setAction($action);
        $this->setModifier(1);
        $dateTime = new \DateTime();
        $this->setStartDateTime($dateTime->add(new \DateInterval('P1M')));
    }

    /**
     * Function to register task into windows task scheduler system.
     *
     * @return boolean
     */
    public function doRegister()
    {
        $listError = $this->validateSchtasksToRegisterMonthlyTask();
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
     * Function to validate sch syntax to register monthly task.
     * LastDay harus mengisi Bulan /M
     * Last Harus Mengisi Hari /D
     *
     * @return array
     */
    private function validateSchtasksToRegisterMonthlyTask()
    {
        $errorMessage = [];
        if (empty(trim($this->getName())) === 0 or $this->getName() === null) {
            $errorMessage[] = "Task name is required.";
        }
        if (empty(trim($this->getAction())) === 0 or $this->getAction() === null) {
            $errorMessage[] = "Task action is required.";
        }
        if (empty(trim($this->getModifier())) === 0 or $this->getModifier() === null) {
            $errorMessage[] = "Modifier is required.";
        } else {
            $allowedModifierValues = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', 'FIRST', 'SECOND', 'THIRD', 'FOURTH', 'LAST', 'LASTDAY'];
            $indexOfModifier = array_search(strtoupper($this->getModifier()), $allowedModifierValues, null);
            if ($indexOfModifier !== false) {
                if (strtoupper($this->getModifier()) === 'LASTDAY') {
                    if ($this->month === null) {
                        $errorMessage[] = "Month parameter must be specified. Can be JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV or DEC.";
                    } else {
                        $allowedMonthlyValue = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'NOV', 'DEC'];
                        $indexOfMonth = array_search($this->getMonth(), $allowedMonthlyValue, null);
                        if ($indexOfMonth === false) {
                            $errorMessage[] = "Invalid value for Month parameter. It's must be JAN, FEB, MAR, APR, MAY, JUN, JUL, AUG, SEP, OCT, NOV or DEC.";
                        }
                    }
                } elseif ($indexOfModifier >= 12 and $indexOfModifier <= 15) {
                    $arrayDayName = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
                    $indexOfDay = array_search(strtoupper($this->getDayName()), $arrayDayName, null);
                    if ($indexOfDay === false) {
                        $errorMessage[] = "Value of Day Name is not valid. It must the first 3 letter of the day name";
                    }
                } else {
                    if ($this->getDayNumber() !== null and ($this->getDayNumber() > 31 or $this->getDayNumber() < 1)) {
                        $errorMessage[] = "Value of Day Number is not valid, the value must be 1 to 31.";
                    }
                }
            } else {
                $errorMessage[] = "Value of Modifier is not valid. It's must be numeric with value 1 to 12 or can also use FIRST, SECOND, THIRD, FOURTH, LAST, LASTDAY.";
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
     * Function to generate query syntax for registering monthly task.
     *
     * @return string
     */
    private function generateRegisterQuery()
    {
        $query = 'SCHTASKS /CREATE /SC MONTHLY';
        $query .= ' ' . $this->getSchSyntaxForModifier();
        $query .= ' ' . $this->getSchSyntaxForName();
        $query .= ' ' . $this->getSchSyntaxForAction();
        $query .= ' ' . $this->getSchSyntaxForStartDate();
        $query .= ' ' . $this->getSchSyntaxForStartTime();
        if (strtoupper($this->getModifier()) === 'LASTDAY') {
            $query .= ' ' . $this->getSchSyntaxForMonth();
        } else {
            $query .= ' ' . $this->getSchSyntaxForDay("MONTHLY");
        }
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
        $listError = $this->validateSchtasksToRegisterMonthlyTask();
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
     * Function to generate query syntax for update monthly task.
     *
     * @return string
     */
    private function generateUpdateQuery()
    {
        $query = 'SCHTASKS /CHANGE /SC MONTHLY';
        $query .= ' ' . $this->getSchSyntaxForModifier();
        $query .= ' ' . $this->getSchSyntaxForName();
        $query .= ' ' . $this->getSchSyntaxForAction();
        $query .= ' ' . $this->getSchSyntaxForStartDate();
        $query .= ' ' . $this->getSchSyntaxForStartTime();
        if (strtoupper($this->getModifier()) === 'LASTDAY') {
            $query .= ' ' . $this->getSchSyntaxForMonth();
        } else {
            $query .= ' ' . $this->getSchSyntaxForDay("MONTHLY");
        }
        $query .= ' ' . $this->getSchSyntaxForEndDate();
        $query .= ' ' . $this->getSchSyntaxForEndTime();
        return $query;
    }
}
