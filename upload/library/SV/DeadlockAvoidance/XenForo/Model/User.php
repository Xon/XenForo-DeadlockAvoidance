<?php

class SV_DeadlockAvoidance_XenForo_Model_User extends XFCP_SV_DeadlockAvoidance_XenForo_Model_User
{
    public function follow(array $followUsers, $dupeCheck = true, array $user = null)
    {
        if ($user === null)
        {
            $user = XenForo_Visitor::getInstance()->toArray();
        }
        try
        {
            return parent::follow($followUsers, $dupeCheck, $user);
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
