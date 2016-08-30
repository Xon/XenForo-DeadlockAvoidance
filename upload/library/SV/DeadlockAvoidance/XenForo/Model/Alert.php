<?php

class SV_DeadlockAvoidance_XenForo_Model_Alert extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Alert
{
    public function deleteAlerts($contentType, $contentId, $userId = null, $action = null)
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(function () use ($contentType, $contentId, $userId, $action)
        {
            parent::deleteAlerts($contentType, $contentId, $userId, $action);
        }))
        {
            return;
        }
        parent::deleteAlerts($contentType, $contentId, $userId, $action);
    }

    public function alertUser($alertUserId, $userId, $username, $contentType, $contentId, $action, array $extraData = null)
    {
        $db = $this->_getDb();
        XenForo_Db::beginTransaction($db);

        // read the xf_user table early to get a lock, but don't block other readers
        $db->query('
            SELECT user_id
            FROM xf_user
            WHERE user_id = ?
            LOCK IN SHARE MODE
        ', $alertUserId);
        // hoist bits out of the Like Transaction
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        $ret = false;
        try
        {
            parent::alertUser($alertUserId, $userId, $username, $contentType, $contentId, $action, $extraData);
            $ret = true;
            XenForo_Db::commit($db);
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction($ret);
        }
    }
}