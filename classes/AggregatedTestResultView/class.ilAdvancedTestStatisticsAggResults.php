<?php

/**
 * Class ilAdvancedTestStatisticsAggResults
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 *
 * This class includes all DB Queries and calculations for filling the table
 */
class ilAdvancedTestStatisticsAggResults {

    /**
     * ilAdvancedTestStatisticsAggResults constructor.
     * @param int $ref_id
     */
    public function __construct($ref_id = 0) {
		global $ilDB;
        $ref_id = $ref_id ? $ref_id : $_GET['ref_id'];
		$this->object = ilObjectFactory::getInstanceByRefId($ref_id);
		$this->DB = $ilDB;
	}


	/**
	 * @return int
	 *
	 */
	public function getTotalNumberStartedTest($ref_id, $as_number = false) {
		$testdata = new ilTestEvaluationData($this->object);
		$participants = $testdata->getParticipants();

		if($this->checkFilterInactive($ref_id) == 1){
			$inactive_users = $this->getInactiveUsers();
			foreach ($inactive_users as $inactive_user) {
				foreach ($participants as $key => $participant){
                    if($inactive_user == $participant->getUserId()){
                        unset($participants[$key]);
                    }
				}
			}
		}

        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

		if (!$participants && !$as_number) {
			return 'Nothing to display';
		}

		return count($participants);
	}


	/**
	 * @param $ref_id
	 *
	 * @return mixed
	 */
	public function getTotalFinishedTests($ref_id, $as_number = false) {
		global $ilDB;

		$select = "select user_fi from object_data
inner join object_reference on object_data.obj_id = object_reference.obj_id
inner join tst_tests on object_data.obj_id = tst_tests.obj_fi
inner join tst_active on tst_tests.test_id = tst_active.test_fi
where ref_id = " . $ilDB->quote($ref_id, "integer") . " and submitted = 1 ";

		$result = $ilDB->query($select);
        $rows = [];
		while ($row = $ilDB->fetchAssoc($result)) {
			$active_count = $row['user_fi'];
			$rows[$active_count] = $active_count;
		}
		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($inactive_usrs as $user) {
				unset($rows[$user]);
			}
		}

		// Filter explicitly filtered users
        foreach ($this->getFilteredUsers() as $filtered_user) {
            unset($rows[$filtered_user]);
        }

		$rows = array_filter($rows);

		if(!$rows && !$as_number){
			return 'Nothing to display';
		}

