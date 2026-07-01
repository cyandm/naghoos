<?php

$args              = get_query_var('args', []);
$term_id           = ! empty($args['term_id']) ? (int) $args['term_id'] : 0;
$layout            = ($args['layout'] ?? 'compact') === 'featured' ? 'featured' : 'compact';
$mobile_horizontal = ! empty($args['mobile_horizontal']);

$term = get_term($term_id, 'product_cat');

if (! $term || is_wp_error($term)) {
    return;
}

$term_link = get_term_link($term);

if (is_wp_error($term_link)) {
    return;
}

$description  = (string) get_field('product_category_description', 'product_cat_' . $term_id);
$color        = (string) get_field('product_category_color', 'product_cat_' . $term_id);
$background   = $color !== '' ? $color : '#6b7280';
$thumbnail_id = (int) get_term_meta($term_id, 'thumbnail_id', true);

$image_markup = '';

if ($thumbnail_id) {
    $image_markup = wp_get_attachment_image($thumbnail_id, 'full', false, [
        'class' => 'h-full w-auto max-h-full object-contain drop-shadow-lg rounded-md',
    ]);
}
?>

<?php if ($layout === 'featured') : ?>
    <a href="<?php echo esc_url($term_link); ?>" class="group flex h-[400px] flex-col items-center gap-4 overflow-hidden p-5 text-center no-underline transition-opacity duration-300 hover:opacity-95 lg:flex-row lg:items-center lg:text-start lg:gap-4 lg:p-6" style="background-color: <?php echo esc_attr($background); ?>;">
        <?php if ($image_markup) : ?>
            <div class="order-1 flex h-[197px] lg:h-[320px] shrink-0 items-center justify-center lg:order-2 lg:w-[45%]">
                <?php echo $image_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>
            </div>
        <?php endif; ?>

        <div class="order-2 flex min-w-0 flex-1 flex-col items-center justify-center gap-3 text-white lg:order-1 lg:items-start">
            <h3 class="text-3xl font-bold leading-tight lg:text-4xl">
                <?php echo esc_html($term->name); ?>
            </h3>

            <?php if ($description !== '') : ?>
                <p class="text-base font-medium leading-7 text-white/90 lg:text-2xl">
                    <?php echo esc_html($description); ?>
                </p>
            <?php endif; ?>

            <span class="mt-1 inline-flex w-fit items-center rounded-full bg-white px-5 py-2 text-sm font-medium text-cynBlack transition-colors duration-300 group-hover:bg-white/90">
                <?php esc_html_e('مشاهده همه', 'naghoos'); ?>
            </span>
        </div>
    </a>
<?php elseif ($mobile_horizontal) : ?>
    <a href="<?php echo esc_url($term_link); ?>" class="group flex h-[270px] lg:h-[400px] flex-row-reverse items-center justify-end gap-4 overflow-hidden px-5 py-6 no-underline transition-opacity duration-300 hover:opacity-95 lg:flex-col lg:items-center lg:justify-center" style="background-color: <?php echo esc_attr($background); ?>;">
        <h3 class="text-2xl font-bold text-white lg:text-center lg:text-3xl">
            <?php echo esc_html($term->name); ?>
        </h3>

        <?php if ($image_markup) : ?>
            <div class="flex shrink-0 items-center justify-center lg:flex-1 h-[173px] lg:h-[246px]">
                <?php echo $image_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>
            </div>
        <?php endif; ?>
    </a>
<?php else : ?>
    <a href="<?php echo esc_url($term_link); ?>" class="group flex h-[270px] lg:h-[400px] flex-col items-center justify-center overflow-hidden gap-3 lg:gap-6 px-4 py-6 no-underline transition-opacity duration-300 hover:opacity-95" style="background-color: <?php echo esc_attr($background); ?>;">
        <h3 class="text-center text-2xl font-bold text-white lg:text-3xl">
            <?php echo esc_html($term->name); ?>
        </h3>

        <?php if ($image_markup) : ?>
            <div class="flex flex-1 items-center justify-center h-[173px] lg:h-[246px]">
                <?php echo $image_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>
            </div>
        <?php endif; ?>
    </a>
<?php endif; ?>