<?php

class SV_DeadlockAvoidance_XenForo_DataWriter_ConversationMaster extends XFCP_SV_DeadlockAvoidance_XenForo_DataWriter_ConversationMaster
{
    protected function _postSave()
    {
        $this->_getConversationModel()->sv_deferRebuildUnreadCounters();

        parent::_postSave();
    }

    protected function _postSaveAfterTransaction()
    {
        $this->_getConversationModel()->sv_rebuildPendingUnreadCounters();

        parent::_postSaveAfterTransaction();
    }

    protected function _getConversationModel()
    {
        return $this->getModelFromCache('XenForo_Model_Conversation');
    }
}