<?php

/**
 * My Account Notifications endpoint content.
 *
 * Lists notifications targeted at the current user (either via "send to all"
 * or a per-user pick) and marks the visible set as read in user meta so the
 * sidebar badge clears on next page load.
 *
 * @package WooCommerce\Templates
 */

defined('ABSPATH') || exit;

use Cyan\Theme\Classes\Notifications;

$current_user_id = get_current_user_id();
$notifications = Notifications::getNotificationsForUser($current_user_id);
$read_map = Notifications::getReadMapForUser($current_user_id);

$visible_ids = array_map(
    static function (\WP_Post $notification): int {
        return (int) $notification->ID;
    },
    $notifications
);

if (!empty($visible_ids)) {
    Notifications::markAllReadForUser($visible_ids, $current_user_id);
}
?>

<h2 class="text-2xl md:text-4xl font-[dinar] mb-3 text-gray-900">اعلان ها</h2>

<section class="notifications-account-page bg-white rounded-2xl p-4 md:p-6 lg:p-8">
    <?php if (!empty($notifications)) : ?>
        <ul class="flex flex-col gap-3 md:gap-4">
            <?php foreach ($notifications as $notification) :
                $notification_id = (int) $notification->ID;
                $title = get_the_title($notification_id);
                $message_html = (string) get_post_meta($notification_id, Notifications::META_MESSAGE, true);
                $published_ts = (int) get_post_time('U', true, $notification_id);
                $relative_time = $published_ts > 0
                    ? sprintf(
                        /* translators: %s: human-readable time difference */
                        esc_html__('%s پیش', 'taghechian'),
                        human_time_diff($published_ts, current_time('timestamp', true))
                    )
                    : '';
                $is_new_for_user = !isset($read_map[$notification_id]);
            ?>
                <li
                    class="relative flex flex-col gap-2 rounded-2xl border border-cynBlack/10 bg-white px-4 py-4 md:px-5 md:py-5 transition-colors duration-200 <?php echo $is_new_for_user ? 'bg-yellow-50/60 border-yellow-300' : ''; ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <?php if ($is_new_for_user) : ?>
                                <span class="inline-block size-2 rounded-full bg-yellow-500 shrink-0" aria-hidden="true"></span>
                                <span class="sr-only"><?php esc_html_e('اعلان جدید', 'taghechian'); ?></span>
                            <?php endif; ?>
                            <h3 class="text-base md:text-lg font-[dinar] text-cynBlack truncate">
                                <?php echo esc_html($title); ?>
                            </h3>
                        </div>
                        <?php if ($relative_time !== '') : ?>
                            <time class="shrink-0 text-xs md:text-sm text-cynBlack/60" datetime="<?php echo esc_attr(get_post_time('c', true, $notification_id)); ?>">
                                <?php echo esc_html($relative_time); ?>
                            </time>
                        <?php endif; ?>
                    </div>

                    <?php if ($message_html !== '') : ?>
                        <div class="notification-message text-sm md:text-base leading-7 text-cynBlack/80 [&_a]:text-cynBlue">
                            <?php echo wp_kses_post(apply_filters('the_content', $message_html)); ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div class="min-h-[220px] flex flex-col items-center justify-center gap-3 text-center">
            <div class="size-14 rounded-full bg-yellow-100 text-yellow-500 flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <p class="text-gray-500"><?php esc_html_e('در حال حاضر اعلانی برای شما ثبت نشده است.', 'taghechian'); ?></p>
        </div>
    <?php endif; ?>
</section>