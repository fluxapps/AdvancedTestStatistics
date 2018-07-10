<?php

class xatsTriggers extends ActiveRecord {

	const TABLE_NAME = 'xats_triggers';
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
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $trigger_name;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $operator;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $value;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $precondition;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $operator_two;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $value_two;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $user_id;
	/**
	 * @var int
	 *
	 * @con_has_field   true
	 * @con_fieldtype   integer
	 * @con_length      8
	 */
	protected $user_percentage;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_length      256
	 */
	protected $datesender;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_length      256
	 */
	protected $intervalls;



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
	 * @return int
	 */
	public function getTriggerName() {
		return $this->trigger_name;
	}


	/**
	 * @param int $trigger_name
	 */
	public function setTriggerName($trigger_name) {
		$this->trigger_name = $trigger_name;
	}


	/**
	 * @return int
	 */
	public function getOperator() {
		return $this->operator;
	}


	/**
	 * @param int $operator
	 */
	public function setOperator($operator) {
		$this->operator = $operator;
	}


	/**
	 * @return int
	 */
	public function getValue() {
		return $this->value;
	}


	/**
	 * @param int $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}


	/**
	 * @return int
	 */
	public function getPrecondition() {
		return $this->precondition;
	}


	/**
	 * @param int $precondition
	 */
	public function setPrecondition($precondition) {
		$this->precondition = $precondition;
	}


	/**
	 * @return int
	 */
	public function getOperatorTwo() {
		return $this->operator_two;
	}


	/**
	 * @param int $operator_two
	 */
	public function setOperatorTwo($operator_two) {
		$this->operator_two = $operator_two;
	}


	/**
	 * @return int
	 */
	public function getValueTwo() {
		return $this->value_two;
	}


	/**
	 * @param int $value_two
	 */
	public function setValueTwo($value_two) {
		$this->value_two = $value_two;
	}


	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}


	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}
}