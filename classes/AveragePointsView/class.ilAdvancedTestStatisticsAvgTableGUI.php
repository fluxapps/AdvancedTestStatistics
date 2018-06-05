<?php



class ilAdvancedTestStatisticsAvgTableGUI extends ilTable2GUI {


	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	protected $parent_obj;
	/**
	 * @var ilAdvancedTestStatisticsPlugin
	 */
	protected $pl;


	public function __construct($a_parent_obj, $a_parent_cmd = "", $a_template_context = "") {
		global $ilCtrl, $ilTabs;

		$this->ctrl = $ilCtrl;
		$this->tabs = $ilTabs;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();

		parent::__construct($a_parent_obj, $a_parent_cmd, $a_template_context);

		$this->setRowTemplate('tpl.row_template.html', $this->pl->getDirectory());

		$this->addColumns();
		$this->parseData();
	}


	public function getSelectableColumns() {
		$cols = array();

		$cols['question_id'] = array( 'txt' => $this->pl->txt('cols_question_id'), 'default' => true, 'width' => 'auto', 'sort_field' => 'question_id' );
		$cols['question_title'] = array( 'txt' => $this->pl->txt('cols_question_title'), 'default' => true, 'width' => 'auto', 'sort_field' => 'question_title' );
		$cols['points'] = array( 'txt' => $this->pl->txt('cols_points'), 'default' => true, 'width' => 'auto', 'sort_field' => 'points' );
		$cols['percentage'] = array( 'txt' => $this->pl->txt('cols_percentage'), 'default' => true, 'width' => 'auto', 'sort_field' => 'percentage' );
		$cols['number_answers'] = array( 'txt' => $this->pl->txt('cols_number_answers'), 'default' => true, 'width' => 'auto', 'sort_field' => 'number_answers' );

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


			$row['question_id'] = '131';
			$row['question_title'] = 'questiontitle';
			$row['points'] = '13 points';
			$row['percentage'] = '15%';
			$row['number_answers'] = '2 answers';


			$rows[] = $row;

		$this->setData($rows);

		return $rows;
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		foreach ($this->getSelectableColumns() as $k => $v){
			if($this->isColumnSelected($k)){
				if($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE',$a_set[$k]);
					$this->tpl->parseCurrentBlock();
				}
				else{
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE','&nbsp;');
					$this->tpl->parseCurrentBlock();

				}
			}
		}
	}








}