<?php

class SV_DeadlockAvoidance_Listener
{
    public static function load_class($class, array &$extend)
    {
        $extend[] = 'SV_DeadlockAvoidance_' . $class;
    }
}
