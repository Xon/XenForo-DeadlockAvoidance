<?php

class SV_DeadlockAvoidance_XenForo_DataWriter_ConversationMessage extends XFCP_SV_DeadlockAvoidance_XenForo_DataWriter_ConversationMessage
{
    public function save()
    {
        SV_DeadlockAvoidance_Globals::enterTransaction();
        try
        {
            return parent::save();
        }
        finally
        {
            SV_DeadlockAvoidance_Globals::exitTransaction();
        }
    }

    protected function _postSaveAfterTransaction()
    {
        if (SV_DeadlockAvoidance_Globals::registerPostTransactionClosure(function ()
        {
            $this->_postSaveAfterTransaction();
        }))
        {
            return;
        }

        parent::_postSaveAfterTransaction();
    }
}