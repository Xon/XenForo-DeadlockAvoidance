<?php

class SV_DeadlockAvoidance_XenForo_DataWriter_ReportComment extends XFCP_SV_DeadlockAvoidance_XenForo_DataWriter_ReportComment
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