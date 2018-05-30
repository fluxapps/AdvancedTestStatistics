<?php


class xatsFilter extends ActiveRecord{

	const TABLE_NAME = 'xats_filter';

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
	protected $filter_inactive;


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
	public function isFilterInactive() {
		return $this->filter_inactive;
	}


	/**
	 * @param bool $filter_inactive
	 */
	public function setFilterInactive($filter_inactive) {
		$this->filter_inactive = $filter_inactive;
	}


	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}
}