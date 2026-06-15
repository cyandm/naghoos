<?php

/**
 * Post views counter
 *
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

class ViewsCount
{
    public const META_KEY = 'post_views_count';

    public static function init(): void
    {
        add_action('wp', [self::class, 'maybeIncreaseViews']);
    }

    public static function maybeIncreaseViews(): void
    {
        if (! is_single()) {
            return;
        }

        $post_id = get_the_ID();

        if ($post_id) {
            self::setPostViews((int) $post_id);
        }
    }

    public static function setPostViews(int $post_id): void
    {
        if (is_user_logged_in() || wp_doing_ajax()) {
            return;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (preg_match('/bot|crawl|slurp|spider/i', $user_agent)) {
            return;
        }

        $cookie_name = 'post_view_' . $post_id;
        $today = date('Y-m-d');

        if (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] === $today) {
            return;
        }

        setcookie($cookie_name, $today, time() + DAY_IN_SECONDS, '/');

        $count = get_post_meta($post_id, self::META_KEY, true);

        if ($count === '') {
            delete_post_meta($post_id, self::META_KEY);
            add_post_meta($post_id, self::META_KEY, '1');
            return;
        }

        update_post_meta($post_id, self::META_KEY, (int) $count + 1);
    }

    public static function getBlogPostType(): string
    {
        return post_type_exists('blog') ? 'blog' : 'post';
    }

    /**
     * @param array<int|string>|int|string|null $selected
     * @return list<int>
     */
    public static function resolveWeeklyBests($selected, int $limit = 5, ?string $post_type = null): array
    {
        $limit = max(1, $limit);

        if (! is_array($selected)) {
            $selected = $selected ? [(int) $selected] : [];
        }

        $selected_ids = [];

        foreach ($selected as $post_id) {
            $post_id = (int) $post_id;

            if ($post_id > 0) {
                $selected_ids[] = $post_id;
            }
        }

        $selected_ids = array_slice(array_values(array_unique($selected_ids)), 0, $limit);
        $remaining = $limit - count($selected_ids);

        if ($remaining <= 0) {
            return $selected_ids;
        }

        $fallback_ids = self::getMostViewedLastWeek($remaining, $post_type, $selected_ids);

        if (count($fallback_ids) < $remaining) {
            $still_needed = $remaining - count($fallback_ids);
            $exclude_ids = array_merge($selected_ids, $fallback_ids);
            $newest_ids = self::getNewestPosts($still_needed, $post_type, $exclude_ids);
            $fallback_ids = array_merge($fallback_ids, $newest_ids);
        }

        return array_merge($selected_ids, $fallback_ids);
    }

    /**
     * @param list<int> $exclude_ids
     * @return list<int>
     */
    public static function getNewestPosts(int $limit = 5, ?string $post_type = null, array $exclude_ids = []): array
    {
        $limit = max(1, $limit);
        $post_type = $post_type ?: self::getBlogPostType();
        $exclude_ids = array_values(array_filter(array_map('intval', $exclude_ids)));

        $query_args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'fields' => 'ids',
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        if (! empty($exclude_ids)) {
            $query_args['post__not_in'] = $exclude_ids;
        }

        $query = new \WP_Query($query_args);

        return array_map('intval', $query->posts);
    }

    /**
     * @param list<int> $exclude_ids
     * @return list<int>
     */
    public static function getMostViewedLastWeek(int $limit = 5, ?string $post_type = null, array $exclude_ids = []): array
    {
        $limit = max(1, $limit);
        $post_type = $post_type ?: self::getBlogPostType();
        $exclude_ids = array_values(array_filter(array_map('intval', $exclude_ids)));

        $query_args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'fields' => 'ids',
            'meta_key' => self::META_KEY,
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'date_query' => [
                [
                    'after' => '1 week ago',
                    'inclusive' => true,
                ],
            ],
            'meta_query' => [
                [
                    'key' => self::META_KEY,
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC',
                ],
            ],
        ];

        if (! empty($exclude_ids)) {
            $query_args['post__not_in'] = $exclude_ids;
        }

        $query = new \WP_Query($query_args);

        return array_map('intval', $query->posts);
    }
}
