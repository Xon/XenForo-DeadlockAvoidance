<?php

class SV_DeadlockAvoidance_Listener
{
    const AddonNameSpace = 'SV_DeadlockAvoidance_';

    public static function load_class($class, array &$extend)
    {
        $extend[] = self::AddonNameSpace.$class;
    }
}
