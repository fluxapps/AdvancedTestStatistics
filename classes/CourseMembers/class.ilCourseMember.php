<?php

class ilCourseMember {

	/**
	 * @var string
	 */
	protected $login;
	/**
	 * @var int
	 */
	protected $usr_id;
	/**
	 * @var string
	 */
	protected $email;
	/**
	 * @var string
	 */
	protected $firstname;
	/**
	 * @var string
	 */
	protected $lastname;


	/**
	 * @return string
	 */
	public function getLogin() {
		return $this->login;
	}


	/**
	 * @param string $login
	 */
	public function setLogin($login) {
		$this->login = $login;
	}


	/**
	 * @return int
	 */
	public function getUsrId() {
		return $this->usr_id;
	}


	/**
	 * @param int $usr_id
	 */
	public function setUsrId($usr_id) {
		$this->usr_id = $usr_id;
	}


	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}


	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}


	/**
	 * @return string
	 */
	public function getFirstname() {
		return $this->firstname;
	}


	/**
	 * @param string $firstname
	 */
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}


	/**
	 * @return string
	 */
	public function getLastname() {
		return $this->lastname;
	}


	/**
	 * @param string $lastname
	 */
	public function setLastname($lastname) {
		$this->lastname = $lastname;
	}





}