		return count($rows);
	}


	/**
	 * @param $ref_id
	 * @param $tst_id
	 *
	 * @return string
	 */
	public function getAvgTestTime($ref_id, $tst_id, $as_number = false) {
		global $ilDB;

		$inactive_usrs = $this->getInactiveUsers();
		$result = $ilDB->queryF("SELECT tst_times.*,user_fi FROM tst_active, tst_times WHERE tst_active.test_fi = %s AND tst_active.active_id = tst_times.active_fi", array( 'integer' ), array( $tst_id ));
		$times = array();
		while ($row = $ilDB->fetchObject($result)) {
			//Filter inactive users if checkbox is set
			if (!($this->checkFilterInactive($ref_id) == 1 && key_exists($row->user_fi, $inactive_usrs)) && !in_array($row->user_fi, $this->getInactiveUsers()) && !in_array($row->user_fi, $this->getFilteredUsers())) {
                preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/", $row->started, $matches);
                $epoch_1 = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
                preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/", $row->finished, $matches);
                $epoch_2 = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
                $times[$row->active_fi] += ($epoch_2 - $epoch_1);
			}
		}

		if (empty($times)) {
            return $as_number ? 0 : 'Nothing to display';
        }

		$max_time = 0;
		$counter = 0;
		foreach ($times as $key => $value) {
			$max_time += $value;
			$counter ++;
		}

		$average_time = $counter ? round($max_time / $counter) : 0;

		if ($as_number) {
		    return $average_time;
        }

		$diff_seconds = $average_time;
		$diff_hours = floor($diff_seconds / 3600);
		$diff_seconds -= $diff_hours * 3600;
		$diff_minutes = floor($diff_seconds / 60);
		$diff_seconds -= $diff_minutes * 60;

		return sprintf("%02d:%02d:%02d", $diff_hours, $diff_minutes, $diff_seconds);
	}


	/**
	 * @param $ref_id
	 *
	 * @return int
	 */
	public function getTotalPassedTests($ref_id, $as_number = false) {
		$eval =& $this->object->getCompleteEvaluationData();
		$participants =& $eval->getParticipants();

		$total_passed = 0;

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($participants as $key => $participant) {
				if (key_exists($participant->getUserID(), $inactive_usrs)) {
					unset($participants[$key]);
				}
			}
		}

		// Filter explicitly filtered users
        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

		foreach ($participants as $userdata) {
			if ($userdata->getPassed()) {
				$total_passed ++;
			}
		}


		if(!$total_passed && !$as_number){
			return 'Nothing to display';
		}

		return $total_passed;
	}


	/**
	 * @param $ref_id
	 *
	 * @return string
	 */
	public function getAveragePointsPassedTests($ref_id, $as_number = false) {
		$eval =& $this->object->getCompleteEvaluationData();
		$participants =& $eval->getParticipants();

		$total_passed = 0;
		$total_passed_reached = 0;
		$total_passed_max = 0;

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($participants as $key => $participant) {
				if (key_exists($participant->getUserID(), $inactive_usrs)) {
					unset($participants[$key]);
				}
			}
		}

        // Filter explicitly filtered users
        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

		foreach ($participants as $userdata) {
			if ($userdata->getPassed()) {
				$total_passed ++;
				$total_passed_reached += $userdata->getReached();
				$total_passed_max += $userdata->getMaxpoints();
			}
		}

		if (!$total_passed) {
            return $as_number ? 0 : 'Nothing to display';
        }

		$average_passed_reached = $total_passed ? $total_passed_reached / $total_passed : 0;
		$average_passed_max = $total_passed ? $total_passed_max / $total_passed : 0;

		if ($as_number) {
		    return $average_passed_reached;
        }

		return sprintf("%2.2f", $average_passed_reached) . " " . strtolower("of") . " " . sprintf("%2.2f", $average_passed_max);
	}


	/**
	 * @param $ref_id
	 *
	 * @return string
	 */
	public function getAverageTimePassedTests($ref_id, $as_number = false) {
		$eval =& $this->object->getCompleteEvaluationData();
		$participants =& $eval->getParticipants();

		$total_passed = 0;
		$total_passed_time = 0;

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($participants as $key => $participant) {
				if (key_exists($participant->getUserID(), $inactive_usrs)) {
					unset($participants[$key]);
				}
			}
		}

        // Filter explicitly filtered users
        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

		foreach ($participants as $userdata) {
			if ($userdata->getPassed()) {
				$total_passed ++;
				$total_passed_time += $userdata->getTimeOfWork();
			}
		}

        if (!$total_passed) {
            return $as_number ? 0 : 'Nothing to display';
        }

		$average_passed_time = $total_passed ? $total_passed_time / $total_passed : 0;

        if ($as_number) {
            return $average_passed_time;
        }

		$average_time = $average_passed_time;
		$diff_seconds = $average_time;
		$diff_hours = floor($diff_seconds / 3600);
		$diff_seconds -= $diff_hours * 3600;
		$diff_minutes = floor($diff_seconds / 60);
		$diff_seconds -= $diff_minutes * 60;

		return sprintf("%02d:%02d:%02d", $diff_hours, $diff_minutes, $diff_seconds);
	}









	/**
	 *
	 * CUSTOM FIELDS
	 *
	 *
	 */

	/**
	 * @param $tst_id
	 * @param $ref_id
	 *
	 * @return float|int|string
	 */
	public function getAveragePointsFinshedTests($tst_id, $ref_id, $as_number = false) {
        $eval =& $this->object->getCompleteEvaluationData();
        $participants =& $eval->getParticipants();

        $total_passed = 0;
        $total_passed_reached = 0;
        $total_passed_max = 0;

        //Filter inactive users if checkbox is set
        if ($this->checkFilterInactive($ref_id) == 1) {
            $inactive_usrs = $this->getInactiveUsers();
            foreach ($participants as $key => $participant) {
                if (key_exists($participant->getUserID(), $inactive_usrs)) {
                    unset($participants[$key]);
                }
            }
        }

        // Filter explicitly filtered users
        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

        foreach ($participants as $userdata) {
            $total_passed ++;
            $total_passed_reached += $userdata->getReached();
            $total_passed_max += $userdata->getMaxpoints();
        }

        if (!$total_passed) {
            return $as_number ? 0 : 'Nothing to display';
        }

        $average_passed_reached = $total_passed ? $total_passed_reached / $total_passed : 0;
        $average_passed_max = $total_passed ? $total_passed_max / $total_passed : 0;

        if ($as_number) {
            return $average_passed_reached;
        }

        return sprintf("%2.2f", $average_passed_reached) . " " . strtolower("of") . " " . sprintf("%2.2f", $average_passed_max);
	}


	/**
	 * @param $tst_id
	 * @param $ref_id
	 *
	 * @return float|int|string
	 */
	public function getAverageResultPassedTests($tst_id, $ref_id, $as_number = false) {
		$select = "select * from tst_active
inner join tst_result_cache on tst_active.active_id = tst_result_cache.active_fi
where passed = 1 and test_fi = " . $this->DB->quote($tst_id, "integer");

		$result = $this->DB->query($select);

		$rows = [];
		while ($row = $this->DB->fetchAssoc($result)) {
			$rows[$row['user_fi']] = (100 / $row['maxpoints']) * $row['reached_points'];
		}

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($inactive_usrs as $user) {
				unset($rows[$user]);
			}
		}

        // Filter explicitly filtered users
        foreach ($this->getFilteredUsers() as $filtered_user) {
            unset($rows[$filtered_user]);
        }

		$rows = array_filter($rows);
        if (!count($rows)) {
            return $as_number ? 0 : 'Nothing to display';
        }

        $average = count($rows) ? (array_sum($rows) / count($rows)) : 0;


        return $average;
	}


	/**
	 * @param $tst_id
	 * @param $ref_id
	 *
	 * @return float|int|string
	 */
	public function getAverageResultFinishedTests($tst_id, $ref_id, $as_number = false) {

		$select = "select user_fi, points, maxpoints from tst_active
inner join tst_pass_result on tst_active.active_id = tst_pass_result.active_fi
 where test_fi = " . $this->DB->quote($tst_id, "integer");

		$result = $this->DB->query($select);

		$rows = array();
		while ($row = $this->DB->fetchAssoc($result)) {
			$rows[$row['user_fi']] = (100 / $row['maxpoints']) * $row['points'];
		}

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($inactive_usrs as $user) {
				unset($rows[$user]);
			}
		}

        // Filter explicitly filtered users
        foreach ($this->getFilteredUsers() as $filtered_user) {
            unset($rows[$filtered_user]);
        }

		$average = count($rows) ? (array_sum($rows) / count($rows)) : 0;

		if (!$average) {
			$average = $as_number ? 0 : 'Nothing to display';
		}

		return $average;
	}


	/**
	 * @param $ref_id
	 *
	 * @return string
	 */
	public function getAverageResultPassedTestsRunOne($ref_id, $as_number = false){

		$eval =& $this->object->getCompleteEvaluationData();
		$participants =& $eval->getParticipants();

		$total_passed = 0;
		$total_passed_reached = 0;
		$total_passed_max = 0;

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($participants as $key => $participant) {
				if (key_exists($participant->getUserID(), $inactive_usrs)) {
					unset($participants[$key]);
				}
			}
		}

        // Filter explicitly filtered users
        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

		foreach ($participants as $userdata) {
            /**
             * @var ilTestEvaluationUserData $userdata
             * @var ilTestEvaluationPassData $pass_data_run_1
             */
            $pass_data_run_1 = $userdata->getPass(0);
			if ($pass_data_run_1 && $pass_data_run_1->getPass()) {
				$total_passed ++;
				$total_passed_reached += $pass_data_run_1->getReachedPoints();
				$total_passed_max += $pass_data_run_1->getMaxPoints();
			}
		}

		if (!$total_passed) {
		    return $as_number ? 0 : 'Nothing to display';
        }

		$average_passed_reached = $total_passed ? $total_passed_reached / $total_passed : 0;
		$average_passed_max = $total_passed ? $total_passed_max / $total_passed : 0;

		$result = $total_passed ? (($average_passed_reached/$average_passed_max) * 100) : 0;

		return $result;
	}


	/**
	 * @param $ref_id
	 *
	 * @return string
	 */
	public function getAverageResultPassedTestsRunTwo($ref_id, $as_number = false){

		$eval =& $this->object->getCompleteEvaluationData();
		$participants =& $eval->getParticipants();

		$total_passed = 0;
		$total_passed_reached = 0;
		$total_passed_max = 0;

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($participants as $key => $participant) {
				if (key_exists($participant->getUserID(), $inactive_usrs)) {
					unset($participants[$key]);
				}
			}
		}

        // Filter explicitly filtered users
        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

		foreach ($participants as $userdata) {
		    /**
             * @var ilTestEvaluationUserData $userdata
             * @var ilTestEvaluationPassData $pass_data_run_2
             */
		    $pass_data_run_2 = $userdata->getPass(1);
			if ($pass_data_run_2 && $pass_data_run_2->getPass()) {
				$total_passed ++;
				$total_passed_reached += $pass_data_run_2->getReachedPoints();
				$total_passed_max += $pass_data_run_2->getMaxPoints();
			}
		}

		if (!$total_passed) {
		    return $as_number ? 0 : 'Nothing to display';
        }

		$average_passed_reached = $total_passed ? $total_passed_reached / $total_passed : 0;
		$average_passed_max = $total_passed ? $total_passed_max / $total_passed : 0;

        $result = $average_passed_max ? (($average_passed_reached/$average_passed_max) * 100) : 0;

		return $result;
	}


	/**
	 * @param $ref_id
	 *
	 * @return string
	 */
	public function getAverageResultFinishedTestsRunOne($ref_id, $as_number = false){

		$eval =& $this->object->getCompleteEvaluationData();
		$participants =& $eval->getParticipants();

		$total_passed = 0;
		$total_passed_reached = 0;
		$total_passed_max = 0;

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($participants as $key => $participant) {
				if (key_exists($participant->getUserID(), $inactive_usrs)) {
					unset($participants[$key]);
				}
			}
		}

        // Filter explicitly filtered users
        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

		foreach ($participants as $userdata) {
            $pass = $userdata->getPass(0);
            if ($pass) {
                $total_passed ++;
                $total_passed_reached += $pass->getReachedPoints();
                $total_passed_max += $pass->getMaxPoints();
            }
		}

		if (!$total_passed) {
		    return $as_number ? 0 : 'Nothing to display';
        }

		$average_passed_reached = $total_passed ? $total_passed_reached / $total_passed : 0;
		$average_passed_max = $total_passed ? $total_passed_max / $total_passed : 0;

		$result = $average_passed_max ? (($average_passed_reached/$average_passed_max) * 100) : 0;

        return $result;
	}


	/**
	 * @param $ref_id
	 *
	 * @return string
	 */
	public function getAverageResultFinishedTestsRunTwo($ref_id, $as_number = false){

		$eval =& $this->object->getCompleteEvaluationData();
		$participants =& $eval->getParticipants();

		$total_passed = 0;
		$total_passed_reached = 0;
		$total_passed_max = 0;

		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($participants as $key => $participant) {
				if (key_exists($participant->getUserID(), $inactive_usrs)) {
					unset($participants[$key]);
				}
			}
		}

        // Filter explicitly filtered users
        if ($filtered_users = $this->getFilteredUsers()) {
            foreach ($participants as $key => $participant) {
                if (in_array($participant->getUserId(), $filtered_users)) {
                    unset($participants[$key]);
                }
            }
        }

		foreach ($participants as $userdata) {
            $pass = $userdata->getPass(1);
            if ($pass) {
                $total_passed ++;
                $total_passed_reached += $pass->getReachedPoints();
                $total_passed_max += $pass->getMaxPoints();
            }
		}

		if (!$total_passed) {
		    return $as_number ? 0 : 'Nothing to display';
        }

		$average_passed_reached = $total_passed ? $total_passed_reached / $total_passed : 0;
		$average_passed_max = $total_passed ? $total_passed_max / $total_passed : 0;

        $result = $average_passed_max ? (($average_passed_reached/$average_passed_max) * 100) : 0;

		return $result;

	}

    /**
     * @param $ref_id
     * @return array
     */
    public function getQuestionPercentage($ref_id) {
        $test = new ilObjTest($ref_id);
        $questions = $test->getQuestions();

        $valuesreached = array();
        foreach ($questions as $qst_id) {
            $valuesreached[$qst_id] = $this->getTotalRightAnswersForTestQuestion($qst_id) * 100;
        }
        return $valuesreached;
	}

    /**
     * @param $qst_id
     * @return float|int
     */
    protected function getTotalRightAnswersForTestQuestion($qst_id) {
        $result = $this->DB->query("SELECT * FROM tst_test_result WHERE question_fi = " . $qst_id);
        $answers = array();
        while ($row = $this->DB->fetchAssoc($result))
        {
            $reached = $row["points"];
            $max = assQuestion::_getMaximumPoints($row["question_fi"]);
            array_push($answers, array("reached" => $reached, "max" => $max));
        }
        $max = 0.0;
        $reached = 0.0;
        foreach ($answers as $key => $value)
        {
            $max += $value["max"];
            $reached += $value["reached"];
        }
        if ($max > 0)
        {
            return $reached / $max;
        }
        else
        {
            return 0;
        }
	}

	/**
	 * HELPER FUNCTIONS
	 *
	 *
	 *
	 */

	/**
	 * @param $ref_id
	 *
	 * @return mixed
	 * Get test_id for Ref_id of the testobject
	 */
	public function getTstidforRefid($ref_id) {

		$select = "select test_id from object_data
inner join object_reference on object_data.obj_id = object_reference.obj_id
inner join tst_tests on object_data.obj_id = tst_tests.obj_fi
where ref_id = " . $this->DB->quote($ref_id, "integer");

		$result = $this->DB->query($select);

		while ($row = $this->DB->fetchAssoc($result)) {
			$tst_id = $row['test_id'];
		}

		return $tst_id;
	}


	/**
	 * @return array
	 * Get all inactive users
	 */
	public function getInactiveUsers() {
		$select = "select usr_id from usr_data where active = 0";

		$result = $this->DB->query($select);

		while ($row = $this->DB->fetchAssoc($result)) {
			$rows[$row['usr_id']] = $row['usr_id'];
		}

		return $rows;
	}

    /**
     * @return array
     */
	public function getFilteredUsers() {
        $users = array();
	    foreach (xatsFilteredUsers::where(array('ref_id' => $_GET['ref_id']))->get() as $filtered_user){
            /** @var $filtered_user xatsFilteredUsers */
            $users[$filtered_user->getUserId()] = $filtered_user->getUserId();
        }
        return $users;
    }

	/**
	 * @param $ref_id
	 *
	 * @return mixed
	 * Check if Inactive filter is set
	 */
	public function checkFilterInactive($ref_id) {
		//Check if filter is set
		$selecta = "select * from xats_filter where ref_id = " . $this->DB->quote($ref_id, "integer");

		$result = $this->DB->query($selecta);

		while ($row = $this->DB->fetchAssoc($result)) {
			$is_filter = $row['filter_inactive'];
		}

		return $is_filter;
	}
}