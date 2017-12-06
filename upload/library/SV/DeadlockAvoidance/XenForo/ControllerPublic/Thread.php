<?php

class SV_DeadlockAvoidance_XenForo_ControllerPublic_Thread extends XFCP_SV_DeadlockAvoidance_XenForo_ControllerPublic_Thread
{
    public function actionReplyBans()
    {
        SV_DeadlockAvoidance_DataWriter::enterTransaction();
        $ret = false;
        try
        {
            $ret = parent::actionReplyBans();

            return $ret;
        }
        finally
        {
            SV_DeadlockAvoidance_DataWriter::exitTransaction(!empty($ret));
        }
    }
}
