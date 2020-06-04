<?php

require_once __DIR__ . '/../vendor/autoload.php';
/**
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 * @version $Id$
 *
 * @ilCtrl_IsCalledBy ilAdvancedTestStatisticsPlugin: ilUIPluginRouterGUI
 */
class ilAdvancedTestStatisticsPlugin extends ilUserInterfaceHookPlugin
{
	const CMD_ADD_USER_AUTO_COMPLETE = 'addUserAutoComplete';
	/**
	 * @var ilAdvancedTestStatisticsPlugin
	 */
	protected static $instance;

    /**
     * @return string
     */
    function getPluginName()
	{
		return "AdvancedTestStatistics";
	}

    /**
     * @return ilAdvancedTestStatisticsPlugin
     */
    public static function getInstance(){

		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}


    /**
     *
     */
    public function executeCommand() {
		global $ilCtrl;
		$cmd = $ilCtrl->getCmd();
		switch ($cmd) {
			default:
				$this->{$cmd}();
				break;
		}
	}

    /**
     * @param string $component
     * @param string $event
     * @param array $parameters
     */
    public function handleEvent($component, $event, $parameters) {
        switch ($component) {
            case 'Services/Object':
                switch ($event) {
                    case 'cloneObject':
                        if (!($parameters['object'] instanceof ilObjTest)) {
                            return;
                        }
                        /**
                         * @var $new_obj      ilObjTest
                         * @var $original_obj ilObjTest
                         */
                        $new_obj = $parameters['object'];
                        $original_obj = $parameters['cloned_from_object'];
                        /** @var xatsTriggers $trigger */
                        foreach (xatsTriggers::where(['ref_id' => $original_obj->getRefId()])->get() as $trigger) {
                            $new_trigger = new xatsTriggers();
                            $new_trigger->setValue($trigger->getValue());
                            $new_trigger->setDatesender($trigger->getDatesender());
                            $new_trigger->setIntervalls($trigger->getIntervalls());
                            $new_trigger->setOperator($trigger->getOperator());
                            $new_trigger->setRefId($new_obj->getRefId());
                            $new_trigger->setTriggerName($trigger->getTriggerName());
                            $new_trigger->setUserId($trigger->getUserId());
                            $new_trigger->setUserThreshold($trigger->getUserThreshold());
                            $new_trigger->create();
                        }
                        break;
                }
                break;
        }
    }


	/**
	 * async auto complete method for user filter in overview
	 */
	public function addUserAutoComplete() {
		include_once './Services/User/classes/class.ilUserAutoComplete.php';
		$auto = new ilUserAutoComplete();
		$auto->setSearchFields(array( 'usr_id','login', 'firstname', 'lastname' ));
		$auto->setResultField('login');
		$auto->enableFieldSearchableCheck(false);
		$auto->setMoreLinkAvailable(true);

		if (($_REQUEST['fetchall'])) {
			$auto->setLimit(ilUserAutoComplete::MAX_ENTRIES);
		}

		$list = $auto->getList($_REQUEST['term']);

		echo $list;
		exit();
	}

    /**
     * @return ilObjCourse
     * @throws Exception
     */
    public function getParentCourse($ref_id = 0) {
        $ref_id = $ref_id ? $ref_id : $_GET['ref_id'];
        require_once 'Services/Object/classes/class.ilObjectFactory.php';
        $parent = ilObjectFactory::getInstanceByRefId($this->getParentCourseId($ref_id));

        return $parent;
    }


    /**
     * @param $ref_id
     *
     * @return int
     * @throws Exception
     */
    public function getParentCourseId($ref_id) {
        global $tree;
        while (!in_array(ilObjCourse::_lookupType($ref_id, true), array( 'crs', 'grp' ))) {
            if ($ref_id == 1 || !$ref_id) {
                throw new Exception("Parent of ref id {$ref_id} is neither course nor group.");
            }
            $ref_id = $tree->getParentId($ref_id);
        }

        return $ref_id;
    }

    /**
     * @param $ref_id
     * @return array
     * @throws Exception
     */
    public function getCourseMembers($ref_id) {
        return $this->getParentCourse($ref_id)->getMembersObject()->getMembers();
    }

}

?>