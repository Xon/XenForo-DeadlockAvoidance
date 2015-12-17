<?php

class SV_DeadlockAvoidance_XenForo_Model_ThreadWatch extends XFCP_SV_DeadlockAvoidance_XenForo_Model_ThreadWatch
{
    protected function getLock($name, $timeout)
    {
        $db = $this->_getDb();
        return $db->fetchOne("select get_lock(?, ?)", array($name, $timeout));
    }

    protected function releaseLock($name)
    {
        $db = $this->_getDb();
        return $db->fetchOne("select release_lock(?)", array($name));
    }

    public function setThreadWatchState($userId, $threadId, $state)
    {
        $key = 'watch-'.$userId.'-'.$threadId;
        if (!$this->getLock($key, 1))
            return false;
        try
        {
            return parent::setThreadWatchState($userId, $threadId, $state);
        }
        finally
        {
            $this->releaseLock($key);
        }
    }
}