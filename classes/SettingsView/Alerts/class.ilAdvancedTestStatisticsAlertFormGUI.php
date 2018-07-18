<?php

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
	protected $extendedFields = array(
		"avg_points_finished" => "Average Points finished tests",
		"avg_result_passed" => "Average result passed tests",
		"avg_result_finished" => "Average result(%) finished tests",
		"avg_result_finished_run_one" => "Average result(%) passed tests (Run 1)",
		"avg_result_passed_run_one" => "Average result(%) finished tests (Run 1)",
		"avg_result_passed_run_two" => "Average result(%) passed tests (Run 2)",
		"avg_result_finished_run_two" => "Average result(%) finished tests (Run 2)",
		'Total number of participants who started the test',
		'Total finished tests (Participants that used up all possible passes)',
		'Average test processing time',
		'Total passed tests',
		'Average points of passed tests',
		'Average processing time of all passed tests',
	);



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


		$test = new ilObjTest($this->ref_id);
		$questions = $test->getAllQuestions();

		foreach ($questions as $question) {
			$this->extendedFields[$question['question_id']] = $question['title'];
		}



		parent::__construct();

		$this->initForm();
	}


	public function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		$te = new ilSelectInputGUI($this->pl->txt('form_trigger'), 'trigger');
		$te->setOptions($this->extendedFields);
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilSelectInputGUI($this->pl->txt('form_operator'), 'operator');
		$te->setOptions($this->operators);
		$te->setRequired(false);
		$this->addItem($te);

		$te = new ilNumberInputGUI($this->pl->txt('form_value'), 'value');
		$te->setRequired(true);
		$te->allowDecimals(true);
		$this->addItem($te);

		$te = new ilTextInputGUI($this->pl->txt('form_user'),'user');
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilNumberInputGUI($this->pl->txt('form_user_completed'),'user_completed');
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilDateTimeInputGUI($this->pl->txt('form_date'),'date');
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilSelectInputGUI($this->pl->txt('form_interval'),'interval');
		$te->setRequired(true);
		$te->setOptions($this->interval_options);
		$this->addItem($te);






	}


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



	public function save() {
		if (!$this->fill()) {
			return false;
		}

		$this->object->setRefId($_GET['ref_id']);

		if(!xatsTriggers::where(array('id' => $this->object->getId()))->hasSets()){
			$this->object->create();
		}
		else {
			$this->object->update();
		}

		return true;
	}


	public function fill() {
		if (!$this->checkInput()) {
			return false;
		}

		$this->object->setTriggerName($this->getInput('trigger'));
		$this->object->setOperator($this->getInput('operator'));
		$this->object->setValue($this->getInput('value'));
		$this->object->setUserId($this->getInput('user'));
		$this->object->setUserPercentage($this->getInput('user_completed'));
		$date = $this->getInput('date');
		$timestamp = strtotime($date['date']);
		$this->object->setDatesender($timestamp);
		$this->object->setIntervalls($this->getInput('interval'));


		return true;
	}

	public function fillForm(){
		$array = array('trigger' => $this->object->getTriggerName(), 'operator' => $this->object->getOperator(),'value' => $this->object->getValue());
		$this->setValuesByArray($array);
	}
}