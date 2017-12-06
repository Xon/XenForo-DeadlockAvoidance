<?php

class SV_DeadlockAvoidance_XenForo_DataWriter_Forum extends XFCP_SV_DeadlockAvoidance_XenForo_DataWriter_Forum
{
    public function save()
    {
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        $ret = false;
        try
        {
            $ret = parent::save();

            return $ret;
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction($ret);
        }
    }

    protected function _postSaveAfterTransaction()
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(
            function () {
                parent::_postSaveAfterTransaction();
            }
        ))
        {
            return;
        }

        parent::_postSaveAfterTransaction();
    }

    // rewrite to push updates the xf_forum outside the transaction use hand written SQL rather than the datawriter to avoid
    // read-mutate-update as seperate steps
    protected function _updateCountersAfterDiscussionSave(XenForo_DataWriter_Discussion $discussionDw, $forceInsert = false)
    {
        if ($discussionDw->get('discussion_type') == 'redirect')
        {
            // note: this assumes the discussion type will never change to/from this except at creation
            return;
        }
        $db = $this->_db;
        $removePost = false;
        $params = array();
        $components = array();

        if ($discussionDw->get('discussion_state') == 'visible'
            && ($discussionDw->getExisting('discussion_state') != 'visible' || $forceInsert)
        )
        {
            $params[] = 1;
            $components[] = 'discussion_count = discussion_count + ?';

            $params[] = $discussionDw->get('reply_count') + 1;
            $components[] = 'message_count = GREATEST(0, cast(message_count as signed) + ?';
        }
        else if ($discussionDw->getExisting('discussion_state') == 'visible' && $discussionDw->get('discussion_state') != 'visible')
        {
            $params[] = -1;
            $components[] = 'discussion_count = discussion_count + ?';

            $params[] = -$discussionDw->get('reply_count') - 1;
            $components[] = 'message_count = GREATEST(0, cast(message_count as signed) + ?';

            if ($discussionDw->get('last_post_id') == $this->get('last_post_id'))
            {
                $removePost = true;
            }
        }
        else if ($discussionDw->get('discussion_state') == 'visible' && $discussionDw->getExisting('discussion_state') == 'visible')
        {
            // no state change, probably just a reply
            $params[] = $discussionDw->get('reply_count') - $discussionDw->getExisting('reply_count');
            $components[] = 'message_count = GREATEST(0, cast(message_count as signed) + ?';
        }

        // atomically update the counters
        if ($params && $components)
        {
            $sql = implode(', ', $components);
            $params[] = $this->get('node_id');
            $db->query(
                "
                update xf_forum
                set {$sql}
                where node_id = ?
            ", $params
            );
        }

        $params = array();
        $components = array();
        $components[] = 'last_post_date = ?';
        $components[] = 'last_post_id = ?';
        $components[] = 'last_post_user_id = ?';
        $components[] = 'last_post_username = ?';
        $components[] = 'last_thread_title = ?';
        if ($removePost)
        {
            $params[] = 0;
            $params[] = 0;
            $params[] = 0;
            $params[] = '';
            $params[] = '';
        }
        else
        {
            $params[] = $discussionDw->get('last_post_date');
            $params[] = $discussionDw->get('last_post_id');
            $params[] = $discussionDw->get('last_post_user_id');
            $params[] = $discussionDw->get('last_post_username');
            $params[] = $discussionDw->get('title');
        }

        // update the last
        if ($params && $components)
        {
            $sql = implode(', ', $components);
            $params[] = $this->get('node_id');
            $params[] = $discussionDw->get('last_post_date');
            $params[] = $discussionDw->get('last_post_id');
            $params[] = $discussionDw->get('last_post_username');
            $params[] = $discussionDw->get('title');
            $db->query(
                "
                update xf_forum
                set {$sql}
                where node_id = ? and (last_post_date != ? or last_post_id != ? or last_post_username != ? or last_thread_title != ?)
            ", $params
            );
        }
    }

    public function updateCountersAfterDiscussionSave(XenForo_DataWriter_Discussion $discussionDw, $forceInsert = false)
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(
            function () use ($discussionDw, $forceInsert) {
                $this->_updateCountersAfterDiscussionSave($discussionDw, $forceInsert);
            }
        ))
        {
            return;
        }
        $this->_updateCountersAfterDiscussionSave($discussionDw, $forceInsert);
    }
}
