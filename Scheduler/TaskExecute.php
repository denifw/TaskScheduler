<?php
namespace Scheduler;

/**
 * Class to execute syntax using php exec.
 * @package    Scheduler
 * @subpackage
 * @author     Deni Firdaus Waruwu <deni.firdaus.w@gmail.com>
 * @copyright  2015-2016 Deni Firdaus Waruwu
 */
class TaskExecute
{

    /**
     * Property to store value success of execution.
     *
     * @var boolean $success To store status of execution.
     */
    private $success;

    /**
     * Property to store response from execution.
     *
     * @var array $response To store message of execution.
     */
    private $response;

    /**
     * TaskExecute constructor.
     *
     * @param string $query To store query syntax.
     */
    public function __construct($query)
    {
        $this->executeSyntax($query);
    }

    /**
     * Function to execute schtasks query.
     *
     * @param string $query To store query syntax.
     *
     * @return void
     */
    private function executeSyntax($query)
    {
        exec($query, $response, $return);
        $this->success = false;
        if ($return === 0) {
            $this->success = true;
        }
        $this->response = $response;
    }

    /**
     * Function to get success value of execution.
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Function to get message of execution.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }
}
