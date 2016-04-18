<?php

class SV_DeadlockAvoidance_XenForo_Model_Post extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Post
{
    protected function _moveOrCopyPosts($action, array $posts, array $sourceThreads, array $targetThread, array $options = array())
    {
        if ($action != 'move')
        {
            return parent::_moveOrCopyPosts($action, $posts, $sourceThreads, $targetThread, $options);
        }

        // hoist bits out of the _moveOrCopyPosts Transaction
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        $ret = false;
        try
        {
            $ret = parent::_moveOrCopyPosts($action, $posts, $sourceThreads, $targetThread, $options);
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction($ret);
        }
        return $ret;
    }

    public function mergePosts(array $posts, array $threads, $targetPostId, $newMessage, $options = array())
    {
        // hoist bits out of the _moveOrCopyPosts Transaction
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        $ret = false;
        try
        {
            $ret = parent::mergePosts($posts, $threads, $targetPostId, $newMessage, $options);
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction($ret);
        }
        return $ret;
    }
}