<?php
namespace Scheduler;

/**
 * Abstract class for scheduler task.
 * @package    Scheduler
 * @subpackage
 * @author     Deni Firdaus Waruwu <deni.firdaus.w@gmail.com>
 * @copyright  2015-2016 Deni Firdaus Waruwu
 */
abstract class AbstractTask implements \Scheduler\TaskInterface
{

    /**
     * Property to store name of the task.
     *
     * @var string $taskName
     */
    protected $name;

    /**
     * Property to store task.
     *
     * @var string $task
     */
    protected $action;

    /**
     * Property to store modifier/interval execution of the task.
     *
     * @var string $modifier
     */
    protected $modifier;

    /**
     * Property to store start date and time to execute the task.
     *
     * @var \DateTime $startDateTime
     */
    protected $startDateTime;

    /**
     * Property to store end date and time of the task.
     *
     * @var \DateTime $endDateTime
     */
    protected $endDateTime;

    /**
     * Property to store message from execution Schtasks query.
     *
     * @var array $message
     */
    protected $message;

    /**
     * Property to store name of the day from execution Schtasks query.
     *
     * @var string $dayName
     */
    protected $dayName;

    /**
     * Property to store number of the day from execution Schtasks query.
     *
     * @var string $dayNumber
     */
    protected $dayNumber;

    /**
     * Property to store month from execution Schtasks query.
     *
     * @var string $month
     */
    protected $month;

    /**
     * Function to get name of the task.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Function to set name of the task.
     *
     * @param string $name To store name of the task.
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Function to get action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Funtion to set action.
     *
     * @param string $action To store the task.
     *
     * @return void
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Funtion to get modifier/interval execution task.
     *
     * @return string
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * Function to get modifier/interval execution task.
     *
     * @param string $modifier To store modifier/interval execution task.
     *
     * @return void
     */
    public function setModifier($modifier)
    {
        $this->modifier = $modifier;
    }

    /**
     * Funtion to get message from execution schtasks query.
     *
     * @return array
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Function to set message for execution schtasks query.
     *
     * @param array $message To store the message.
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Function to get start date to execute the task.
     *
     * @return \DateTime
     */
    public function getStartDateTime()
    {
        $day = $this->startDateTime->format("d");
        $month = $this->startDateTime->format("m");
        $year = $this->startDateTime->format("Y");
        $hour = $this->startDateTime->format("H");
        $minute = $this->startDateTime->format("i");
        $second = $this->startDateTime->format("s");
        $newDateTime = $day . '/' . $month . '/' . $year . ' ' . $hour . ':' . $minute . ':' . $second;
        return \DateTime::createFromFormat('d/m/Y H:i:s', $newDateTime);
    }

    /**
     * Function to set date and time to execute the task.
     *
     * @param \DateTime $startDateTime To store date and time.
     *
     * @return void
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * Function to get end date and time for the task.
     *
     * @return \DateTime
     */
    public function getEndDateTime()
    {
        if ($this->endDateTime !== null) {
            $day = $this->endDateTime->format("d");
            $month = $this->endDateTime->format("m");
            $year = $this->endDateTime->format("Y");
            $hour = $this->endDateTime->format("H");
            $minute = $this->endDateTime->format("i");
            $second = $this->endDateTime->format("s");
            $newDateTime = $day . '/' . $month . '/' . $year . ' ' . $hour . ':' . $minute . ':' . $second;
            return \DateTime::createFromFormat('d/m/Y H:i:s', $newDateTime);
        } else {
            return $this->endDateTime;
        }
    }

    /**
     * Function to set end date and time for the task.
     *
     * @param \DateTime $endDateTime To store date and time.
     *
     * @return void
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    /**
     * Function to get Name of thr Day value for scheduling task.
     *
     * @return string
     */
    public function getDayName()
    {
        return strtoupper($this->dayName);
    }

