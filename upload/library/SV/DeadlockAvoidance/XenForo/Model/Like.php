<?php

class SV_DeadlockAvoidance_XenForo_Model_Like extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Like
{
    public function likeContent($contentType, $contentId, $contentUserId, $likeUserId = null, $likeDate = null)
    {
        // hoist bits out of the Like Transaction
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        $ret = false;
        try
        {
            $ret = parent::likeContent($contentType, $contentId, $contentUserId, $likeUserId, $likeDate);
        }
            /** @noinspection PhpRedundantCatchClauseInspection */
        catch (Zend_Db_Statement_Mysqli_Exception $e)
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction(false);
            @XenForo_Db::rollbackAll();
            // something went wrong, retry
            if (stripos($e->getMessage(), "Deadlock found when trying to get lock; try restarting transaction") !== false)
            {
                SV_DeadlockAvoidance_DataWriter::enterTransaction();
                $ret = parent::likeContent($contentType, $contentId, $contentUserId, $likeUserId, $likeDate);
            }
            else
            {
                throw $e;
            }
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction($ret);
        }

        return $ret;
    }
}
