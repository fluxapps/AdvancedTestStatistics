<?php

/**
 * Class ilAdvancedTestStatisticsAlertFormGUI
 *
 * @ilCtrl_Calls    ilAdvancedTestStatisticsAlertFormGUI: ilAdvancedTestStatisticsPlugin
 */
class ilAdvancedTestStatisticsAlertFormGUI extends ilPropertyFormGUI {

	/**
	 * @var ilAdvancedTestStatisticsPlugin
	 */
	protected $pl;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilAdvancedTestStatisticsSettingsGUI
	 */
	protected $parent_gui;
	/**
	 * @var xatsFilter
	 */
	protected $object;
	protected $operators = array( '>', '<', '>=', '<=', '!=', '==' );
	protected $extendedFields;



	protected $interval_options = array('daily','weekly','monthly');


	/**
	 * ilAdvancedTestStatisticsAlertFormGUI constructor.
	 *
	 * @param            $parent_gui
	 */
	public function __construct($parent_gui, xatsTriggers $triggers) {
		global $ilCtrl, $tpl;

		$this->object = $triggers;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ref_id = $_GET['ref_id'];
		$this->is_new = ($this->object->getId() != NULL);
		$this->parent_gui = $parent_gui;
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));

        $this->extendedFields = array(
            "avg_points_finished" => $this->pl->txt("avg_points_finished"),
            "avg_result_passed" => $this->pl->txt("avg_result_passed"),
            "avg_result_finished" => $this->pl->txt("avg_result_finished"),
            "avg_result_finished_run_one" => $this->pl->txt("avg_result_finished_run_one"),
            "avg_result_passed_run_one" => $this->pl->txt("avg_result_passed_run_one"),
            "avg_result_passed_run_two"  => $this->pl->txt("avg_result_passed_run_two"),
            "avg_result_finished_run_two" => $this->pl->txt("avg_result_finished_run_two"),
            'nr_participants_started' => $this->pl->txt('nr_participants_started'),
            'nr_tests_finished' => $this->pl->txt('nr_tests_finished'),
            'avg_test_time' => $this->pl->txt('avg_test_time_in_s'),
            'nr_tests_passed' => $this->pl->txt('nr_tests_passed'),
            'avg_points_passed' => $this->pl->txt('avg_points_passed'),
            'avg_passed_test_time' => $this->pl->txt('avg_passed_test_time_in_s'),
            'qst_percentage' => $this->pl->txt('qst_percentage')
        );

		parent::__construct();

		$this->initForm();
	}


    /**
     *
     */
    public function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		if ($trigger_id = $this->object->getId()) {
		    $hidden = new ilHiddenInputGUI('trigger_id');
		    $this->addItem($hidden);
        }

		$te = new ilSelectInputGUI($this->pl->txt('form_trigger'), 'trigger');
		$te->setOptions($this->extendedFields);
		$te->setInfo('Trigger compared to value');
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilSelectInputGUI($this->pl->txt('form_operator'), 'operator');
		$te->setOptions($this->operators);
		$te->setInfo('Operator for comparison');
		$te->setRequired(false);
		$this->addItem($te);

		$te = new ilNumberInputGUI($this->pl->txt('form_value'), 'value');
		$te->setRequired(true);
		$te->setInfo('Value to be compared with trigger');
		$te->allowDecimals(true);
		$this->addItem($te);

		$user = new ilTextInputGUI($this->pl->txt('form_user'), 'user');
		$user->setDataSource($this->ctrl->getLinkTargetByClass(array(
			ilUIPluginRouterGUI::class,
			ilAdvancedTestStatisticsPlugin::class
		), ilAdvancedTestStatisticsPlugin::CMD_ADD_USER_AUTO_COMPLETE, "", true));
		$user->setInfo('User which will receive the notification');
        $user->setRequired(true);
        $this->addItem($user);


		$te = new ilNumberInputGUI($this->pl->txt('form_user_completed'),'user_completed');
		$te->setRequired(true);
		$te->setInfo('The trigger is only checked if this number of users have completed the test / question');
		$this->addItem($te);

		$te = new ilDateTimeInputGUI($this->pl->txt('form_date'),'date');
		$te->setRequired(true);
		$te->setInfo('The date when the trigger is checked for the first time');
		$this->addItem($te);

		$te = new ilSelectInputGUI($this->pl->txt('form_interval'),'interval');
		$te->setRequired(true);
		$te->setInfo('The interval within the trigger is checked');
		$te->setOptions($this->interval_options);
		$this->addItem($te);
	}


    /**
     *
     */
    public function initButtons() {
		if (!$this->is_new) {
			$this->setTitle($this->pl->txt('form_headtitle_new'));
			$this->addCommandButton(ilAdvancedTestStatisticsSettingsGUI::CMD_CREATE_TRIGGER, $this->pl->txt('form_create'));
		} else {
			$this->setTitle($this->pl->txt('form_headtitle_old'));
			$this->addCommandButton(ilAdvancedTestStatisticsSettingsGUI::CMD_UPDATE_TRIGGER, $this->pl->txt('form_update'));
		}
		$this->addCommandButton(ilAdvancedTestStatisticsSettingsGUI::CMD_CANCEL, $this->pl->txt('form_cancel'));
	}


    /**
     * @return bool
     */
    public function save() {
		if (!$this->fill()) {
			return false;
		}

		$this->object->setRefId($_GET['ref_id']);

		$this->object->store();

		return true;
	}


    /**
     * @return bool
     */
    public function fill() {
		if (!$this->checkInput()) {
			return false;
		}

		$this->object->setTriggerName($this->getInput('trigger'));
		$this->object->setOperator($this->getInput('operator'));
		$this->object->setValue($this->getInput('value'));
		$this->object->setUserId(ilObjUser::_lookupId($this->getInput('user')));
		$this->object->setUserThreshold($this->getInput('user_completed'));
		$date = $this->getInput('date');
		$timestamp = strtotime((ILIAS_VERSION_NUMERIC >= "5.3" ? $date : $date['date']));
		$this->object->setDatesender($timestamp);
		$this->object->setIntervalls($this->getInput('interval'));


		return true;
	}

    /**
     *
     */
    public function fillForm(){
        $date = date('Y-m-d', $this->object->getDatesender());
		$array = array(
		    'trigger_id' => $this->object->getId(),
		    'trigger' => $this->object->getTriggerName(),
            'operator' => $this->object->getOperator(),
            'value' => $this->object->getValue(),
            'user' => ilObjUser::_lookupLogin($this->object->getUserId()),
            'user_completed' => $this->object->getUserThreshold(),
            'date' => (ILIAS_VERSION_NUMERIC >= "5.3" ? $date : ["date" => $date]) ,
            'interval' => $this->object->getIntervalls()
        );
		$this->setValuesByArray($array);
	}
}