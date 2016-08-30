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
}