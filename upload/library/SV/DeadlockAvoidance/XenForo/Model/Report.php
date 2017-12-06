<?php

class SV_DeadlockAvoidance_XenForo_Model_Report extends XFCP_SV_DeadlockAvoidance_XenForo_Model_Report
{
    public function sendAlertsOnReportResolution(array $report, $comment = '')
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(
            function () use ($report, $comment) {
                parent::sendAlertsOnReportResolution($report, $comment);
            }
        ))
        {
            return;
        }
        parent::sendAlertsOnReportResolution($report, $comment);
    }

    public function sendAlertsOnReportRejection(array $report, $comment = '')
    {
        if (SV_DeadlockAvoidance_DataWriter::registerPostTransactionClosure(
            function () use ($report, $comment) {
                parent::sendAlertsOnReportRejection($report, $comment);
            }
        ))
        {
            return;
        }
        parent::sendAlertsOnReportRejection($report, $comment);
    }
}
