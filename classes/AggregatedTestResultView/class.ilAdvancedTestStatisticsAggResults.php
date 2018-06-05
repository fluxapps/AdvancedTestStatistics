<?php

class ilAdvancedTestStatisticsAggResults {

	public function getTotalFinishedTests($ref_id) {
		global $ilDB;

		$select = "select count(active_id) from object_data
inner join object_reference on object_data.obj_id = object_reference.obj_id
inner join tst_tests on object_data.obj_id = tst_tests.obj_fi
inner join tst_active on tst_tests.test_id = tst_active.test_fi
where ref_id = " . $ilDB->quote($ref_id, "integer") . " and submitted = 1 ";

		$result = $ilDB->query($select);

		while ($row = $ilDB->fetchAssoc($result)) {
			$active_count = $row['count(active_id)'];
		}

		return $active_count;
	}


	public function getTotalParticipants($ref_id) {
		global $ilDB;

		$select = "select count(active_id) from object_data
inner join object_reference on object_data.obj_id = object_reference.obj_id
inner join tst_tests on object_data.obj_id = tst_tests.obj_fi
inner join tst_active on tst_tests.test_id = tst_active.test_fi
where ref_id = " . $ilDB->quote($ref_id, "integer") . "";

		$result = $ilDB->query($select);

		while ($row = $ilDB->fetchAssoc($result)) {
			$participants = $row['count(active_id)'];
		}

		return $participants;
	}

	public function getTstidforRefid($ref_id){
		global $ilDB;

		$select = "select test_id from object_data
inner join object_reference on object_data.obj_id = object_reference.obj_id
inner join tst_tests on object_data.obj_id = tst_tests.obj_fi
where ref_id = ". $ilDB->quote($ref_id, "integer") ."";

		$result = $ilDB->query($select,"integer");

		while ($row = $ilDB->fetchAssoc($result)){
			$tst_id = $row['test_id'];
		}

		return $tst_id;


	}


	public function getAverageProccessingTime($tst_id){
		global $ilDB;

		$result = $ilDB->queryF("SELECT tst_times.* FROM tst_active, tst_times WHERE tst_active.test_fi = %s AND tst_active.active_id = tst_times.active_fi",
			array('integer'),
			array($tst_id)
		);
		$times = array();
		while ($row = $ilDB->fetchObject($result))
		{
			preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/", $row->started, $matches);
			$epoch_1 = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
			preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/", $row->finished, $matches);
			$epoch_2 = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
			$times[$row->active_fi] += ($epoch_2 - $epoch_1);
		}
		$max_time = 0;
		$counter = 0;
		foreach ($times as $key => $value)
		{
			$max_time += $value;
			$counter++;
		}
		if ($counter)
		{
			$average_time = round($max_time / $counter);
		}
		else
		{
			$average_time = 0;
		}
		return $average_time;
	}
}