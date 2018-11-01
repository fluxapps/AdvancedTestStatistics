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
		"avg_points_finished",
		"avg_result_passed",
		"avg_result_finished",
		"avg_result_finished_run_one",
		"avg_result_passed_run_one",
		"avg_result_passed_run_two",
		"avg_result_finished_run_two",
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


    /**
     *
     */
    public function initForm() {
		$this->setTarget('_top');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initButtons();


		$c = new ilFormSectionHeaderGUI();
		$c->setTitle($this->pl->txt("form_title_filter_statistics"));
		$this->addItem($c);

		$b = new ilCheckboxInputGUI($this->pl->txt("input_dont_count_inactive"), "inactive");
		$this->addItem($b);

		//$check_prop = new ilCheckboxInputGUI("Custom Filter", "custom_filter");
		$multiuserselectgui = new ilMultiUserSelectInputGUI($this->pl->txt("input_filter_users"), "user");
		//$check_prop->addSubItem($multiuserselectgui);
		//$this->addItem($check_prop);
		$this->addItem($multiuserselectgui);


		$c = new ilFormSectionHeaderGUI();
		$c->setTitle($this->pl->txt("form_title_extend_statistic_fields"));
		$c->setInfo($this->pl->txt("form_title_extend_statistic_fields_info"));
		$this->addItem($c);

		foreach ($this->extendedFields as $value) {
			$c = new ilCheckboxInputGUI($this->pl->txt($value), $value);
			$this->addItem($c);
		}
		$this->fillForm();
	}


    /**
     *
     */
    public function initButtons() {
		$this->addCommandButton(ilAdvancedTestStatisticsSettingsGUI::CMD_UPDATE_FILTER, $this->pl->txt('form_update'));
		$this->addCommandButton(ilAdvancedTestStatisticsSettingsGUI::CMD_CANCEL, $this->pl->txt('form_cancel'));
	}

    /**
     * @return bool
     */
	public function save() {
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


    /**
     * @return bool
     */
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

    /**
     *
     */
	public function fillForm() {
        $users = xatsFilteredUsers::where(array('ref_id' => $this->ref_id))->getArray();
        $users_array = array();
        foreach ($users as $user) {
            $users_array[] = $user['user_id'];
        }
        $values['user'] = $users_array;
        $values['inactive'] = $this->object->isFilterInactive();

		$values['avg_points_finished'] = $this->filterFields->isAvgPointsFinished();
		$values['avg_result_passed'] = $this->filterFields->isAvgResultPassed();
		$values['avg_result_finished'] = $this->filterFields->isAvgResultFinished();
		$values['avg_result_finished_run_one'] = $this->filterFields->isAvgResultFinishedRunOne();
		$values['avg_result_passed_run_one'] = $this->filterFields->isAvgResultPassedRunOne();
		$values['avg_result_passed_run_two'] = $this->filterFields->isAvgResultPassedRunTwo();
		$values['avg_result_finished_run_two'] = $this->filterFields->isAvgResultsFinishedRunTwo();


		$this->setValuesByArray($values);
	}

    /**
     * @return array
     */
	public function stripUsers() {
		$users = $this->getInput("user");

		if ($users[0] == "") {
			return [];
		}

		$users = explode(',', $users);

		return $users;
	}
}