<?php

class SV_DeadlockAvoidance_XenForo_Model_Conversation extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Conversation
{
    public function rebuildUnreadConversationCountForUser($userId)
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(function () use ($userId)
        {
            XenForo_Db::beginTransaction();
            parent::rebuildUnreadConversationCountForUser($userId);
            XenForo_Db::commit();
        }))
        {
            return;
        }

        parent::rebuildUnreadConversationCountForUser($userId);
    }
}