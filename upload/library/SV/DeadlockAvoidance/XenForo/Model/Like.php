<?php

class SV_DeadlockAvoidance_XenForo_Model_Like extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Like
{
    public function likeContent($contentType, $contentId, $contentUserId, $likeUserId = null, $likeDate = null)
    {
        if (!$contentUserId)
        {
            return parent::likeContent($contentType, $contentId, $contentUserId, $likeUserId, $likeDate);
        }

        $visitor = XenForo_Visitor::getInstance();
        if ($likeUserId === null)
        {
            $likeUserId = $visitor['user_id'];
        }
        if (!$likeUserId)
        {
            return false;
        }

        $db = $this->_getDb();
        XenForo_Db::beginTransaction($db);

        // read the xf_user table early, with consistent ordering, to get a write lock. But don't block other readers
        $db->query('
            SELECT user_id
            FROM xf_user
            WHERE user_id in ('.$db->quote(array($contentUserId, $likeUserId)).')
            order by user_id
            LOCK IN SHARE MODE
        ');
        // hoist bits out of the Like Transaction
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        $ret = false;
        try
        {
            $ret = parent::likeContent($contentType, $contentId, $contentUserId, $likeUserId, $likeDate);
            XenForo_Db::commit($db);
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction($ret);
        }
        return $ret;
    }
}