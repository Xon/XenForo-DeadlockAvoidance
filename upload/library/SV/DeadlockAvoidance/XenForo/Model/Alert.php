<?php

class SV_DeadlockAvoidance_XenForo_Model_Alert extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Alert
{
    public function alertUser($alertUserId, $userId, $username, $contentType, $contentId, $action, array $extraData = null)
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(
            function () use ($alertUserId, $userId, $username, $contentType, $contentId, $action, $extraData) {
                parent::alertUser($alertUserId, $userId, $username, $contentType, $contentId, $action, $extraData);
            }
        ))
        {
            return;
        }
        parent::alertUser($alertUserId, $userId, $username, $contentType, $contentId, $action, $extraData);
    }

    public function deleteAlerts($contentType, $contentId, $userId = null, $action = null)
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(
            function () use ($contentType, $contentId, $userId, $action) {
                parent::deleteAlerts($contentType, $contentId, $userId, $action);
            }
        ))
        {
            return;
        }
        parent::deleteAlerts($contentType, $contentId, $userId, $action);
    }
}
