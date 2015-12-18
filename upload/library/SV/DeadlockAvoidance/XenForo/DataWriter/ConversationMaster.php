<?php

class SV_DeadlockAvoidance_XenForo_DataWriter_ConversationMaster extends XFCP_SV_DeadlockAvoidance_XenForo_DataWriter_ConversationMaster
{
    public function save()
    {
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        try
        {
            return parent::save();
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction();
        }
    }

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
}