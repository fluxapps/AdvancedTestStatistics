<?php
require_once './Modules/Course/classes/class.ilObjCourse.php';
require_once './Modules/Test/classes/class.ilObjTest.php';
/**
 * Class ilAdvancedTestStatisticsSender
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilAdvancedTestStatisticsSender {

	Const NOTIFICATIONNAME = 'statisticsNotification';


	public function createNotification($course_id, xatsTriggers $trigger){

		$sender = new srNotificationInternalMailSender(new ilObjUser(6), new ilObjUser($trigger->getUserId()));

		$placeholders = array('course' => new ilObjCourse($course_id,true), 'test' => new ilObjTest($trigger->getRefId(),true), 'trigger' => $trigger);

		try {
			$notification = srNotification::getInstanceByName(self::NOTIFICATIONNAME);
			$notification->send($sender,$placeholders);
		}

		catch (Exception $e){
			return $e;
		}
	}


}