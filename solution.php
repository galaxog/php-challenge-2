<?php
// YOUR NAME AND EMAIL GOES HERE
//Galax Womack galaxw@gmail.com

function parse_request($request, $secret)
{
    // YOUR CODE GOES HERE
	//DECODE REQUEST AND VALIDATE SIGNATURE

	$out = array();

	$init = explode('.', strtr($request, '-_', '+/'));
	$decoded_sig = base64_decode($init[0]);
	$decoded = base64_decode($init[1]);
	$json_decoded = json_decode($decoded, true);
	$json_size = sizeof($json_decoded);

	//SETUP OUTPUT ARRAY
	if ($json_size === 3) {

		foreach ($json_decoded as $key => $value) {

			$out[':' . $key] = $value;

		}

		$date_test = date_parse(@$out[':date']);

		if (@$out[':user_id'] && @$out[':score'] && @$out[':date'] && $date_test && !$date_test['errors']) {
			return (check_signature($decoded, $decoded_sig, $secret)) ? $out : false;
		} else {
			return false;
		}

	} else {
		return (is_int($json_decoded)) ? $json_decoded : false;
	}

}

function check_signature($payload, $signature, $secret=API_SECRET) {
	//CONFIRM SIGNATURE
	$out = (hash_hmac('sha256', $payload, $secret, true) == $signature) ? true : false;

	return $out;
}

function total_number_of_valid_requests($pdo)
{
    // YOUR CODE GOES HERE
	//GET TOTAL NUMBER OF RECORDS IN TABLE

	$sql = "SELECT COUNT(*) FROM scores";

	$res = $pdo->query($sql)->fetch(PDO::FETCH_BOTH);

	return $res[0];

}

function dates_with_at_least_n_scores($pdo, $n=0)
{
    // YOUR CODE GOES HERE
	//GET DATES FROM SCORES TABLE THAT HAVE AT LEAST $n SCORES;

	$out = array();

	$sql = "SELECT date AS d, COUNT(*) AS c FROM scores 
			GROUP BY date 
			ORDER BY date DESC";

	foreach ($pdo->query($sql) as $row) {

		if ($row['c'] >= $n) {

			$out[] = $row['d'];

		}

	}

	return $out;
}

function users_with_top_score_on_date($pdo, $date)
{
    // YOUR CODE GOES HERE
	//GET THE USERS WITH THE TOP 3 SCORES FOR A SPECIFIED DATE

	$out = array();

	$sql = "SELECT user_id 
			FROM scores 
			WHERE date = '$date' 
			ORDER BY score DESC 
			LIMIT 3";

	foreach($pdo->query($sql) as $row) {
		$out[] = $row['user_id'];
	}

	return $out;

}

function times_user_beat_overall_daily_average($pdo, $user_id)
{
    // YOUR CODE GOES HERE
	//GET THE NUMBER OF TIMES THE SPECIFIED USER SCORED ABOVE THE DAILY AVERAGE FOR DAYS WHERE THE USER ACTUALLY HAS A SCORE

	$out = 0;

	//GET THE OVERALL DAILY AVERAGE
	$sql = "SELECT AVG(score) AS a, date AS d 
			FROM scores 
			GROUP BY date ";

	foreach($pdo->query($sql) as $row) {

		//CHECK THEIR HIGHEST SCORE FOR EACH DAY
		$sql1 = "SELECT AVG(score) 
				FROM scores 
				WHERE user_id = $user_id 
				AND `date` = '".$row['d']."' 
				GROUP BY date";

		$row2 = $pdo->query($sql1)->fetch(PDO::FETCH_BOTH);

		if ((int)$row['a'] < $row2[0]) {
			$out ++;
		}

	}

	return $out;

}
