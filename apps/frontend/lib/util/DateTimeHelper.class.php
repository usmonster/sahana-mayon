<?php

class DateTimeHelper extends DateTime
{

    /**
     * DateTimeHelper this class is used to convert a time in seconds
     * and return a parsed array of Days, Hours, Minutes, and Seconds.
     * This wrapper is intended to simplify some of the use
     * of date-time methods supplied by the DateTime class
     *
     * LICENSE: This source file is subject to LGPLv3.0 license
     * that is available through the world-wide-web at the following URI:
     * http://www.gnu.org/copyleft/lesser.html
     *
     * @author     Antonio Estrada, CUNY SPS
     * @todo       Finish extending parent class functionality.  Make it usable for milliseconds and abstract.
     *
     *
     * Copyright of the Sahana Software Foundation, sahanafoundation.org
     */

    // STATIC CLASS MEMBERS
    // ====================

    // Conversion Constants
    const MSEC_IN_DAY = 86400000;
    const SEC_IN_DAY = 86400;

    const MSEC_IN_HOUR = 3600000;
    const SEC_IN_HOUR = 3600;

    const MSEC_IN_MINUTE = 60000;
    const SEC_IN_MINUTE = 60;

    const MSEC_IN_SEC = 1000;
   

    // INTERVAL FORMATS
    //const TESTFORMAT = "Y%:D%"

    // Declare static (class) variables to be initialized below
    public static $days = null;
    public static $hours = null;
    public static $minutes = null;
    public static $seconds = null;
    public static $milliseconds = null;

    public static $dateTimeHelperArray = array (

        "days"          =>      null,
        "hours"         =>      null,
        "minutes"       =>      null,
        "seconds"       =>      null,
        "milliseconds"  =>      null

    );
    
    public static function secondsToParsedTimeArray($s)
    {
        DateTimeHelper::$days = (int) ($s / DateTimeHelper::SEC_IN_DAY);
        $hours_remaining = $s % DateTimeHelper::SEC_IN_DAY;

        DateTimeHelper::$hours = (int) ($hours_remaining / DateTimeHelper::SEC_IN_HOUR);
        $minutes_remaining = $hours_remaining % DateTimeHelper::SEC_IN_HOUR;

        DateTimeHelper::$minutes = (int) ($minutes_remaining / DateTimeHelper::SEC_IN_MINUTE);
        $seconds_remaining = $minutes_remaining % DateTimeHelper::SEC_IN_MINUTE;

        DateTimeHelper::$seconds = $seconds_remaining;


        // Build Interval String
        DateTimeHelper::$dateTimeHelperArray['days']     =      DateTimeHelper::$days;
        DateTimeHelper::$dateTimeHelperArray['hours']    =      DateTimeHelper::$hours;
        DateTimeHelper::$dateTimeHelperArray['minutes']  =      DateTimeHelper::$minutes;
        DateTimeHelper::$dateTimeHelperArray['seconds']  =      DateTimeHelper::$seconds;

        return DateTimeHelper::$dateTimeHelperArray;
    }


    // function: initializeDateTimeHelperClassVariables
    //
    // Works like a static constructor if such were supported in PHP.
    // Initializes the Constants to be called in a static context outside
    // the class, just before Class Definiation.

    public static function initializeDateTimeHelperClassArray($s)
    {
        $dateTimeHelperObject = new DateTimeHelper();
        DateTimeHelper::$dateTimeHelperArray = $dateTimeHelperObject->secondsToLegibleIntervalStr($s);
    }

    // INSTANCE MEMBERS
    // ================

    private     $days_ivar       =           null;
    private     $hours_ivar      =           null;
    private     $mintues_ivar    =           null;
    private     $seconds_ivar    =           null;

    private     $dateTimeHelpArray  =   array(

        
        "days"          =>      null,
        "hours"         =>      null,
        "minutes"       =>      null,
        "seconds"       =>      null,
        "milliseconds"  =>      null



    );

    
    /**
     *
     * @method Calls DateTime::__construct($time, $object)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function secondsToParsedTimeArr($s)
    {
        $this->days_ivar = (int) ($s / DateTimeHelper::SEC_IN_DAY);
        $hours_remaining = $s % DateTimeHelper::SEC_IN_DAY;

        $this->hours_ivar = (int) ($hours_remaining / DateTimeHelper::SEC_IN_HOUR);
        $minutes_remaining = $hours_remaining % DateTimeHelper::SEC_IN_HOUR;

        $this->mintues_ivar = (int) ($minutes_remaining / DateTimeHelper::SEC_IN_MINUTE);
        $seconds_remaining = $minutes_remaining % DateTimeHelper::SEC_IN_MINUTE;

        $this->seconds_ivar = $seconds_remaining;


        // Build Interval String
        $this->dateTimeHelpArray['days']        =       $this->days_ivar;
        $this->dateTimeHelpArray['hours']       =       $this->hours_ivar;
        $this->dateTimeHelpArray['minutes']     =       $this->mintues_ivar;
        $this->dateTimeHelpArray['seconds']     =       $this->seconds_ivar;

        return $this->dateTimeHelpArray;
    }


}

?>