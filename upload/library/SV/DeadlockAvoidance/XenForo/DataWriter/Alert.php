<?php

class SV_DeadlockAvoidance_XenForo_DataWriter_Alert extends XFCP_SV_DeadlockAvoidance_XenForo_DataWriter_Alert
{
    protected function _postSave()
    {
        $this->_db->query('
            UPDATE xf_user SET
            alerts_unread = LEAST(alerts_unread + 1, 65535)
            WHERE user_id = ?
        ', $this->get('alerted_user_id'));
    }
}