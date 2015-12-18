<?php

class SV_DeadlockAvoidance_XenForo_DataWriter_Discussion_Thread extends XFCP_SV_DeadlockAvoidance_XenForo_DataWriter_Discussion_Thread
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