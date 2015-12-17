<?php

// This class is used to encapsulate global state between layers without using $GLOBAL[] or
// relying on the consumer being loaded correctly by the dynamic class autoloader
class SV_DeadlockAvoidance_Globals
{
    // permits conversation messages & conversation data writers to both use these things at the same time.
    public static $UsersToUpdateRefs = 0;
    // list of users who require having thier counters updated.
    public static $UsersToUpdate = null;

    private function __construct() {}
}
