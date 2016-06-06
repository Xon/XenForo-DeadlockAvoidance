<?php

class SV_DeadlockAvoidance_XenForo_Model_User extends XFCP_SV_DeadlockAvoidance_XenForo_Model_User
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

    public function follow(array $followUsers, $dupeCheck = true, array $user = null)
    {
        if ($user === null)
        {
            $user = XenForo_Visitor::getInstance()->toArray();
        }
        $key = 'follow-'.$user['user_id'];
        if (!$this->getLock($key, 1))
        {
            return false;
        }
        try
        {
            return parent::follow($followUsers, $dupeCheck, $user);
        }
        finally
        {
            $this->releaseLock($key);
        }
    }
}