<?php

/**
 * Class ilAdvancedTestStatisticsAvgGUI
 *
 * @ilCtrl_isCalledBy ilAdvancedTestStatisticsAvgGUI: ilUIPluginRouterGUI, ilAdvancedTestStatisticsGUI
 */
class ilAdvancedTestStatisticsAvgGUI {

	const CMD_DISPLAY = 'display';

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var int
	 */
	protected $ref_id;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	public function __construct() {
	global $ilCtrl,$tpl,$ilTabs;

	$this->ctrl = $ilCtrl;
	$this->tpl = $tpl;
	$this->ref_id = $_GET['ref_id'];
	$this->tabs = $ilTabs;
	$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
	$this->test = ilObjectFactory::getInstanceByRefId($this->ref_id);
	}


	public function executeCommand() {
		$this->tpl->getStandardTemplate();
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

		$table = new ilAdvancedTestStatisticsAvgTableGUI($this,ilAdvancedTestStatisticsAvgGUI::CMD_DISPLAY);

		$this->tpl->setContent($table->getHTML());
		$this->tpl->show();

	}


	protected function initHeader() {
		$this->tpl->setTitle($this->test->getTitle());
		$this->tpl->setDescription($this->test->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->test->getId()));

		$this->ctrl->setParameterByClass('ilrepositorygui', 'ref_id', (int)$_GET['ref_id']);
		$this->tabs->setBackTarget($this->pl->txt('btn_back'), $this->ctrl->getLinkTargetByClass(array( 'ilrepositorygui', 'ilObjTestGUI' )));
	}

}
