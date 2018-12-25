<?php

class SV_DeadlockAvoidance_XenForo_Model_ThreadWatch extends XFCP_SV_DeadlockAvoidance_XenForo_Model_ThreadWatch
{
    public function setThreadWatchState($userId, $threadId, $state)
    {
        try
        {
            return parent::setThreadWatchState($userId, $threadId, $state);
        }
        /** @noinspection PhpRedundantCatchClauseInspection */
        catch (Zend_Db_Exception $e)
        {
            @XenForo_Db::rollbackAll();
            $code = $e->getCode();
            if ($code == 1062 || $code == 1213 ||
                stripos($e->getMessage(), "Deadlock found when trying to get lock; try restarting transaction") !== false ||
                stripos($e->getMessage(), "Duplicate entry") !== false)
            {
                return false;
            }
            else
            {
                throw $e;
            }
        }
    }
}
