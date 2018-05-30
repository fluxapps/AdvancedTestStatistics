<?php



class xatsExtendedStatisticsFields extends ActiveRecord {

	const TABLE_NAME = 'xats_extended_fields';

	/**
	 * @var int
	 *
	 * @con_is_primary  true
	 * @con_sequence    true
	 * @con_is_notnull  true
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $id;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $ref_id;
	/**
	 * @var bool
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $avg_points_finished;
	/**
	 * @var bool
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $avg_result_passed;
	/**
	 * @var bool
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $avg_result_finished;
	/**
	 * @var bool
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $avg_result_passed_run_one;
	/**
	 * @var bool
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $avg_result_finished_run_one;
	/**
	 * @var bool
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $avg_result_passed_run_two;
	/**
	 * @var bool
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $avg_results_finished_run_two;


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 */
	public function getRefId() {
		return $this->ref_id;
	}


	/**
	 * @param int $ref_id
	 */
	public function setRefId($ref_id) {
		$this->ref_id = $ref_id;
	}


	/**
	 * @return bool
	 */
	public function isAvgPointsFinished() {
		return $this->avg_points_finished;
	}


	/**
	 * @param bool $avg_points_finished
	 */
	public function setAvgPointsFinished($avg_points_finished) {
		$this->avg_points_finished = $avg_points_finished;
	}


	/**
	 * @return bool
	 */
	public function isAvgResultPassed() {
		return $this->avg_result_passed;
	}


	/**
	 * @param bool $avg_result_passed
	 */
	public function setAvgResultPassed($avg_result_passed) {
		$this->avg_result_passed = $avg_result_passed;
	}


	/**
	 * @return bool
	 */
	public function isAvgResultFinished() {
		return $this->avg_result_finished;
	}


	/**
	 * @param bool $avg_result_finished
	 */
	public function setAvgResultFinished($avg_result_finished) {
		$this->avg_result_finished = $avg_result_finished;
	}


	/**
	 * @return bool
	 */
	public function isAvgResultPassedRunOne() {
		return $this->avg_result_passed_run_one;
	}


	/**
	 * @param bool $avg_result_passed_run_one
	 */
	public function setAvgResultPassedRunOne($avg_result_passed_run_one) {
		$this->avg_result_passed_run_one = $avg_result_passed_run_one;
	}


	/**
	 * @return bool
	 */
	public function isAvgResultFinishedRunOne() {
		return $this->avg_result_finished_run_one;
	}


	/**
	 * @param bool $avg_result_finished_run_one
	 */
	public function setAvgResultFinishedRunOne($avg_result_finished_run_one) {
		$this->avg_result_finished_run_one = $avg_result_finished_run_one;
	}


	/**
	 * @return bool
	 */
	public function isAvgResultPassedRunTwo() {
		return $this->avg_result_passed_run_two;
	}


	/**
	 * @param bool $avg_result_passed_run_two
	 */
	public function setAvgResultPassedRunTwo($avg_result_passed_run_two) {
		$this->avg_result_passed_run_two = $avg_result_passed_run_two;
	}


	/**
	 * @return bool
	 */
	public function isAvgResultsFinishedRunTwo() {
		return $this->avg_results_finished_run_two;
	}


	/**
	 * @param bool $avg_results_finished_run_two
	 */
	public function setAvgResultsFinishedRunTwo($avg_results_finished_run_two) {
		$this->avg_results_finished_run_two = $avg_results_finished_run_two;
	}


	public static function returnDbTableName() {
	return self::TABLE_NAME;
	}
}