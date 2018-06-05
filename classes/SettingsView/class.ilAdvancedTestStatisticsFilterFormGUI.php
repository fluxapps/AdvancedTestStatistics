<?php

/**
 * Class ilAdvancedTestStatisticsFilterFormGUI
 *
 * @ilCtrl_Calls    ilAdvancedTestStatisticsFilterFormGUI: ilAdvancedTestStatisticsPlugin
 */
class ilAdvancedTestStatisticsFilterFormGUI extends ilPropertyFormGUI {

	/**
	 * @var xatsExtendedStatisticsFields
	 */
	protected $filterFields;
	/**
	 * @var int
	 */
	protected $ref_id;
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
	/**
	 * @var array
	 */
	public $extendedFields = array(
		"avg_points_finished" => "Average Points finished tests",
		"avg_result_passed" => "Average result (%) passed tests",
		"avg_result_finished" => "Average result(%) finished tests",
		"avg_result_finished_run_one" => "Average result(%) passed tests (Run 1)",
		"avg_result_passed_run_one" => "Average result(%) finished tests (Run 1)",
		"avg_result_passed_run_two" => "Average result(%) passed tests (Run 2)",
		"avg_result_finished_run_two" => "Average result(%) finished tests (Run 2)"
	);


	/**
	 * ilAdvancedTestStatisticsFilterFormGUI constructor.
	 *
	 * @param            $parent_gui
	 * @param xatsFilter $filter
	 */
	public function __construct($parent_gui) {
		global $ilCtrl, $tpl;

		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ref_id = $_GET['ref_id'];
		$this->parent_gui = $parent_gui;
		$this->is_new = false;

		$this->ctrl->setParameterByClass(ilAdvancedTestStatisticsSettingsGUI::class, 'ref_id', $this->ref_id);

		$result = xatsFilter::where(array( 'ref_id' => $this->ref_id ))->first();
		if ($result != NULL) {
			$this->is_new = true;
		}

		$this->object = xatsFilter::where(array( 'ref_id' => $this->ref_id ))->first();
		if ($this->object == NULL) {
			$this->object = new xatsFilter();
		}

		$this->filterFields = xatsExtendedStatisticsFields::where(array( 'ref_id' => $this->ref_id ))->first();
		if ($this->filterFields == NULL) {
			$this->filterFields = new xatsExtendedStatisticsFields();
		}

		parent::__construct();

		$this->initForm();
	}


	public function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();

		$c = new ilFormSectionHeaderGUI();
		$c->setTitle("FilterStatistics");
		$this->addItem($c);

		$b = new ilCheckboxInputGUI("Do not count inactive users", "inactive");
		$this->addItem($b);

		//$check_prop = new ilCheckboxInputGUI("Custom Filter", "custom_filter");
		$multiuserselectgui = new ilMultiUserSelectInputGUI("Users", "user");
		//$check_prop->addSubItem($multiuserselectgui);
		//$this->addItem($check_prop);
		$this->addItem($multiuserselectgui);


		$c = new ilFormSectionHeaderGUI();
		$c->setTitle("Extend Statistic Fields");
		$c->setInfo("Make the following fields available in aggregated test results");
		$this->addItem($c);

		foreach ($this->extendedFields as $key => $value) {
			$c = new ilCheckboxInputGUI($value, $key);
			$this->addItem($c);
		}
		$this->fillForm();
	}


	public function initButtons() {
		$this->addCommandButton(ilAdvancedTestStatisticsSettingsGUI::CMD_UPDATE_FILTER, $this->pl->txt('form_update'));
		$this->addCommandButton(ilAdvancedTestStatisticsSettingsGUI::CMD_CANCEL, $this->pl->txt('form_cancel'));
	}


	public function save() {
		global $ilDB;

		if (!$this->fill()) {
			return false;
		}

		if (!xatsFilter::where(array( 'ref_id' => $this->object->getRefId() ))->hasSets()) {
			$this->object->create();
		} else {
			$this->object->update();
		}

		if (!xatsExtendedStatisticsFields::where(array( 'ref_id' => $this->filterFields->getRefId() ))->hasSets()) {
			$this->filterFields->create();
		} else {
			$this->filterFields->update();
		}

		$xatsDeleteUser = xatsFilteredUsers::where(array( 'ref_id' => $this->ref_id ))->get();
		foreach ($xatsDeleteUser as $usr_delete) {
			$usr_delete->delete();
		}

		$users = $this->stripUsers();

		foreach ($users as $user) {
			$xatsUser = new xatsFilteredUsers();
			$xatsUser->setRefId($this->ref_id);
			$xatsUser->setUserId($user);
			$xatsUser->create();
		}

		return true;
	}


	public function fill() {
		if (!$this->checkInput()) {
			return false;
		}

		$this->object->setRefId($this->ref_id);
		$this->object->setFilterInactive($this->getInput('inactive'));

		$this->filterFields->setRefId($this->ref_id);
		$this->filterFields->setAvgPointsFinished($this->getInput('avg_points_finished'));
		$this->filterFields->setAvgResultPassed($this->getInput('avg_result_passed'));
		$this->filterFields->setAvgResultFinished($this->getInput('avg_result_finished'));
		$this->filterFields->setAvgResultFinishedRunOne($this->getInput('avg_result_finished_run_one'));
		$this->filterFields->setAvgResultPassedRunOne($this->getInput('avg_result_passed_run_one'));
		$this->filterFields->setAvgResultPassedRunTwo($this->getInput('avg_result_passed_run_two'));
		$this->filterFields->setAvgResultsFinishedRunTwo($this->getInput('avg_result_finished_run_two'));

		return true;
	}


	public function fillForm() {
		$values['inactive'] = $this->object->isFilterInactive();

		$values['avg_points_finished'] = $this->filterFields->isAvgPointsFinished();
		$values['avg_result_passed'] = $this->filterFields->isAvgResultPassed();
		$values['avg_result_finished'] = $this->filterFields->isAvgResultFinished();
		$values['avg_result_finished_run_one'] = $this->filterFields->isAvgResultFinishedRunOne();
		$values['avg_result_passed_run_one'] = $this->filterFields->isAvgResultPassedRunOne();
		$values['avg_result_passed_run_two'] = $this->filterFields->isAvgResultPassedRunTwo();
		$values['avg_result_finished_run_two'] = $this->filterFields->isAvgResultsFinishedRunTwo();

		$values['user[]'] = 231;


		$this->setValuesByArray($values);
	}


	public function stripUsers() {
		$users = $this->getInput("user");

		if ($users[0] == "") {
			return false;
		}

		$users = explode(',', $users);

		return $users;
	}
}