    /**
     * Function to set Day value for scheduling task.
     *
     * @param string $dayName To set the day for scheduling task.
     *
     * @return void
     */
    public function setDayName($dayName)
    {
        $this->dayName = $dayName;
    }

    /**
     * Function to get Month value for scheduling task.
     *
     * @return string
     */
    public function getMonth()
    {
        return strtoupper($this->month);
    }

    /**
     * Function to set Month value for scheduling task.
     *
     * @param string $month To store value of the month.
     *
     * @return void
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * Function to get number of day for scheduling task.
     *
     * @return string
     */
    public function getDayNumber()
    {
        return $this->dayNumber;
    }

    /**
     * Function to set number of day  for scheduling task.
     *
     * @param string $dayNumber To store the value of day number.
     *
     * @return void
     */
    public function setDayNumber($dayNumber)
    {
        $this->dayNumber = $dayNumber;
    }

    /**
     * Function to generate Task Name syntax for schtasks.
     *
     * @return string
     */
    public function getSchSyntaxForName()
    {
        if (empty(trim($this->name)) === true) {
            return '';
        } else {
            return '/TN "' . $this->name . '"';
        }
    }

    /**
     * Function to generate Start Date syntax for schtasks.
     *
     * @return string
     */
    public function getSchSyntaxForStartDate()
    {
        if ($this->startDateTime === null) {
            return '';
        } else {
            return '/SD ' . $this->startDateTime->format('d/m/Y');
        }
    }

    /**
     * Function to generate Start Time syntax for schtasks.
     *
     * @return string
     */
    public function getSchSyntaxForStartTime()
    {
        if ($this->startDateTime === null) {
            return '';
        } else {
            return '/ST ' . $this->startDateTime->format('H:i:s');
        }
    }

    /**
     * Function to generate end Date syntax for schtasks.
     *
     * @return string
     */
    public function getSchSyntaxForEndDate()
    {
        if ($this->endDateTime === null) {
            return '';
        } else {
            return '/ED ' . $this->endDateTime->format('d/m/Y');
        }
    }

    /**
     * Function to generate end Time syntax for schtasks.
     *
     * @return string
     */
    public function getSchSyntaxForEndTime()
    {
        if ($this->endDateTime === null) {
            return '';
        } else {
            return '/ET ' . $this->endDateTime->format('H:i:s');
        }
    }

    /**
     * Function to generate Modifier Task syntax for schtasks.
     *
     * @return string
     */
    public function getSchSyntaxForModifier()
    {
        if ($this->modifier === null) {
            return '';
        } else {
            return '/MO ' . $this->modifier;
        }
    }

    /**
     * Function to generate Task Run syntax for schtasks.
     *
     * @return string
     */
    public function getSchSyntaxForAction()
    {
        if ($this->action === null) {
            return '';
        } else {
            return '/TR "curl ' . $this->action . '"';
        }
    }

    /**
     * Function to generate Day syntax for schtasks.
     *
     * @param string $type To store type of scheduler.
     *
     * @return string
     */
    public function getSchSyntaxForDay($type)
    {
        $syntax = '';
        if (strtolower($type) === 'weekly') {
            if ($this->dayName !== null) {
                $syntax = '/D ' . $this->dayName;
            }
        } elseif (strtolower($type) === 'monthly') {
            if ($this->modifier !== null) {
                if (is_numeric($this->modifier) === true) {
                    if ($this->dayNumber !== null) {
                        $syntax = '/D ' . $this->dayNumber;
                    }
                } else {
                    if ($this->dayName !== null and strtolower($this->modifier) !== 'LASTDAY') {
                        $syntax = '/D ' . $this->dayName;
                    }
                }
            }
        }
        return $syntax;
    }

    /**
     * Function to generate Month syntax for schtasks.
     *
     * @return string
     */
    public function getSchSyntaxForMonth()
    {
        if ($this->month === null) {
            return '';
        } else {
            return '/M ' . $this->month;
        }
    }
}
