<?php

class SV_DeadlockAvoidance_DataWriter
{
    protected static $transactionCount = 0;
    protected static $postSaveAfterTransactionList = array();


    public static function enterTransaction()
    {
        self::$transactionCount += 1;
    }

    public static function registerPostTransactionClosure($closure)
    {
        if (self::$transactionCount > 0)
        {
            self::$postSaveAfterTransactionList[] = $closure;        
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
            $postSaveAfterTransactionList = array_reverse(self::$postSaveAfterTransactionList);
            self::$postSaveAfterTransactionList = array();
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
