<?php

/**
 * Class ilAdvancedTestStatisticsAggTableGUI
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilAdvancedTestStatisticsAggTableGUI extends ilTable2GUI {

	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var
	 */
	protected $parent_obj;
	/**
	 * @var ilAdvancedTestStatisticsPlugin
	 */
	protected $pl;
	/**
	 * @var object
	 */
	protected $object;


	public function __construct($a_parent_obj, $a_parent_cmd = "", $a_template_context = "") {
		global $ilCtrl, $ilTabs;

		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ref_id = $_GET['ref_id'];
		$this->object = ilObjectFactory::getInstanceByRefId($this->ref_id);
		$this->setExportFormats(array(self::EXPORT_EXCEL));

		parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);

		$this->setRowTemplate('tpl.row_template.html', $this->pl->getDirectory());

		$this->addColumns();
		$this->parseData();
		$this->ctrl->setParameterByClass(ilAdvancedTestStatisticsAggGUI::class,'ref_id',$this->ref_id);
	}


	public function construct_array() {
		$data = array();
		$class = new ilAdvancedTestStatisticsAggResults();
		$tst_id = $class->getTstidforRefid($this->ref_id);
		$id = $class->getTstidforRefid($this->ref_id);

		$avg_points_finished = $class->getAveragePointsFinshedTests($id, $this->ref_id);
		$avg_result_passed = $class->getAverageResultPassedTests($id,$this->ref_id);
		$avg_result_finished = $class->getAverageResultFinishedTests($id,$this->ref_id);

		$ext_fields = xatsExtendedStatisticsFields::where(array( 'ref_id' => $this->ref_id ))->first();
		if ($ext_fields == NULL){
			$ext_fields = new xatsExtendedStatisticsFields();
			$ext_fields->setRefId($this->ref_id);
			$ext_fields->create();
		}

		//Standard fields
		$data['Total number of participants who started the test'] = $class->getTotalNumberStartedTest($this->ref_id);
		$data['Total finished tests (Participants that used up all possible passes)'] = $class->getTotalFinishedTests($this->ref_id);
		$data['Average test processing time'] = $class->getAvgTestTime($this->ref_id,$tst_id);
		$data['Total passed tests'] = $class->getTotalPassedTests($this->ref_id);
		$data['Average points of passed tests'] = $class->getAveragePointsPassedTests($this->ref_id);
		$data['Average processing time of all passed tests'] = $class->getAverageTimePassedTests($this->ref_id);

		//Custom Fields
		//Check if the field is required and if there is data to display
		if ($ext_fields->isAvgPointsFinished() == 1) {
			if (!is_string($avg_points_finished)) {
				$data['Average points finished tests'] = round($avg_points_finished, 2);
			} else {
				$data['Average points finished tests'] = $avg_points_finished;
			}
		}

		if ($ext_fields->isAvgResultPassed() == 1) {
			if (!is_string($avg_result_passed)) {
				$data['Average result (%) passed tests'] = round($avg_result_passed, 2) . "%";
			} else {
				$data['Average result (%) passed tests'] = $avg_result_passed;
			}
		}

		if ($ext_fields->isAvgResultFinished() == 1) {
			if (!is_string($avg_result_finished)){
			$data['Average result(%) finished tests'] = round($avg_result_finished, 2) . "%";
			}
			else{
				$data['Average result(%) finished tests'] = $avg_result_finished;
			}
		}
		if ($ext_fields->isAvgResultPassedRunOne() == 1) {
			$data['Average result(%) passed tests (Run 1)'] = round($class->getAverageResultPassedTestsRunOne($this->ref_id)) . '%';
		}
		if ($ext_fields->isAvgResultFinishedRunOne() == 1) {
			$data['Average result(%) finished tests (Run 1)'] = round($class->getAverageResultFinishedTestsRunOne($this->ref_id)). '%';
		}
		if ($ext_fields->isAvgResultPassedRunTwo() == 1) {
			$data['Average result(%) passed tests (Run 2)'] = round($class->getAverageResultPassedTestsRunTwo($this->ref_id)). '%';
		}
		if ($ext_fields->isAvgResultsFinishedRunTwo() == 1) {
			$data['Average result(%) finished tests (Run 2)'] = round($class->getAverageResultFinishedTestsRunTwo($this->ref_id)). '%';
		}

		return $data;
	}


	public function getSelectableColumns() {
		$cols = array();

		$cols['result'] = array( 'txt' => $this->pl->txt('cols_result'), 'default' => true, 'width' => 'auto', 'sort_field' => 'result' );
		$cols['value'] = array( 'txt' => $this->pl->txt('cols_value'), 'default' => true, 'width' => 'auto', 'sort_field' => 'value' );

		return $cols;
	}


	public function addColumns() {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if (isset($v['sort_field'])) {
					$sort = $v['sort_field'];
				} else {
					$sort = NULL;
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
	}


	public function parseData() {
		$rows = array();
		$data = $this->construct_array();
		foreach ($data as $k => $v) {
			$row['result'] = $k;
			$row['value'] = $v;

			$rows[] = $row;
		}
		$this->setData($rows);

		return $rows;
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if ($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', $a_set[$k]);
					$this->tpl->parseCurrentBlock();
				} else {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', '&nbsp;');
					$this->tpl->parseCurrentBlock();
				}
			}
		}
	}
}