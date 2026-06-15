<?php

use Cyan\Theme\Helpers\Icon;

$args    = get_query_var('args', []);
$post_id = ! empty($args['post_id']) ? (int) $args['post_id'] : get_the_ID();

if (! $post_id) {
    return;
}

$author_id   = (int) get_post_field('post_author', $post_id);
$author_name = get_the_author_meta('display_name', $author_id);

$thumbnail_classes = 'w-full object-cover object-center transition-transform duration-300 group-hover:scale-105';
$placeholder_classes = 'flex w-full items-center justify-center bg-cynBgItem';
?>

<a href="<?php echo esc_url(get_the_permalink($post_id)); ?>" class="blog-card group flex w-full items-stretch flex-col gap-3">
    <div class="overflow-hidden w-full">
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

    <div class="flex flex-col justify-center gap-4">
        <h3 class="line-clamp-2 text-base font-semibold text-cynBlack">
            <?php echo esc_html(get_the_title($post_id)); ?>
        </h3>

        <div class="flex items-center text-xs font-medium text-cynBlack/40 w-auto justify-start gap-4">
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