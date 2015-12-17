<?php

class SV_DeadlockAvoidance_XenForo_Model_Conversation extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Conversation
{
    public function sv_deferRebuildUnreadCounters()
    {
        if (SV_DeadlockAvoidance_Globals::$UsersToUpdate === null)
        {
            SV_DeadlockAvoidance_Globals::$UsersToUpdate = array();
        }
        SV_DeadlockAvoidance_Globals::$UsersToUpdateRefs++;
    }

    public function sv_rebuildPendingUnreadCounters()
    {
        SV_DeadlockAvoidance_Globals::$UsersToUpdateRefs--;
        if (SV_DeadlockAvoidance_Globals::$UsersToUpdateRefs > 0)
        {
            return;
        }

        if (SV_DeadlockAvoidance_Globals::$UsersToUpdate !== null)
        {
            $userIds = SV_DeadlockAvoidance_Globals::$UsersToUpdate;
            SV_DeadlockAvoidance_Globals::$UsersToUpdate = null;
            foreach($userIds as $userId => $null)
            {
                XenForo_Db::beginTransaction();
                $this->rebuildUnreadConversationCountForUser($userId);
                XenForo_Db::commit();
            }
        }
    }

    public function rebuildUnreadConversationCountForUser($userId)
    {
        if (SV_DeadlockAvoidance_Globals::$UsersToUpdate !== null)
        {
            SV_DeadlockAvoidance_Globals::$UsersToUpdate[$userId] = true;
            return;
        }
        parent::rebuildUnreadConversationCountForUser($userId);
    }
}