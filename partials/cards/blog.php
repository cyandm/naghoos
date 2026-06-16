<?php

use Cyan\Theme\Helpers\Icon;

$args       = get_query_var('args', []);
$post_id    = ! empty($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
$layout     = ($args['layout'] ?? 'horizontal') === 'vertical' ? 'vertical' : 'horizontal';
$responsive = array_key_exists('responsive', $args) ? (bool) $args['responsive'] : true;
$fixed      = ! empty($args['fixed']);

if (! $post_id) {
    return;
}

$author_id   = (int) get_post_field('post_author', $post_id);
$author_name = get_the_author_meta('display_name', $author_id);

$is_horizontal = $layout === 'horizontal';
$fixed         = $fixed && $is_horizontal;

$card_classes = 'blog-card group flex w-full items-stretch';

if ($responsive) {
    $card_classes .= $is_horizontal
        ? ' flex-row gap-2 md:flex-col md:gap-3'
        : ' flex-col gap-3 md:flex-row md:gap-2';
} else {
    $card_classes .= $is_horizontal ? ' flex-row gap-2' : ' flex-col gap-3';
}

if ($fixed) {
    $card_classes .= $responsive ? ' max-md:h-[160px] max-md:w-full max-md:justify-center' : ' h-[160px] w-full justify-center';
}

$image_wrapper_classes = 'overflow-hidden';

if ($responsive) {
    $image_wrapper_classes .= $is_horizontal ? ' w-[45%] md:w-full' : ' w-full md:w-[45%]';
} else {
    $image_wrapper_classes .= $is_horizontal ? ' w-[45%]' : ' w-full';
}

$meta_classes = 'flex items-center text-xs font-medium text-cynBlack/40 w-full justify-start gap-6';

$thumbnail_classes   = 'h-full w-full object-cover object-center transition-transform duration-300 group-hover:scale-105';
$placeholder_classes = 'flex h-full w-full items-center justify-center bg-cynBgItem';
?>

<a href="<?php echo esc_url(get_the_permalink($post_id)); ?>" class="<?php echo esc_attr($card_classes); ?>">
    <div class="<?php echo esc_attr($image_wrapper_classes); ?>">
        <?php if (has_post_thumbnail($post_id)) : ?>
            <?php echo get_the_post_thumbnail($post_id, 'full', [
                'class' => $thumbnail_classes,
            ]); ?>
        <?php else : ?>
            <div class="<?php echo esc_attr($placeholder_classes); ?>">
                <span class="text-sm font-medium text-cynBlack/40"><?php esc_html_e('بدون تصویر', 'naghoos'); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="flex flex-col justify-center gap-4 flex-1">
        <h3 class="line-clamp-2 text-base font-semibold text-cynBlack">
            <?php echo esc_html(get_the_title($post_id)); ?>
        </h3>

        <div class="<?php echo esc_attr($meta_classes); ?>">
            <div class="flex items-center gap-1">
                <i class="size-4 stroke-[1.5]">
                    <?php Icon::print('User,-Profile'); ?>
                </i>
                <span><?php echo esc_html($author_name); ?></span>
            </div>

            <div class="flex items-center gap-1">
                <i class="size-4 stroke-[1.5]">
                    <?php Icon::print('calendar-schedule-1-1'); ?>
                </i>
                <span><?php echo esc_html(get_the_date('', $post_id)); ?></span>
            </div>
        </div>
    </div>
</a>