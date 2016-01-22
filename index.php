<?php


/**
 * Created by PhpStorm.
 * User: Deni
 * Date: 11/30/15
 * Time: 8:58 AM
 */
#Load and initialize any file.
include('Scheduler/TaskInterface.php');
include('Scheduler/TaskExecute.php');
include('Scheduler/AbstractTask.php');
include('Scheduler/task/DailyTask.php');
include('Scheduler/task/HourlyTask.php');
include('Scheduler/task/MinuteTask.php');
include('Scheduler/task/WeeklyTask.php');
include('Scheduler/task/MonthlyTask.php');
include('Scheduler/TaskScheduler.php');

/*
 Case :
    i wanna make a scheduler to shutdown my laptop every day at 01:00 am.
 * */

# Create instance of windows task scheduler;

$taskName = 'My_First_Task';
$taskAction = 'shutdown -s';
# Minute, Hourly, Daily, Weekly and Monthly
$taskType = 'Daily';
$taskModifier = '1';
$startDateTime = \DateTime::createFromFormat('d/m/Y H:i:s', date('d/m/Y') . ' 01:00:00');
$endDateTime = null;
$dayName = '';
$dayNumber = '';
$month = '';

$task = new \Scheduler\TaskScheduler($taskName, $taskAction, $taskType);
$task->setModifier($taskModifier);
$task->setStartDateTime($startDateTime);
$task->setEndDateTime($endDateTime);
$task->setDayName($dayName);
$task->setDayNumber($dayNumber);
$task->setMonth($month);

# Register task into windows task scheduler.
$task->doRegister();
echo implode('<br>', $task->getMessage());
