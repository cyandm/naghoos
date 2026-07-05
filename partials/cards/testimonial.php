<?php

use Cyan\Theme\Helpers\StarRating;

$args            = get_query_var('args', []);
$testimonial_id  = !empty($args['testimonial']) ? (int) $args['testimonial'] : get_the_ID();

if (!$testimonial_id) {
    return;
}

$title   = get_the_title($testimonial_id);
$content = get_post_field('post_content', $testimonial_id);
$name    = get_field('testimonial_name', $testimonial_id);
$rate_raw = get_field('testimonial_rate', $testimonial_id);
$rate    = ($rate_raw === '' || $rate_raw === null || $rate_raw === false) ? 5 : min(5, max(1, (int) $rate_raw));

if ($title === '' && $content === '') {
    return;
}
?>

<div class="bg-white rounded-3xl shadow-cart p-5 flex flex-col gap-4 text-right h-full w-full">
    <?php if ($title !== '') : ?>
        <h3 class="text-base font-medium text-cynBlack">
            <?php echo esc_html($title); ?>
        </h3>
    <?php endif; ?>

    <?php if ($content !== '') : ?>
        <div class="text-cynBlack/80 text-sm font-normal leading-relaxed flex-1">
            <?php echo wp_kses_post(apply_filters('the_content', $content)); ?>
        </div>
    <?php endif; ?>

    <?php if ($name !== '' || $rate > 0) : ?>
        <div class="flex flex-col gap-1">
            <?php if ($name !== '') : ?>
                <p class="text-sm text-cynBlack/50">
                    <?php echo esc_html($name); ?>
                </p>
            <?php endif; ?>

            <?php
            StarRating::echo((float) $rate, [
                'id_prefix'    => 'testimonialStar' . $testimonial_id,
                'class'        => 'flex items-center gap-0.5',
                'aria_label'   => sprintf(__('امتیاز %d از 5', 'naghoos'), $rate),
                'stroke_color' => '#1E1311',
            ]);
            ?>
        </div>
    <?php endif; ?>
</div>