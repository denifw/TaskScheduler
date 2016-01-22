<?php
namespace Scheduler;

/**
 * Class to handle Task Scheduler in windows task scheduler.
 * @package    Scheduler
 * @subpackage
 * @author     Deni Firdaus Waruwu <deni.firdaus.w@gmail.com>
 * @copyright  2015-2016 Deni Firdaus Waruwu
 */
class TaskScheduler implements \Scheduler\TaskInterface
{

    /**
     * Property to store the object of instance \scheduler\TaskInterface.
     *
     * @var \Scheduler\TaskInterface $Obj
     */
    private $Obj;

    /**
     * Property to store message from execution Schtasks query.
     *
     * @var array $message
     */
    private $message;

    /**
     * Property to store name of the task.
     *
     * @var string $name
     */
    private $name;

    /**
     * TaskScheduler constructor.
     *
     * @param string $name To set the name of the task.
     * @param string $action To set action of the task.
     * @param string $scheduleType To set the type of the schedule.
     *
     * @throws \Exception Schedule type is not exist.
     */
    public function __construct($name, $action = null, $scheduleType = null)
    {
        $this->Obj = null;
        $this->name = $name;
        if ($scheduleType !== null) {
            if (strtoupper($scheduleType) === 'DAILY') {
                $this->Obj = new \Scheduler\Task\DailyTask($name, $action);
            } elseif (strtoupper($scheduleType) === 'HOURLY') {
                $this->Obj = new \Scheduler\Task\HourlyTask($name, $action);
            } elseif (strtoupper($scheduleType) === 'MINUTE') {
                $this->Obj = new \Scheduler\Task\MinuteTask($name, $action);
            } elseif (strtoupper($scheduleType) === 'WEEKLY') {
                $this->Obj = new \Scheduler\Task\WeeklyTask($name, $action);
            } elseif (strtoupper($scheduleType) === 'MONTHLY') {
                $this->Obj = new \Scheduler\Task\MonthlyTask($name, $action);
            }
        }
    }

    /**
     * Function to register task into windows task scheduler system.
     *
     * @throws \Exception Schedule type is not exist.
     * @return boolean
     */
    public function doRegister()
    {
        if ($this->Obj === null) {
            $this->message[] = 'Scheduler type is not define.';
            return false;
        } else {
            $success = $this->Obj->doRegister();
            $this->message = $this->Obj->getMessage();
            return $success;
        }
    }

    /**
     * Function to enable task in windows task scheduler system.
     *
     * @return boolean
     */
    public function doEnable()
    {
        if ($this->isTaskNameValid($this->name) === true) {
            $query = 'SCHTASKS /CHANGE /TN "' . $this->name . '" /ENABLE';
            $execute = new \Scheduler\TaskExecute($query);
            $this->message = $execute->getResponse();
            return $execute->isSuccess();
        } else {
            return false;
        }
    }

    /**
     * Function to validate name of the task.
     *
     * @param string $name To store name of the task.
     *
     * @return boolean
     */
    private function isTaskNameValid($name)
    {
        if (empty(trim($name)) === true) {
            $this->message[] = 'Task name is required.';
            return false;
        } else {
            return true;
        }
    }

    /**
     * Function to disable task in windows task scheduler system.
     *
     * @return boolean
     */
    public function doDisable()
    {
        if ($this->isTaskNameValid($this->name) === true) {
            $query = 'SCHTASKS /CHANGE /TN "' . $this->name . '" /DISABLE';
            $execute = new \Scheduler\TaskExecute($query);
            $this->message = $execute->getResponse();
            return $execute->isSuccess();
        } else {
            return false;
        }
    }

    /**
     * Function to delete task from windows task scheduler system.
     *
     * @return boolean
     */
    public function doDelete()
    {
        if ($this->isTaskNameValid($this->name) === true) {
            $query = 'SCHTASKS /DELETE /TN "' . $this->name . '" /F';
            $execute = new \Scheduler\TaskExecute($query);
            $this->message = $execute->getResponse();
            return $execute->isSuccess();
        } else {
            return false;
        }
    }

    /**
     * Function to status of the task.
     *
     * @return string
     */
    public function getStatusTask()
    {
        $task = $this->getDataTaskByName();
        $status = 'Undefined';
        if (count($task) > 0) {
            if (empty($task['status']) === true) {
                if (strtolower($task['next run time']) === 'disabled') {
                    $status = $task['next run time'];
                }
            } else {
                $status = $task['status'];
            }
        }
        return $status;
    }

    /**
     * Function to get data task from windows task scheduler system by name of the task.
     *
     * @return array
     */
    public function getDataTaskByName()
    {
        $task = [];
        if ($this->isTaskNameValid($this->name) === true) {
            $query = 'SCHTASKS /QUERY /FO CSV /TN "' . $this->name . '"';
            $execute = new \Scheduler\TaskExecute($query);
            if ($execute->isSuccess() === true) {
                $task = $this->doPrepareDataTask($execute->getResponse());
            }
        }
        return $task;
    }

    /**
     * Function to prepare data task from scheduling task.
     *
     * @param string $arrayTask To set task data from scheduling task.
     *
     * @return array
     */
    private function doPrepareDataTask($arrayTask)
    {
        $task = [];
        if (count($arrayTask) !== 0) {
            $arrayKey = explode(',', str_replace("\"", "", $arrayTask[0]));
            $arrayValue = explode(',', str_replace("\"", "", $arrayTask[1]));
            $arrayLength = count($arrayKey);
            for ($indexTask = 0; $indexTask < $arrayLength; $indexTask++) {
                $task[strtolower($arrayKey[$indexTask])] = strtolower($arrayValue[$indexTask]);
            }
        }
        return $task;
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
        if ($this->Obj !== null and $startDateTime !== null and $startDateTime !== false) {
            $this->Obj->setStartDateTime($startDateTime);
        }
    }

    /**
     * Function to get message from execution process.
     *
     * @return array
     */
    public function getMessage()
    {
        return $this->message;
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
        if ($this->Obj !== null and strlen($modifier) > 0) {
            $this->Obj->setModifier($modifier);
        }
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
        if ($this->Obj !== null and strlen($month) > 0) {
            $this->Obj->setMonth($month);
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
        if ($this->Obj !== null and $endDateTime !== null and $endDateTime !== false) {
            $this->Obj->setEndDateTime($endDateTime);
        }
    }

    /**
     * Function to set number of day value for scheduling task.
     *
     * @param string $dayNumber To set number of day for scheduling task.
     *
     * @return void
     */
    public function setDayNumber($dayNumber)
    {
        if ($this->Obj !== null and strlen($dayNumber) > 0) {
            $this->setDayName($dayNumber);
        }
    }

    /**
     * Function to set name of day value for scheduling task.
     *
     * @param string $dayName To set name of day for scheduling task.
     *
     * @return void
     */
    public function setDayName($dayName)
    {
        if ($this->Obj !== null and strlen($dayName) > 0) {
            $this->setDayName($dayName);
        }
    }

    /**
     * Function to update task in windows task scheduler system.
     *
     * @return boolean
     */
    public function doUpdate()
    {
        if ($this->Obj === null) {
            $this->message[] = 'Scheduler type is not define.';
            return false;
        } else {
            $success = $this->Obj->doUpdate();
            $this->message = $this->Obj->getMessage();
            return $success;
        }
    }
}
