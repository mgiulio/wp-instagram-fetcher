# About

A PHP class to fetch the latest shots from an Instagram account using the WordPress HTTP API, without OAuth access tokens.

# Usage

No need of OAuth tokens, only the Instagram client_id.

Shots are cached via WP transients.

Exceptions are thrown is something goes wrong.

Example code:

```php
funtion get_ig_shots() {
	$account = array(
		'username' => 'username',
		'user_id' => 9999999,
		'client_id' => 'yourclientidhere'
	);

	include 'Instagram_Fetcher.php';

	$ig = new Instagram_Fetcher(array(
		'account' => $account,
		'page_size' => 10, // How many shots to retrieve
		'fetch_period' => 1 // The transient expiration time, expressed in hours
	));

	add_filter('ig_fetcher_latest_shots_filter', 'ig_fetcher_latest_shots_filter');
	// also available the filter "ig_fetcher_$username_latest_shots_filter"

	try {
		$shots = $ig->get_latest_shots();
	}
	catch(Exception $e) {
		trigger_error('Cannot fetch ig shots: ' . $e->getMessage());
		$shots = array();
	}

	return $shots;
}

// An example filter to pluck (and cache) only the shot info you need
function ig_fetcher_latest_shots_filter($shots) {
	$filtered_shots = array();
		
	foreach ($shots as $shot) {
		$filtered_shot = array();
		
		$filtered_shot['img_url'] = $shot['images']['thumbnail']['url']; //  Here you could pick standard or low resolution images sizes instead
		$filtered_shot['url'] = $shot['link'];
		$filtered_shot['desc'] = $shot['caption']['text'];
		
		$filtered_shots[] = $filtered_shot;
	}
		
	return $filtered_shots;
}
```

Code was adapted from the [WDS-Instagram](https://github.com/WebDevStudios/WDS-Instagram) WordPress plugin, described in [this article](http://webdevstudios.com/2014/08/21/using-apis-to-integrate-with-wordpress/).

		
