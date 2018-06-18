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

	protected $operators = array('>', '<', '>=', '<=', '!=', '==');

	protected $extendedFields = array(
		"avg_points_finished" => "Average Points finished tests",
		"avg_result_passed" => "Average result passed tests",
		"avg_result_finished" => "Average result(%) finished tests",
		"avg_result_finished_run_one" => "Average result(%) passed tests (Run 1)",
		"avg_result_passed_run_one" => "Average result(%) finished tests (Run 1)",
		"avg_result_passed_run_two" => "Average result(%) passed tests (Run 2)",
		"avg_result_finished_run_two" => "Average result(%) finished tests (Run 2)"
	);




	/**
	 * ilAdvancedTestStatisticsAlertFormGUI constructor.
	 *
	 * @param            $parent_gui
	 */
	public function __construct($parent_gui) {
		global $ilCtrl, $tpl;

		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ref_id = $_GET['ref_id'];
		$this->parent_gui = $parent_gui;
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));

		$this->ctrl->setParameterByClass(ilAdvancedTestStatisticsSettingsGUI::class,'ref_id',$this->ref_id);

		parent::__construct();




	}




	public function setContent() {
		$this->tpl = new ilTemplate("tpl.alert_template.html", true ,true, './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics');

		$this->setTarget('_top');


		foreach ($this->extendedFields as $extendedField){
		$this->tpl->setCurrentBlock('OPTIONS');
		$this->tpl->setVariable('SELECT_NAME','Select one');
		$this->tpl->setVariable('OPTION_VALUE', $extendedField);
		$this->tpl->setVariable('OPTION', $extendedField);
		$this->tpl->parseCurrentBlock();
		}

		foreach ($this->operators as $operator){
			$this->tpl->setCurrentBlock('OPTIONS1');
			$this->tpl->setVariable('SELECT_NAME1','Select one');
			$this->tpl->setVariable('OPTION_VALUE1', $operator);
			$this->tpl->setVariable('OPTION1', $operator);
			$this->tpl->parseCurrentBlock();
		}
		$this->tpl->setCurrentBlock('VALUE');
		$this->tpl->setVariable('VALUE', 'Value');
		$this->tpl->parseCurrentBlock();

		foreach ($this->extendedFields as $extendedField){
			$this->tpl->setCurrentBlock('OPTIONS2');
			$this->tpl->setVariable('SELECT_NAME2','Select one');
			$this->tpl->setVariable('OPTION_VALUE2', $extendedField);
			$this->tpl->setVariable('OPTION2', $extendedField);
			$this->tpl->parseCurrentBlock();
		}

		foreach ($this->operators as $operator){
			$this->tpl->setCurrentBlock('OPTIONS3');
			$this->tpl->setVariable('SELECT_NAME3','Select one');
			$this->tpl->setVariable('OPTION_VALUE3', $operator);
			$this->tpl->setVariable('OPTION3', $operator);
			$this->tpl->parseCurrentBlock();
		}

		$this->tpl->setVariable('VALUE1', 'Value');
		$this->tpl->parseCurrentBlock();
		$user = new ilTextInputGUI("user", "login");
		$user->setDataSource($this->ctrl->getLinkTargetByClass(array(
			ilUIPluginRouterGUI::class,
			ilAdvancedTestStatisticsPlugin::class
		), ilAdvancedTestStatisticsPlugin::CMD_ADD_USER_AUTO_COMPLETE, "", true));
		$user->setInfo("User");
		$this->addItem($user);

		$html = $user->getToolbarHTML();
		$this->tpl->setVariable("SEARCHFIELD",$html);

		$link = $this->ctrl->getLinkTargetByClass(ilAdvancedTestStatisticsSettingsGUI::class, ilAdvancedTestStatisticsSettingsGUI::CMD_CREATE_TRIGGER);
		$this->tpl->setVariable("HREF",$link);

		return $this->tpl->get();
	}

}