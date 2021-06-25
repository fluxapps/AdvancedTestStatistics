<?php

use srag\Notifications4Plugin\Notifications4Plugins\Utils\Notifications4PluginTrait;

require_once './Modules/Course/classes/class.ilObjCourse.php';
require_once './Modules/Test/classes/class.ilObjTest.php';

/**
 * Class ilAdvancedTestStatisticsSender
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilAdvancedTestStatisticsSender
{

    use Notifications4PluginTrait;

    const NOTIFICATIONNAME = 'statisticsNotification';

    /**
     * @param              $course_id
     * @param xatsTriggers $trigger
     * @param array        $trigger_values
     * @throws ilException
     */
    public function createNotification($course_id, xatsTriggers $trigger, $trigger_values)
    {
        if (!$trigger->getUserId()) {
            throw new ilException('no recipient user id given');
        }
        $sender = self::sender()->factory()->internalMail(new ilObjUser(6), new ilObjUser($trigger->getUserId()));
        $test = new ilObjTest($trigger->getRefId(), true);

        $placeholders = array(
            'course' => new ilObjCourse($course_id, true),
            'test' => $test,
            'test_url' => ILIAS_HTTP_PATH . '/goto.php?target=tst_' . $trigger->getRefId() . '&client_id=' . CLIENT_ID,
            'trigger' => $trigger,
            'trigger_values' => $trigger_values
        );

        $notification = self::notification(
            'srag\Plugins\Notifications4Plugins\Notification\Notification',
            'srag\Plugins\Notifications4Plugins\Notification\Language\NotificationLanguage'
        )->getNotificationByName(self::NOTIFICATIONNAME);
        self::sender()->send($sender, $notification, $placeholders);
    }

}