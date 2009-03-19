<?php
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__).'/../lib/user_data_manager.class.php';
class ReportingUser {

    function ReportingUser() {
    }

    public static function greaterDate($start_date,$end_date)
    {
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        if ($start-$end > 0)
        return 1;
        else
        return 0;
    }

    public static function getActiveInactive($params)
    {
        $udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $active[Translation :: get('Active')] = 0;
        $active[Translation :: get('Inactive')] = 0;
        while($user = $users->next_result())
        {
            if($user->get_active())
            {
                $active[Translation :: get('Active')]++;
            }
            else
            {
                $active[Translation :: get('Inactive')]++;
            }
        }
        return self :: getSerieArray($active);
    }//getActiveInactive

    public static function getNoOfUsers()
    {
        $udm = UserDataManager :: get_instance();

        $arr = array(Translation :: get('NumberOfUsers')=>$udm->count_users());

        return self :: getSerieArray($arr);
    }

    public static function getNoOfLogins()
    {
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $arr = array(Translation :: get('Logins')=>sizeof($trackerdata));

        return self :: getSerieArray($arr);
    }

    public static function getSerieArray($arr)
    {
        $array = array();
        $i = 0;
        foreach($arr as $key => $value)
        {
            $data[$i]["Name"] = $key;
            $data[$i]["Serie1"] = $value;
            $i++;
        }
        $datadescription["Position"] = "Name";
        $datadescription["Values"][] = "Serie1";
        array_push($array, $data);
        array_push($array,$datadescription);
        return $array;
    }

    public static function getDateArray($data,$format)
    {
        $arr = array();
        foreach($data as $key => $value)
        {
            $bla =  explode('-',$value->get_date());
            $bla2 = explode(' ',$bla[2]);
            $hoursarray = explode(':',$bla2[1]);
            $date = date($format,mktime($hoursarray[0],$hoursarray[1],$hoursarray[2],$bla[1],$bla2[0],$bla[0]));
            $date = (is_numeric($date))?$date:Translation :: get($date);
            if (array_key_exists($date, $arr))
            {
                $arr[$date]++;
            }else
            {
                $arr[$date] = 1;
            }
        }
        return $arr;
    }

    public static function getNoOfLoginsMonth()
    {
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $months = self :: getDateArray($trackerdata,'F');

        return self :: getSerieArray($months);
    }

    public static function getNoOfLoginsDay()
    {
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $days = self :: getDateArray($trackerdata,'l');

        return self :: getSerieArray($days);
    }

    public static function getNoOfLoginsHour()
    {
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $hours = self :: getDateArray($trackerdata,'G');

        return self :: getSerieArray($hours);
    }

    public static function getNoOfUsersPicture()
    {
        $udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $picturetext = Translation :: get('Picture');
        $nopicturetext = Translation :: get('NoPicture');
        $picture[$picturetext] = 0;
        $picture[$nopicturetext] = 0;
        while($user = $users->next_result())
        {
            if($user->get_picture_uri())
            {
                $picture[$picturetext]++;
            }
            else
            {
                $picture[$nopicturetext]++;
            }
        }
        return self :: getSerieArray($picture);
    }

    public static function getNoOfUsersSubscribedCourse()
    {
        require_once Path :: get_application_path().'lib/weblcms/weblcms_data_manager.class.php';
        $udm = UserDataManager :: get_instance();
        $users = $udm->count_users();

        $wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->count_user_courses();

        $arr[Translation :: get('UserSubscribedToCourse')] = $courses;
        $arr[Translation :: get('UserNotSubscribedToCourse')] = $users-$courses;

        return self :: getSerieArray($arr);
    }

    public static function getUserInformation($params)
    {
        $uid = $params[ReportingManager :: PARAM_USER_ID];
        //$uid = 2;
        require_once Path :: get_admin_path().'/trackers/online_tracker.class.php';
        $udm = UserDataManager :: get_instance();
        $tracking = new OnlineTracker();

        $items = $tracking->retrieve_tracker_items();
        foreach($items as $item)
        {
            if($item->get_user_id()==$uid)
            {
                $online = 1;
            }
        }

        $user = $udm->retrieve_user($uid);

        $arr[Translation :: get('Name')] = $user->get_fullname();
        $arr[Translation :: get('Email')] = $user->get_email();
        $arr[Translation :: get('Phone')] = $user->get_phone();
        //$arr[Translation :: get('Status')] = $user->get_status_name();
        $arr[Translation :: get('Online')] = ($online)?Translation :: get('Online'):Translation :: get('Offline');

        return self :: getSerieArray($arr);
    }

    public static function getUserPlatformStatistics($params)
    {
        $uid = $params[ReportingManager :: PARAM_USER_ID];
        //$uid = 2;
        require_once(dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $conditions[] = new EqualityCondition(LoginLogoutTracker::PROPERTY_USER_ID,$uid);
        $conditions[] = new EqualityCondition(LoginLogoutTracker::PROPERTY_TYPE,'login');
        $condition = new AndCondition($conditions);
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        //dump($condition);
        foreach($trackerdata as $key => $value)
        {
            if(!$firstconnection)
            {
                //$firstconnection = $value->get_date();
                $firstconnection = $value->get_date();
                $lastconnection = $value->get_date();
            }
            if(!self :: greaterDate($value->get_date(), $firstconnection))
            {
                $firstconnection = $value->get_date();
            }else if(self :: greaterDate($value->get_date(),$lastconnection))
            {
                $lastconnection = $value->get_date();
            }
        }
        echo $firstconnection, $lastconnection;

        $arr[Translation :: get('FirstConnection')] = $firstconnection;
        $arr[Translation :: get('LastConnection')] = $lastconnection;
        $arr[Translation :: get('TimeOnPlatform')] = '00:00:00';

        return self :: getSerieArray($arr);
    }

    public static function getBrowsers()
    {
        require_once(dirname(__FILE__) . '/../trackers/browsers_tracker.class.php');
        $tracker = new BrowsersTracker();
        $condition = new EqualityCondition(BrowsersTracker::PROPERTY_TYPE,'browser');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Browsers'));
    }

    public static function getCountries()
    {
        require_once(dirname(__FILE__) . '/../trackers/countries_tracker.class.php');
        $tracker = new CountriesTracker();
        $condition = new EqualityCondition(CountriesTracker::PROPERTY_TYPE,'country');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Countries'));
    }

    public static function getOs()
    {
        require_once(dirname(__FILE__) . '/../trackers/os_tracker.class.php');
        $tracker = new OSTracker();
        $condition = new EqualityCondition(OSTracker :: PROPERTY_TYPE,'os');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Os'));
    }

    public static function getProviders()
    {
        require_once(dirname(__FILE__) . '/../trackers/providers_tracker.class.php');
        $tracker = new ProvidersTracker();
        $condition = new EqualityCondition(ProvidersTracker :: PROPERTY_TYPE,'provider');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Providers'));
    }

    public static function getReferers()
    {
        require_once(dirname(__FILE__) . '/../trackers/referrers_tracker.class.php');
        $tracker = new ReferrersTracker();
        $condition = new EqualityCondition(ReferrersTracker :: PROPERTY_TYPE,'referer');

        return Reporting :: array_from_tracker($tracker,$condition,Translation :: get('Referers'));
    }
}
?>