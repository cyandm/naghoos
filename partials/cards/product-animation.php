<?php

use Cyan\Theme\Helpers\StarRating;

$args       = get_query_var('args', []);
$product_id = !empty($args['product']) ? (int) $args['product'] : get_the_ID();
$variant    = isset($args['variant']) ? ((int) $args['variant'] % 4) : 0;
$product    = wc_get_product($product_id);

if (!$product) {
    return;
}

$writer = wc_get_product_terms($product_id, 'pa_writer', ['fields' => 'names']);
$product_excert_raw = get_field('product_excert', $product_id);
$product_excert     = !empty($product_excert_raw)
    ? wp_trim_words(wp_strip_all_tags($product_excert_raw), 35)
    : '';

$average_rating = (float) $product->get_average_rating();
$show_rating    = wc_review_ratings_enabled();

$placeholder_src = (function_exists('wc_placeholder_img_src') && wc_placeholder_img_src('full'))
    ? wc_placeholder_img_src('full')
    : get_template_directory_uri() . '/assets/image/woocommerce-placeholder.webp';
?>

<a
    href="<?php echo esc_url(get_permalink($product_id)); ?>"
    class="highlight-card highlight-card--variant-<?php echo esc_attr((string) $variant); ?> group block relative overflow-hidden md:overflow-visible rounded-3xl min-h-[180px] h-full">

    <div class="highlight-card__blobs" aria-hidden="true">
        <span class="highlight-card__blob highlight-card__blob--1"></span>
        <span class="highlight-card__blob highlight-card__blob--2"></span>
        <span class="highlight-card__blob highlight-card__blob--3"></span>
    </div>

    <div class="relative z-10 flex items-center justify-between gap-4 p-5 md:p-6 h-full">
        <div class="highlight-card__image shrink-0 relative z-20 w-[110px] md:w-[130px] my-0 md:-mt-10 md:mb-0 self-center md:self-end">
            <?php if (has_post_thumbnail($product_id)) : ?>
                <?php echo get_the_post_thumbnail($product_id, 'full', [
                    'class' => 'w-full h-auto max-h-[160px] md:max-h-[240px] object-contain drop-shadow-[0_12px_24px_rgba(0,0,0,0.45)] group-hover:scale-[1.02] transition-transform duration-300 rounded-3xl',
                    'alt'   => esc_attr(get_the_title($product_id)),
                ]); ?>
            <?php else : ?>
                <img
                    src="<?php echo esc_url($placeholder_src); ?>"
                    alt="<?php echo esc_attr(get_the_title($product_id)); ?>"
                    class="w-full h-auto max-h-[160px] md:max-h-[240px] object-contain drop-shadow-[0_12px_24px_rgba(0,0,0,0.45)] group-hover:scale-[1.02] transition-transform duration-300 rounded-3xl" />
            <?php endif; ?>
        </div>

        <div class="flex flex-1 min-w-0 flex-col gap-1 text-right">
            <div class="flex flex-col gap-1">
                <p class="text-lg font-semibold text-white line-clamp-1">
                    <?php echo esc_html(get_the_title($product_id)); ?>
                </p>

                <?php if (!empty($writer)) : ?>
                    <span class="text-sm font-medium text-white/60">
                        <?php echo esc_html(implode('، ', $writer)); ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($product_excert !== '') : ?>
                <p class="text-xs font-medium text-white/50 line-clamp-3 leading-6">
                    <?php echo esc_html($product_excert); ?>
                </p>
            <?php endif; ?>

            <?php if ($show_rating) :
                StarRating::echo($average_rating, [
                    'id_prefix' => 'highlightCardStar' . $product_id,
                    'class'     => 'flex items-center gap-0.5 mt-2',
                ]);
            endif; ?>
        </div>
    </div>
</a>