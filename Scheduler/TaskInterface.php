<?php
namespace Scheduler;

/**
 * Class to make sure than all the instance have the same function.
 * @package    Scheduler
 * @subpackage
 * @author     Deni Firdaus Waruwu <deni.firdaus.w@gmail.com>
 * @copyright  2015-2016 Deni Firdaus Waruwu
 */
interface TaskInterface
{

    /**
     * Function to register task into windows task scheduler system.
     *
     * @return boolean
     */
    public function doRegister();

    /**
     * Function to update task in windows task scheduler system.
     *
     * @return boolean
     */
    public function doUpdate();

    /**
     * Function to get message from execution process.
     *
     * @return array
     */
    public function getMessage();

    /**
     * Function to set start date and time to execute the task.
     *
     * @param \DateTime $startDateTime To store date and time.
     *
     * @return void
     */
    public function setStartDateTime($startDateTime);

    /**
     * Function to set end date and time for the task.
     *
     * @param \DateTime $endDateTime To store date and time.
     *
     * @return void
     */
    public function setEndDateTime($endDateTime);

    /**
     * Function to get modifier/interval execution task.
     *
     * @param string $modifier To store modifier/interval execution task.
     *
     * @return void
     */
    public function setModifier($modifier);

    /**
     * Function to set name of day value for scheduling task.
     *
     * @param string $dayName To set name of day for scheduling task.
     *
     * @return void
     */
    public function setDayName($dayName);

    /**
     * Function to set number of day value for scheduling task.
     *
     * @param string $dayNumber To set number of day for scheduling task.
     *
     * @return void
     */
    public function setDayNumber($dayNumber);

    /**
     * Function to set Month value for scheduling task.
     *
     * @param string $month To store value of the month.
     *
     * @return void
     */
    public function setMonth($month);
}
