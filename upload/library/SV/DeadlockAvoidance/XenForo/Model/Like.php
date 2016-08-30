<?php

class SV_DeadlockAvoidance_XenForo_Model_Like extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Like
{
    public function likeContent($contentType, $contentId, $contentUserId, $likeUserId = null, $likeDate = null)
    {
        if (!$contentUserId)
        {
            return parent::likeContent($contentType, $contentId, $contentUserId, $likeUserId, $likeDate);
        }

        // hoist bits out of the Like Transaction
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        $ret = false;
        try
        {
            $ret = parent::likeContent($contentType, $contentId, $contentUserId, $likeUserId, $likeDate);
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction($ret);
        }
        return $ret;
    }
}