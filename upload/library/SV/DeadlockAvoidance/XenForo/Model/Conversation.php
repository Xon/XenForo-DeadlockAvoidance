<?php

class SV_DeadlockAvoidance_XenForo_Model_Conversation extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Conversation
{
    public function rebuildUnreadConversationCountForUser($userId)
    {
        if (SV_DeadlockAvoidance_Globals::registerPostTransactionClosure(function ()
        {
            parent::rebuildUnreadConversationCountForUser($userId);
        }))
        {
            return;
        }

        parent::rebuildUnreadConversationCountForUser($userId);
    }
}