<?php

/**
 * Class ilAdvancedTestStatisticsAggGUI
 * @ilCtrl_isCalledBy ilAdvancedTestStatisticsAggGUI: ilUIPluginRouterGUI, ilAdvancedTestStatisticsGUI
 */

class ilAdvancedTestStatisticsAggGUI {

	const CMD_DISPLAY = 'display';

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;

	protected $pl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	public function __construct() {
		global $ilCtrl,$tpl,$ilTabs;

		$this->tabs = $ilTabs;
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ref_id = $_GET['ref_id'];
		$this->test = ilObjectFactory::getInstanceByRefId($this->ref_id);

	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();

		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd($this::CMD_DISPLAY);
				$this->{$cmd}();
		}
	}


	public function display(){
		$this->tpl->getStandardTemplate();
		$this->initHeader();

		$table = new ilAdvancedTestStatisticsAggTableGUI($this,ilAdvancedTestStatisticsAggGUI::CMD_DISPLAY);

		$this->tpl->setContent($table->getHTML());
		$this->tpl->show();
	}

	public function initHeader(){
		$this->tpl->setTitle($this->test->getTitle());
		$this->tpl->setDescription($this->test->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->test->getId()));

		$this->ctrl->setParameterByClass('ilrepositorygui', 'ref_id', (int)$_GET['ref_id']);
		$this->tabs->setBackTarget($this->pl->txt('btn_back'), $this->ctrl->getLinkTargetByClass(array( 'ilrepositorygui', 'ilObjTestGUI', 'ilTestEvaluationGUI' ), 'outEvaluation'));
	}
}