<?php

class SV_DeadlockAvoidance_XenForo_DataWriter_DiscussionMessage_Post extends XFCP_SV_DeadlockAvoidance_XenForo_DataWriter_DiscussionMessage_Post
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
/*
    protected function _update()
    {
        if ($this->isChanged('message_state') &&
            ($this->get('message_state') == 'visible' || $this->getExisting('message_state') == 'visible'))
        {
            $this->_db->query('SELECT post_id
                FROM xf_post
                WHERE thread_id = ? and position >= ?
                FOR UPDATE
            ', array($this->get('thread_id'), $this->getExisting('position')));
        }

        parent::_update();
    }

    protected function _delete()
    {
        if ($this->get('message_state') == 'visible' || $this->getExisting('message_state') == 'visible')
        {
            $this->_db->query('SELECT post_id
                FROM xf_post
                WHERE thread_id = ? and position >= ?
                FOR UPDATE
            ', array($this->get('thread_id'), $this->getExisting('position')));
        }

        parent::_delete();
    }
*/
    protected function _postSaveAfterTransaction()
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(function ()
        {
            parent::_postSaveAfterTransaction();
        }))
        {
            return;
        }

        parent::_postSaveAfterTransaction();
    }

    public function _indexForSearch()
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(function ()
        {
            parent::_indexForSearch();
        }))
        {
            return;
        }
        parent::_indexForSearch();
    }
}