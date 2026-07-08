<?php

/**
 * Notifications
 *
 * Backs the My Account "اعلان ها" page. Admins create posts in the
 * `notification` CPT and target either all users or a specific list of
 * users via ACF fields. Read state is tracked per user in user meta.
 *
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

class Notifications
{
    /**
     * WooCommerce My Account endpoint slug.
     *
     * @var string
     */
    public const ENDPOINT = 'notifications';

    /**
     * Post type backing notifications.
     *
     * @var string
     */
    public const POST_TYPE = 'notification';

    /**
     * ACF meta key for the "send to all users" boolean flag.
     *
     * @var string
     */
    public const META_SEND_TO_ALL = 'notification_send_to_all';

    /**
     * ACF meta key storing the serialized array of target user IDs.
     *
     * @var string
     */
    public const META_TARGET_USERS = 'notification_target_users';

    /**
     * ACF meta key for the rich-text message body.
     *
     * @var string
     */
    public const META_MESSAGE = 'notification_message';

    /**
     * User meta key storing the per-user read map: [notification_id => read_timestamp].
     *
     * @var string
     */
    public const USER_META_READ_MAP = '_cyn_notification_reads';

    /**
     * Wire up hooks.
     *
     * @return void
     */
    public static function init(): void
    {
        add_action('init', [__CLASS__, 'registerEndpoint']);
        add_filter('woocommerce_account_menu_items', [__CLASS__, 'filterAccountMenuItems'], 20);
        add_action('woocommerce_account_' . self::ENDPOINT . '_endpoint', [__CLASS__, 'renderEndpointContent']);

        add_filter('manage_' . self::POST_TYPE . '_posts_columns', [__CLASS__, 'adminColumns']);
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', [__CLASS__, 'renderAdminColumn'], 10, 2);

        add_filter('acf/fields/user/query', [__CLASS__, 'filterUserPickerQuery'], 10, 3);
    }

    /**
     * Register the My Account `notifications` endpoint.
     *
     * Permalinks must be flushed once (Settings → Permalinks → Save) for the
     * new endpoint URL to resolve.
     *
     * @return void
     */
    public static function registerEndpoint(): void
    {
        add_rewrite_endpoint(self::ENDPOINT, EP_ROOT | EP_PAGES);
    }

    /**
     * Add "اعلان ها" to WooCommerce's account menu items so the endpoint is recognized.
     * Our sidebar in `myaccount/navigation.php` renders its own list, so the visible
     * order/label there is independent — this filter exists so WC treats the endpoint
     * as a first-class account page (e.g., title and body class).
     *
     * @param array<string, string> $items
     * @return array<string, string>
     */
    public static function filterAccountMenuItems(array $items): array
    {
        $reordered = [];
        foreach ($items as $key => $label) {
            $reordered[$key] = $label;
            if ($key === 'orders') {
                $reordered[self::ENDPOINT] = __('اعلان ها', 'naghoos');
            }
        }

        if (!isset($reordered[self::ENDPOINT])) {
            $reordered[self::ENDPOINT] = __('اعلان ها', 'naghoos');
        }

        return $reordered;
    }

    /**
     * Render the endpoint template and mark visible notifications as read.
     *
     * @return void
     */
    public static function renderEndpointContent(): void
    {
        wc_get_template('myaccount/notifications.php');
    }

    /**
     * Whether "send to all" was explicitly turned off (ACF false branch).
     *
     * @param mixed $raw
     * @return bool
     */
    private static function isSendToAllExplicitlyOff($raw): bool
    {
        if ($raw === false || $raw === 0 || $raw === '0' || $raw === 'false' || $raw === 'off') {
            return true;
        }

        return false;
    }

    /**
     * Whether post meta for "send to all" is truthy (ACF true_false can be stored
     * as string "1", int 1, or occasionally missing when defaults apply).
     *
     * @param mixed $raw
     * @return bool
     */
    private static function isSendToAllMetaTruthy($raw): bool
    {
        if ($raw === true || $raw === 1 || $raw === '1' || $raw === 'true' || $raw === 'on') {
            return true;
        }

        return false;
    }

    /**
     * Normalize ACF user field meta to a list of user IDs (handles int[], string[],
     * single int, or JSON-like string from edge cases).
     *
     * @param mixed $raw
     * @return int[]
     */
    private static function normalizeTargetUserIds($raw): array
    {
        if ($raw === '' || $raw === null) {
            return [];
        }

        if (is_int($raw) || (is_string($raw) && ctype_digit($raw))) {
            $id = (int) $raw;
            return $id > 0 ? [$id] : [];
        }

        if (!is_array($raw)) {
            return [];
        }

        $ids = [];
        foreach ($raw as $item) {
            if (is_int($item) || is_float($item)) {
                $id = (int) $item;
            } elseif (is_string($item) && ctype_digit($item)) {
                $id = (int) $item;
            } elseif (is_array($item) && isset($item['ID'])) {
                $id = (int) $item['ID'];
            } else {
                continue;
            }
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Whether a published notification should appear for this user.
     *
     * We evaluate raw post meta in PHP instead of a SQL meta_query because ACF
     * serialization formats and default-value persistence vary; a strict LIKE on
     * `i:USER_ID;` or `= '1'` alone was excluding valid rows.
     *
     * @param int $post_id
     * @param int $user_id
     * @return bool
     */
    public static function isNotificationEligibleForUser(int $post_id, int $user_id): bool
    {
        if ($post_id <= 0 || $user_id <= 0) {
            return false;
        }

        $send_raw = get_post_meta($post_id, self::META_SEND_TO_ALL, true);
        $targets_raw = get_post_meta($post_id, self::META_TARGET_USERS, true);
        $target_ids = self::normalizeTargetUserIds($targets_raw);

        if (self::isSendToAllMetaTruthy($send_raw)) {
            return true;
        }

        if (in_array($user_id, $target_ids, true)) {
            return true;
        }

        // No explicit recipient list and "send to all" was not turned off: treat as
        // broadcast. Covers ACF default-on before meta is written, and legacy rows.
        if (empty($target_ids) && !self::isSendToAllExplicitlyOff($send_raw)) {
            return true;
        }

        return false;
    }

    /**
     * Return all eligible notifications for a user, newest first.
     *
     * @param int $user_id
     * @param int $limit posts_per_page (-1 = unlimited)
     * @return \WP_Post[]
     */
    public static function getNotificationsForUser(int $user_id, int $limit = -1): array
    {
        if ($user_id <= 0) {
            return [];
        }

        $query = new \WP_Query([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
        ]);

        $posts = is_array($query->posts) ? $query->posts : [];
        $eligible = [];
        foreach ($posts as $post) {
            if (!$post instanceof \WP_Post) {
                continue;
            }
            if (self::isNotificationEligibleForUser((int) $post->ID, $user_id)) {
                $eligible[] = $post;
            }
        }

        return $eligible;
    }

    /**
     * Retrieve a user's read map.
     *
     * @param int $user_id
     * @return array<int, int> notification_id => unix timestamp
     */
    public static function getReadMapForUser(int $user_id): array
    {
        if ($user_id <= 0) {
            return [];
        }

        $stored = get_user_meta($user_id, self::USER_META_READ_MAP, true);
        if (!is_array($stored)) {
            return [];
        }

        $normalized = [];
        foreach ($stored as $notification_id => $timestamp) {
            $normalized_id = (int) $notification_id;
            $normalized_ts = (int) $timestamp;
            if ($normalized_id > 0 && $normalized_ts > 0) {
                $normalized[$normalized_id] = $normalized_ts;
            }
        }

        return $normalized;
    }

    /**
     * Whether a specific notification has been read by the user.
     *
     * @param int $notification_id
     * @param int $user_id
     * @return bool
     */
    public static function isReadByUser(int $notification_id, int $user_id): bool
    {
        if ($notification_id <= 0 || $user_id <= 0) {
            return false;
        }

        $read_map = self::getReadMapForUser($user_id);

        return isset($read_map[$notification_id]);
    }

    /**
     * Mark a batch of notifications as read for a user. New entries are merged
     * into the existing read map; already-read entries are preserved.
     *
     * @param int[] $notification_ids
     * @param int   $user_id
     * @return void
     */
    public static function markAllReadForUser(array $notification_ids, int $user_id): void
    {
        if ($user_id <= 0 || empty($notification_ids)) {
            return;
        }

        $read_map = self::getReadMapForUser($user_id);
        $now = time();
        $changed = false;

        foreach ($notification_ids as $raw_id) {
            $notification_id = (int) $raw_id;
            if ($notification_id <= 0) {
                continue;
            }
            if (!isset($read_map[$notification_id])) {
                $read_map[$notification_id] = $now;
                $changed = true;
            }
        }

        if ($changed) {
            update_user_meta($user_id, self::USER_META_READ_MAP, $read_map);
        }
    }

    /**
     * Count unread notifications for a user.
     *
     * @param int $user_id
     * @return int
     */
    public static function getUnreadCountForUser(int $user_id): int
    {
        if ($user_id <= 0) {
            return 0;
        }

        $notifications = self::getNotificationsForUser($user_id);
        if (empty($notifications)) {
            return 0;
        }

        $read_map = self::getReadMapForUser($user_id);
        $unread = 0;

        foreach ($notifications as $notification) {
            if (!isset($read_map[$notification->ID])) {
                $unread++;
            }
        }

        return $unread;
    }

    /**
     * Maximum number of candidates we resolve for the ACF user picker.
     * Kept tight so the dropdown stays snappy on large user tables.
     *
     * @var int
     */
    private const USER_PICKER_LIMIT = 50;

    /**
     * User meta keys searched (in addition to default WP columns) when
     * picking notification recipients in the admin.
     *
     * @var string[]
     */
    private const USER_PICKER_META_KEYS = [
        'billing_phone',
        'phone',
        'first_name',
        'last_name',
        'nickname',
    ];

    /**
     * Extend ACF's user picker search so admins can pick recipients by phone
     * number or first/last name (none of which are in WP's default user
     * search columns). Scoped to the `notification_target_users` field only.
     *
     * @param array<string, mixed> $args
     * @param array<string, mixed> $field
     * @param int|string           $post_id
     * @return array<string, mixed>
     */
    public static function filterUserPickerQuery(array $args, array $field, $post_id): array
    {
        unset($post_id);

        if (!isset($field['name']) || $field['name'] !== self::META_TARGET_USERS) {
            return $args;
        }

        $search_raw = isset($args['search']) ? (string) $args['search'] : '';
        $search = trim($search_raw, " \t\n\r\0\x0B*");
        if ($search === '') {
            return $args;
        }

        global $wpdb;
        $like = '%' . $wpdb->esc_like($search) . '%';

        $name_query = new \WP_User_Query([
            'search' => '*' . $search . '*',
            'search_columns' => ['user_login', 'user_email', 'user_nicename', 'display_name'],
            'fields' => 'ID',
            'number' => self::USER_PICKER_LIMIT,
        ]);
        $name_results = $name_query->get_results();
        $name_ids = is_array($name_results) ? array_map('intval', $name_results) : [];

        $meta_keys = self::USER_PICKER_META_KEYS;
        $placeholders = implode(',', array_fill(0, count($meta_keys), '%s'));
        $params = array_merge($meta_keys, [$like, self::USER_PICKER_LIMIT]);

        $meta_results = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT DISTINCT user_id FROM {$wpdb->usermeta}
                 WHERE meta_key IN ($placeholders)
                 AND meta_value LIKE %s
                 LIMIT %d",
                $params
            )
        );
        $meta_ids = is_array($meta_results) ? array_map('intval', $meta_results) : [];

        $combined = array_values(
            array_unique(
                array_filter(
                    array_merge($name_ids, $meta_ids),
                    static function (int $user_id): bool {
                        return $user_id > 0;
                    }
                )
            )
        );

        if (empty($combined)) {
            $combined = [0];
        }

        $args['include'] = $combined;
        $args['number'] = self::USER_PICKER_LIMIT;
        $args['orderby'] = 'display_name';
        $args['order'] = 'ASC';
        unset($args['search'], $args['search_columns']);

        add_filter('acf/fields/user/result', [__CLASS__, 'formatUserPickerResult'], 10, 4);

        return $args;
    }

    /**
     * Decorate the ACF user picker label with display name + phone so admins
     * can confirm at a glance they're picking the right recipient.
     *
     * @param string         $text
     * @param \WP_User|mixed $user
     * @param array<string, mixed> $field
     * @param int|string     $post_id
     * @return string
     */
    public static function formatUserPickerResult($text, $user, array $field, $post_id): string
    {
        unset($post_id);

        if (!isset($field['name']) || $field['name'] !== self::META_TARGET_USERS) {
            return (string) $text;
        }

        if (!$user instanceof \WP_User) {
            return (string) $text;
        }

        $name_parts = array_values(array_filter([
            trim((string) $user->first_name),
            trim((string) $user->last_name),
        ], static function (string $part): bool {
            return $part !== '';
        }));

        $primary = !empty($name_parts)
            ? implode(' ', $name_parts)
            : trim((string) $user->display_name);

        if ($primary === '') {
            $primary = $user->user_login;
        }

        $phone = (string) get_user_meta((int) $user->ID, 'billing_phone', true);
        if ($phone === '') {
            $phone = (string) get_user_meta((int) $user->ID, 'phone', true);
        }

        if ($phone !== '') {
            return sprintf('%s — %s', $primary, $phone);
        }

        return $primary;
    }

    /**
     * Admin list columns for notifications.
     *
     * @param array<string, string> $columns
     * @return array<string, string>
     */
    public static function adminColumns(array $columns): array
    {
        $new = [];
        foreach ($columns as $key => $label) {
            $new[$key] = $label;
            if ($key === 'title') {
                $new['recipients'] = __('گیرندگان', 'naghoos');
            }
        }

        if (!isset($new['recipients'])) {
            $new['recipients'] = __('گیرندگان', 'naghoos');
        }

        return $new;
    }

    /**
     * Render the recipients column for a notification.
     *
     * @param string $column
     * @param int    $post_id
     * @return void
     */
    public static function renderAdminColumn(string $column, int $post_id): void
    {
        if ($column !== 'recipients') {
            return;
        }

        $send_raw = get_post_meta($post_id, self::META_SEND_TO_ALL, true);
        $target_users_raw = get_post_meta($post_id, self::META_TARGET_USERS, true);
        $target_ids = self::normalizeTargetUserIds($target_users_raw);

        $is_broadcast = self::isSendToAllMetaTruthy($send_raw)
            || (empty($target_ids) && !self::isSendToAllExplicitlyOff($send_raw));

        if ($is_broadcast) {
            echo '<strong>' . esc_html__('همه کاربران', 'naghoos') . '</strong>';
            return;
        }

        if (!is_array($target_users_raw) || empty($target_ids)) {
            echo '<span style="color:#a00;">' . esc_html__('بدون گیرنده', 'naghoos') . '</span>';
            return;
        }

        $count = count($target_ids);
        echo esc_html(sprintf(_n('%d کاربر', '%d کاربر', $count, 'naghoos'), $count));
    }
}
