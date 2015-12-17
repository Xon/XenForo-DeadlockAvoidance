<?php

// This class is used to encapsulate global state between layers without using $GLOBAL[] or
// relying on the consumer being loaded correctly by the dynamic class autoloader
class SV_DeadlockAvoidance_Globals
{
    // permits conversation messages & conversation data writers to both use these things at the same time.
    public static $UsersToUpdateRefs = 0;
    // list of users who require having their counters updated.
    public static $UsersToUpdate = null;

    protected static $transactionCount = 0;
    protected static $postSaveAfterTransactionList = array();


    public static enterTransaction()
    {
        self::$transactionCount += 1;
    }

    public static skipPostSaveAfterTransaction()
    {
        if (self::$transactionCount == 1 && empty(self::$postSaveAfterTransactionList))
        {
            self::$postSaveAfterTransactionList = null;
            return true;
        }
        return false;
    }

    public static exitTransaction($closure)
    {
        self::$transactionCount -= 1;
        if (self::$postSaveAfterTransactionList !== null)
        {
            self::$postSaveAfterTransactionList[] = $closure;
        }
        if (self::$transactionCount <= 0)
        {
            self::$transactionCount = 0;
            $postSaveAfterTransactionList = self::$postSaveAfterTransactionList;
            self::$postSaveAfterTransactionList = array();
            foreach($postSaveAfterTransactionList as $postSaveAfterTransaction)
            {
                $postSaveAfterTransaction();
            }
        }
    }

    private function __construct() {}
}
