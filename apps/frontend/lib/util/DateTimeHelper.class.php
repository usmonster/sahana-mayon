<?php

DateTimeHelper::initializeDateTimeHelperClassVariables();

class DateTimeHelper extends DateTime
{

 /**
 * DateTimeHelper this class is used to convert a time in milliseconds
 * and return a human readable pretty string of Days, Hours, Minutes, Seconds.
 * This wrapper is intended to simplify some of the use
 * of date-time methods supplied by the DateTime class
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Antonio Estrada, CUNY SPS
 * @todo       Finish Class (accessor methods, and any other useful features)
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

// STATIC CLASS MEMBERS
    // ====================
    
    // Format string variants to constants declared in DateTime class
    const ATOM_P = "Y-m-d H:i:s P";
    const COOKIE_T = "l, d-M-y H:i:s T";
    const ISO8601_O = "Y-m-d H:i:s O";
    const RFC822_O = "D, d M y H:i:s O";
    const RFC850_T = "l, d-M-y H:i:s T";
    const RFC1036_O = "D, d M y H:i:s O";
    const RFC1123_O = "D, d M Y H:i:s O";
    const RFC2822_O = "D, d M Y H:i:s O";
    const RFC3339_P = "Y-m-d H:i:s P";
    const RSS_O = "D, d M Y H:i:s O";
    const W3C_P = "Y-m-d H:i:s P";

    // Format strings unique to DateTimeHelper class
    const YMD_HMS_CIV_4YR_A = "Y-m-d h:i:s A";
    const YMD_HMS_CIV_4YR_L = "Y-m-d h:i:s a";
    const YMD_HMS_MIL_4YR = "Y-m-d H:i:s";
    const YMD_HMS_CIV_2YR_A = "y-m-d h:i:s A";
    const YMD_HMS_CIV_2YR_L = "y-m-d h:i:s a";
    const YMD_HMS_MIL_2YR = "y-m-d H:i:s";
    const HMS_CIV_A = "h:i:s A";
    const HMS_CIV_L = "h:i:s a";
    const HMS_MIL = "H:i:s";
    const HMS_CIV = "h:i:s";
    const HM_CIV_A = "h:i A";
    const HM_CIV_L = "h:i a";
    const HM_MIL = "H:i";
    const HM_CIV = "h:i";

    // Declare static (class) variables to be initialized below
    public static $atom_P = null;
    public static $cookie_T = null;
    public static $iso8601_O = null;
    public static $rfc822_O = null;
    public static $rfc850_t = null;
    public static $rfc1036_O = null;
    public static $rfc1123_O = null;
    public static $rfc2822_O = null;
    public static $rfc3339_P = null;
    public static $rss_O = null;
    public static $w3c_P = null;
    public static $ymd_hms_civ_4yr_A = null;
    public static $ymd_hms_civ_4yr_L = null;
    public static $ymd_hms_mil_4yr = null;
    public static $ymd_hms_civ_2yr_A = null;
    public static $ymd_hms_civ_2yr_L = null;
    public static $ymd_hms_mil_2yr = null;
    public static $hms_civ_A = null;
    public static $hms_civ_L = null;
    public static $hms_mil = null;
    public static $hms_civ = null;
    public static $hm_civ_A = null;
    public static $hm_civ_L = null;
    public static $hm_mil = null;
    public static $hm_civ = null;

    // function: initializeDateTimeHelperClassVariables
    //
    // Works like a static constructor if such were supported in PHP.
    // Initializes the Constants to be called in a static context outside
    // the class, just before Class Definiation.
    public static function initializeDateTimeHelperClassVariables()
    {
        $dateTimeHelperObject = new DateTimeHelper();
        
        self::$atom_P = $dateTimeHelperObject->format(DateTimeHelper::ATOM_P);
        self::$cookie_T = $dateTimeHelperObject->format(DateTimeHelper::COOKIE_T);
        self::$hm_civ = $dateTimeHelperObject->format(DateTimeHelper::HM_CIV);
        self::$hm_civ_A = $dateTimeHelperObject->format(DateTimeHelper::HM_CIV_A);
        self::$hm_civ_L = $dateTimeHelperObject->format(DateTimeHelper::HM_CIV_L);
        self::$hm_mil = $dateTimeHelperObject->format(DateTimeHelper::HM_MIL);
        self::$hms_civ = $dateTimeHelperObject->format(DateTimeHelper::HMS_CIV);
        self::$hms_civ_A = $dateTimeHelperObject->format(DateTimeHelper::HMS_CIV_A);
        self::$hms_civ_L = $dateTimeHelperObject->format(DateTimeHelper::HMS_CIV_L);
        self::$hms_mil = $dateTimeHelperObject->format(DateTimeHelper::HMS_MIL);
        self::$iso8601_O = $dateTimeHelperObject->format(DateTimeHelper::ISO8601_O);
        self::$rfc1036_O = $dateTimeHelperObject->format(DateTimeHelper::RFC1036_O);
        self::$rfc1123_O = $dateTimeHelperObject->format(DateTimeHelper::RFC1123_O);
        self::$rfc2822_O = $dateTimeHelperObject->format(DateTimeHelper::RFC2822_O);
        self::$rfc3339_P = $dateTimeHelperObject->format(DateTimeHelper::RFC3339_P);
        self::$rfc822_O = $dateTimeHelperObject->format(DateTimeHelper::RFC822_O);
        self::$rfc850_t = $dateTimeHelperObject->format(DateTimeHelper::RFC850_T);
        self::$rss_O = $dateTimeHelperObject->format(DateTimeHelper::RSS_O);
        self::$w3c_P = $dateTimeHelperObject->format(DateTimeHelper::W3C_P);
        self::$ymd_hms_civ_2yr_A = $dateTimeHelperObject->format(DateTimeHelper::YMD_HMS_CIV_2YR_A);
        self::$ymd_hms_civ_2yr_L = $dateTimeHelperObject->format(DateTimeHelper::YMD_HMS_CIV_2YR_L);
        self::$ymd_hms_civ_4yr_A = $dateTimeHelperObject->format(DateTimeHelper::YMD_HMS_CIV_4YR_A);
        self::$ymd_hms_civ_4yr_L = $dateTimeHelperObject->format(DateTimeHelper::YMD_HMS_CIV_4YR_L);
        self::$ymd_hms_mil_2yr = $dateTimeHelperObject->format(DateTimeHelper::YMD_HMS_MIL_2YR);
        self::$ymd_hms_mil_4yr = $dateTimeHelperObject->format(DateTimeHelper::YMD_HMS_MIL_4YR);
    }


    public static function convertMillisecondTimeToSeconds($milliseconds)
    {

        $conversionArray = array(
                                    "seconds"   =>  null,
                                    "remainder" =>  null
                                );

        $conversionArray['seconds']         =   (int) ($milliseconds / 1000);
        $conversionArray['milliseconds']    =   $milliseconds % 1000;

        return $conversionArray;

    }




    // INSTANCE MEMBERS
    // ================

    // Declare Private Data Fields (Instance Variables)
    private $atom_P_ivar = null;
    private $cookie_T_ivar= null;
    private $iso8601_O_ivar = null;
    private $rfc822_O_ivar = null;
    private $rfc850_t_ivar = null;
    private $rfc1036_O_ivar = null;
    private $rfc1123_O_ivar = null;
    private $rfc2822_O_ivar = null;
    private $rfc3339_P_ivar = null;
    private $rss_O_ivar = null;
    private $w3c_P_ivar = null;
    private $ymd_hms_civ_4yr_A_ivar = null;
    private $ymd_hms_civ_4yr_L_ivar = null;
    private $ymd_hms_mil_4yr_ivar = null;
    private $ymd_hms_civ_2yr_A_ivar = null;
    private $ymd_hms_civ_2yr_L_ivar = null;
    private $ymd_hms_mil_2yr_ivar = null;
    private $hms_civ_A_ivar = null;
    private $hms_civ_L_ivar = null;
    private $hms_mil_ivar = null;
    private $hms_civ_ivar = null;
    private $hm_civ_A_ivar = null;
    private $hm_civ_L_ivar = null;
    private $hm_mil_ivar = null;
    private $hm_civ_ivar = null;

    /**
     *
     * @method Calls DateTime::__construct($time, $object)
     *
     */
    public function __construct()
    {
        parent::__construct();

        // Initializes (Instance Variables) to current time
        $this->atom_P_ivar = $this->format(DateTimeHelper::ATOM_P);
        $this->cookie_T_ivar= $this->format(DateTimeHelper::COOKIE_T);
        $this->iso8601_O_ivar = $this->format(DateTimeHelper::ISO8601_O);
        $this->rfc822_O_ivar = $this->format(DateTimeHelper::RFC822_O);
        $this->rfc850_t_ivar = $this->format(DateTimeHelper::RFC850_T);
        $this->rfc1036_O_ivar = $this->format(DateTimeHelper::RFC1036_O);
        $this->rfc1123_O_ivar = $this->format(DateTimeHelper::RFC1123_O);
        $this->rfc2822_O_ivar = $this->format(DateTimeHelper::RFC2822_O);
        $this->rfc3339_P_ivar = $this->format(DateTimeHelper::RFC3339_P);
        $this->rss_O_ivar = $this->format(DateTimeHelper::RSS_O);
        $this->w3c_P_ivar = $this->format(DateTimeHelper::W3C_P);
        $this->ymd_hms_civ_4yr_A_ivar = $this->format(DateTimeHelper::YMD_HMS_CIV_4YR_A);
        $this->ymd_hms_civ_4yr_L_ivar = $this->format(DateTimeHelper::YMD_HMS_CIV_4YR_L);
        $this->ymd_hms_mil_4yr_ivar = $this->format(DateTimeHelper::YMD_HMS_MIL_4YR);
        $this->ymd_hms_civ_2yr_A_ivar = $this->format(DateTimeHelper::YMD_HMS_CIV_2YR_A);
        $this->ymd_hms_civ_2yr_L_ivar = $this->format(DateTimeHelper::YMD_HMS_CIV_2YR_L);
        $this->ymd_hms_mil_2yr_ivar = $this->format(DateTimeHelper::YMD_HMS_MIL_2YR);
        $this->hms_civ_A_ivar = $this->format(DateTimeHelper::HMS_CIV_A);
        $this->hms_civ_L_ivar = $this->format(DateTimeHelper::HMS_CIV_L);
        $this->hms_mil_ivar = $this->format(DateTimeHelper::HMS_MIL);
        $this->hms_civ_ivar = $this->format(DateTimeHelper::HMS_CIV);
        $this->hm_civ_A_ivar = $this->format(DateTimeHelper::HM_CIV_A);
        $this->hm_civ_L_ivar = $this->format(DateTimeHelper::HM_CIV_L);
        $this->hm_mil_ivar = $this->format(DateTimeHelper::HM_MIL);
        $this->hm_civ_ivar = $this->format(DateTimeHelper::HM_CIV);
        

    }

    public function get_atom_P_ivar()
    {
        return $this->atom_P_ivar;
    }

    public function get_cookie_T_ivar()
    {
        return $this->cookie_T_ivar;
    }
    
    public function get_hm_civ_A_ivar()
    {
        $this->hm_civ_A_ivar;
    }

    public function millisecondsToSeconds($milliseconds)
    {
        $conversionArray = array(
                                    "seconds"   =>  null,
                                    "remainder" =>  null
                                );

        $conversionArray['seconds']         =   (int) ($milliseconds / 1000);
        $conversionArray['milliseconds']    =   $milliseconds % 1000;

        return $conversionArray;
    }



}

?>