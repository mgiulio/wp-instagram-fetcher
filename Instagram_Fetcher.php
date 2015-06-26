<?php

class Instagram_Fetcher {
	
	public function __construct($cfg) {
		$this->cfg = $cfg;
		$this->transient_key = "ig-latest-shots-" . sanitize_title_with_dashes($this->cfg['account']['username']);
	}

	public function get_latest_shots() {
		$shots = get_transient($this->transient_key);
		
		if (!$shots) {
			$shots = $this->fetch_shots();
			$shots = apply_filters("ig_fetcher_latest_shots_filter", $shots['data']);
			$shots = apply_filters("ig_fetcher_{$this->cfg['account']['username']}_latest_shots_filter", $shots);
			set_transient($this->transient_key, $shots, $this->cfg['fetch_period'] * HOUR_IN_SECONDS);
		}
		
		return $shots;
	}
	
	private function fetch_shots() {
		$username = $this->cfg['account']['username'];
		$user_id = $this->cfg['account']['user_id'];
		$client_id = $this->cfg['account']['client_id'];
		$count = $this->cfg['page_size'];
		
		$request_url = 'https://api.instagram.com/v1/users/' . esc_html($user_id ) . '/media/recent/';
		$request_url = add_query_arg(array(
			'client_id' => esc_html($client_id),
			'count'     => absint($count)
		), $request_url);
			
		$response = wp_remote_get($request_url);
		if (is_wp_error($response))
			throw new Exception('HTTP request failed');
		
		if (wp_remote_retrieve_response_code($response) !== 200)
			throw new Exception('HTTP status code is not 200');

		return json_decode(wp_remote_retrieve_body($response), true);
	}
	
}
