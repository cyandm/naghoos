<?php

/**
 * Rest API
 * this class is used to register rest routes and handle requests
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

use WP_REST_Request;
use WP_REST_Response;

class Rest
{

	protected static $namespace = 'cyn/v1';

	public static function init()
	{
		add_action('rest_api_init', [__CLASS__, 'registerRoutes']);
	}

	public static function registerRoutes()
	{
		self::makeRoute('/contact_form', 'POST', [__CLASS__, 'createForm']);
		self::makeRoute('/customer_club_form', 'POST', [__CLASS__, 'createCustomerClubForm']);
		self::makeRoute('/support_form', 'POST', [__CLASS__, 'createSupportForm']);
	}

	public static function createForm(WP_REST_Request $request)
	{
		$rate_limit = self::checkRateLimit('cyn_contact_');
		if ($rate_limit instanceof WP_REST_Response) {
			return $rate_limit;
		}

		$body = $request->get_body_params();

		$name = isset($body['name']) ? sanitize_text_field($body['name']) : '';
		$email = isset($body['email']) ? sanitize_email($body['email']) : '';
		$phone = isset($body['phone']) ? sanitize_text_field($body['phone']) : '';
		$message = isset($body['message']) ? sanitize_textarea_field($body['message']) : '';

		// Validate required fields (email is now optional)
		if (empty($phone) || empty($name) || empty($message)) {
			return new WP_REST_Response(['error' => 'نام، شماره تلفن و پیام الزامی هستند'], 400);
		}

		// Validate phone number (Iranian format)
		if (!preg_match('/^[0-9]{11}$/', $phone)) {
			return new WP_REST_Response(['error' => 'شماره تلفن معتبر نیست'], 400);
		}

		// Validate email format if provided
		if (!empty($email) && !is_email($email)) {
			return new WP_REST_Response(['error' => 'فرمت ایمیل معتبر نیست'], 400);
		}

		$new_post = wp_insert_post([
			'post_type' => 'contact_form',
			'post_title' => $name,
			'post_status' => 'private',
			'meta_input' => [
				'_name' => $name,
				'_phone' => $phone,
				'_email' => $email,
				'_message' => $message,
			]
		]);

		if (is_wp_error($new_post)) {
			return new WP_REST_Response(['error' => 'خطا در ثبت فرم، لطفاً دوباره تلاش کنید'], 500);
		}

		self::recordRateLimit('cyn_contact_');

		return new WP_REST_Response(['message' => 'فرم با موفقیت ارسال شد'], 200);
	}

	public static function createCustomerClubForm(WP_REST_Request $request)
	{
		$rate_limit = self::checkRateLimit('cyn_club_');
		if ($rate_limit instanceof WP_REST_Response) {
			return $rate_limit;
		}

		$body  = $request->get_body_params();
		$phone = isset($body['phone']) ? sanitize_text_field($body['phone']) : '';

		if (empty($phone)) {
			return new WP_REST_Response(['error' => 'شماره تلفن الزامی است'], 400);
		}

		if (!preg_match('/^[0-9]{11}$/', $phone)) {
			return new WP_REST_Response(['error' => 'شماره تلفن معتبر نیست'], 400);
		}

		$new_post = wp_insert_post([
			'post_type'   => 'customer_club_form',
			'post_title'  => $phone,
			'post_status' => 'private',
			'meta_input'  => [
				'_phone' => $phone,
			],
		]);

		if (is_wp_error($new_post)) {
			return new WP_REST_Response(['error' => 'خطا در ثبت فرم، لطفاً دوباره تلاش کنید'], 500);
		}

		self::recordRateLimit('cyn_club_');

		return new WP_REST_Response(['message' => 'عضویت با موفقیت ثبت شد'], 200);
	}

	public static function createSupportForm(WP_REST_Request $request)
	{
		$rate_limit = self::checkRateLimit('cyn_support_');
		if ($rate_limit instanceof WP_REST_Response) {
			return $rate_limit;
		}

		$body    = $request->get_body_params();
		$name    = isset($body['name']) ? sanitize_text_field($body['name']) : '';
		$phone   = isset($body['phone']) ? sanitize_text_field($body['phone']) : '';
		$message = isset($body['message']) ? sanitize_textarea_field($body['message']) : '';

		if (empty($name) || empty($phone) || empty($message)) {
			return new WP_REST_Response(['error' => 'نام، شماره تلفن و پیام الزامی هستند'], 400);
		}

		if (!preg_match('/^[0-9]{11}$/', $phone)) {
			return new WP_REST_Response(['error' => 'شماره تلفن معتبر نیست'], 400);
		}

		$user_id = get_current_user_id();

		$new_post = wp_insert_post([
			'post_type'   => 'support_form',
			'post_title'  => $name,
			'post_status' => 'private',
			'post_author' => $user_id > 0 ? $user_id : 0,
			'meta_input'  => [
				'_name'    => $name,
				'_phone'   => $phone,
				'_message' => $message,
				'_user_id' => $user_id > 0 ? $user_id : '',
			],
		]);

		if (is_wp_error($new_post)) {
			return new WP_REST_Response(['error' => 'خطا در ثبت فرم، لطفاً دوباره تلاش کنید'], 500);
		}

		self::recordRateLimit('cyn_support_');

		return new WP_REST_Response(['message' => 'پیام پشتیبانی با موفقیت ارسال شد'], 200);
	}

	/**
	 * @return WP_REST_Response|null
	 */
	private static function checkRateLimit($prefix)
	{
		$ip           = self::getClientIp();
		$min_interval = 120;
		$rate_key     = $prefix . 'last_' . md5($ip);
		$last_time    = get_transient($rate_key);

		if ($last_time !== false && (time() - $last_time) < $min_interval) {
			$wait = $min_interval - (time() - $last_time);
			return new WP_REST_Response([
				'error' => sprintf(__('لطفاً %d ثانیه صبر کنید و دوباره تلاش کنید.', 'naghoos'), $wait),
			], 429);
		}

		$max_per_hour = 2;
		$count_key    = $prefix . 'count_' . md5($ip);
		$count_data   = get_transient($count_key);

		if ($count_data === false) {
			$count_data = ['count' => 0, 'start' => time()];
		}

		if ($count_data['count'] >= $max_per_hour) {
			return new WP_REST_Response([
				'error' => __('تعداد ارسال‌های شما در این ساعت به حد مجاز رسیده. لطفاً بعداً تلاش کنید.', 'naghoos'),
			], 429);
		}

		return null;
	}

	private static function recordRateLimit($prefix)
	{
		$ip           = self::getClientIp();
		$min_interval = 120;
		$rate_key     = $prefix . 'last_' . md5($ip);
		$count_key    = $prefix . 'count_' . md5($ip);
		$count_data   = get_transient($count_key);

		if ($count_data === false) {
			$count_data = ['count' => 0, 'start' => time()];
		}

		set_transient($rate_key, time(), $min_interval);
		$count_data['count']++;
		set_transient($count_key, $count_data, 3600);
	}


	/**
	 * Get client IP address
	 * @return string
	 */
	private static function getClientIp()
	{
		$ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

		foreach ($ip_keys as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					$ip = trim($ip);
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
						return $ip;
					}
				}
			}
		}

		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
	}

	/**
	 * make route
	 * @param string $route route path
	 * @param string $methods GET, POST, PUT, DELETE, etc.
	 * @param callable $callback callback function
	 * @param callable $permission_callback permission callback function
	 * @return void
	 */
	private static function makeRoute($route, $methods, $callback, $permission_callback = '__return_true')
	{
		register_rest_route(self::$namespace, $route, [
			'methods' => $methods,
			'callback' => $callback,
			'permission_callback' => $permission_callback
		]);
	}
}
