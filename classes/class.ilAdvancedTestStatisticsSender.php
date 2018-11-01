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

    /**
     * @param $course_id
     * @param xatsTriggers $trigger
     * @param array $trigger_values
     * @return Exception
     */
	public function createNotification($course_id, xatsTriggers $trigger, $trigger_values){
		$sender = new srNotificationInternalMailSender(new ilObjUser(6), new ilObjUser($trigger->getUserId()));
        $test = new ilObjTest($trigger->getRefId(),true);

		$placeholders = array(
		    'course' => new ilObjCourse($course_id,true),
            'test' => $test,
            'test_url' => ILIAS_HTTP_PATH . '/goto.php?target=tst_' . $trigger->getRefId() . '&client_id=' . CLIENT_ID,
            'trigger' => $trigger,
            'trigger_values' => $trigger_values
        );

		try {
			$notification = srNotification::getInstanceByName(self::NOTIFICATIONNAME);
			$notification->send($sender,$placeholders);
		}

		catch (Exception $e){
			return $e;
		}
	}


}