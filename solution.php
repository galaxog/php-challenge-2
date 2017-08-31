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

		$date_test = date_parse($out[':date']);

		if ($out[':user_id'] && $out[':score'] && $out[':date'] && $date_test && !$date_test['errors']) {
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
}

function dates_with_at_least_n_scores($pdo, $n)
{
    // YOUR CODE GOES HERE
}

function users_with_top_score_on_date($pdo, $date)
{
    // YOUR CODE GOES HERE
}

function times_user_beat_overall_daily_average($pdo, $user_id)
{
    // YOUR CODE GOES HERE
}
