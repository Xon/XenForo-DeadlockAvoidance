<?php

class SV_DeadlockAvoidance_DataWriter
{
    protected static $transactionCount = 0;
    protected static $postSaveAfterTransactionList = array();
    protected static $postSaveAfterTransactionListAlways = array();


    public static function enterTransaction()
    {
        self::$transactionCount += 1;
    }

    public static function registerPostTransactionClosure($closure, $alwaysExecute = false)
    {
        if (self::$transactionCount > 0)
        {
            if ($alwaysExecute)
            {
                self::$postSaveAfterTransactionListAlways[] = $closure;
            }
            else
            {
                self::$postSaveAfterTransactionList[] = $closure;
            }
            return true;
        }
        return false;
    }

    public static function exitTransaction($executePostTransaction)
    {
        self::$transactionCount -= 1;
        if (self::$transactionCount <= 0)
        {
            self::$transactionCount = 0;
            $postSaveAfterTransactionList = self::$postSaveAfterTransactionList;
            self::$postSaveAfterTransactionList = array();
            $postSaveAfterTransactionListAlways = self::$postSaveAfterTransactionListAlways;
            self::$postSaveAfterTransactionListAlways = array();
            foreach($postSaveAfterTransactionListAlways as $postSaveAfterTransaction)
            {
                $postSaveAfterTransaction();
            }
            if ($executePostTransaction)
            {
                foreach($postSaveAfterTransactionList as $postSaveAfterTransaction)
                {
                    $postSaveAfterTransaction();
                }
            }
        }
    }

    private function __construct() {}
}
