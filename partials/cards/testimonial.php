<?php

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

            <span class="flex items-center gap-0.5" aria-label="<?php echo esc_attr(sprintf(__('امتیاز %d از 5', 'taghechian'), $rate)); ?>">
                <?php for ($i = 1; $i <= 5; $i++) :
                    $fill = $i <= $rate ? '#fecf00' : '#E0E0E0';
                ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 18 18" aria-hidden="true">
                        <path d="M16.963,6.786c-.088-.271-.323-.469-.605-.51l-4.62-.671L9.672,1.418c-.252-.512-1.093-.512-1.345,0l-2.066,4.186-4.62,.671c-.282,.041-.517,.239-.605,.51-.088,.271-.015,.57,.19,.769l3.343,3.258-.79,4.601c-.048,.282,.067,.566,.298,.734,.231,.167,.538,.189,.79,.057l4.132-2.173,4.132,2.173c.11,.058,.229,.086,.349,.086,.155,0,.31-.048,.441-.143,.231-.168,.347-.452,.298-.734l-.79-4.601,3.343-3.258c.205-.199,.278-.498,.19-.769Z" fill="<?php echo esc_attr($fill); ?>" />
                    </svg>
                <?php endfor; ?>
            </span>
        </div>
    <?php endif; ?>
</div>