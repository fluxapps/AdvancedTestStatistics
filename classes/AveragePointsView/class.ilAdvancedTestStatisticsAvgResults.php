<?php



class ilAdvancedTestStatisticsAvgResults {

	protected $object;


	public function __construct() {
		global $ilDB;


		$this->DB = $ilDB;
		$this->object = ilObjectFactory::getInstanceByRefId($_GET['ref_id']);


	}



	public function getQuestionsFiltered($ref_id){
		$data =& $this->object->getCompleteEvaluationData();
		$foundParticipants =& $data->getParticipants();


		//Filter inactive users if checkbox is set
		if ($this->checkFilterInactive($ref_id) == 1) {
			$inactive_usrs = $this->getInactiveUsers();
			foreach ($foundParticipants as $participant) {
				if (key_exists($participant->getUserID(), $inactive_usrs)) {
					$key = key($participant);
					unset($key, $foundParticipants);
				}
			}
		}


		foreach ($data->getQuestionTitles() as $question_id => $question_title)
		{
			$answered = 0;
			$reached = 0;
			$max = 0;
			foreach ($foundParticipants as $userdata)
			{
				for ($i = 0; $i <= $userdata->getLastPass(); $i++)
				{
					if (is_object($userdata->getPass($i)))
					{
						$question =& $userdata->getPass($i)->getAnsweredQuestionByQuestionId($question_id);
						if (is_array($question))
						{
							$answered++;
							$reached += $question["reached"];
							$max += $question["points"];
						}
					}
				}
			}
			$percent = $max ? $reached/$max * 100.0 : 0;
			$counter++;
			$results["questions"][$question_id] = array(
				$question_title,
				sprintf("%.2f", $answered ? $reached / $answered : 0) . " " . strtolower("of") . " " . sprintf("%.2f", $answered ? $max / $answered : 0),
				sprintf("%.2f", $percent) . "%",
				$answered,
				sprintf("%.2f", $answered ? $reached / $answered : 0),
				sprintf("%.2f", $answered ? $max / $answered : 0),
				$percent / 100.0
			);
		}
		return $results;

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
	 * @param $ref_id
	 *
	 * @return mixed
	 * Check if Inactive filter is set
	 */
	public function checkFilterInactive($ref_id) {
		//Check if filter is set
		$selecta = "select * from xats_filter where ref_id = " . $this->DB->quote($ref_id, "integer") . "";

		$result = $this->DB->query($selecta);

		while ($row = $this->DB->fetchAssoc($result)) {
			$is_filter = $row['filter_inactive'];
		}

		return $is_filter;
	}
}