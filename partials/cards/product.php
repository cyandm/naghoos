<?php

use Cyan\Theme\Classes\WooCommerce;
use Cyan\Theme\Helpers\StarRating;

$args       = get_query_var('args', []);
$class      = ! empty($args['class']) ? $args['class'] : '';
$layout     = ($args['layout'] ?? 'vertical') === 'vertical' ? 'vertical' : 'horizontal';
$responsive = array_key_exists('responsive', $args) ? (bool) $args['responsive'] : true;
$fixed      = ! empty($args['fixed']);
$product_id = ! empty($args['product']) ? (int) $args['product'] : get_the_ID();
$product    = wc_get_product($product_id);

if (! $product) {
    return;
}

$prices          = WooCommerce::getProductPrices($product);
$percent         = WooCommerce::getProductDiscountPercent($product);
$is_out_of_stock = WooCommerce::isProductFullyOutOfStock($product);
$writer          = wc_get_product_terms($product_id, 'pa_writer', ['fields' => 'names']);

$is_vertical = $layout === 'vertical';
$fixed       = $fixed && $is_vertical;

$link_classes = 'flex h-full relative group rounded-3xl bg-white/20 hover:bg-white border border-transparent hover:border-cynBlack transition-all duration-300 shadow-cart w-full items-stretch';

if ($responsive) {
    $link_classes .= $is_vertical
        ? ' flex-row gap-2 md:flex-col md:gap-3'
        : ' flex-col gap-3 md:flex-row md:gap-2';
} else {
    $link_classes .= $is_vertical ? ' flex-col gap-3' : ' flex-row gap-2';
}

if ($fixed) {
    $link_classes .= $responsive ? ' max-md:h-[160px] max-md:w-full max-md:justify-center' : ' h-[160px] w-full justify-center';
}

$image_wrapper_classes = 'shrink-0 flex items-center justify-center overflow-hidden';

if ($responsive) {
    $image_wrapper_classes .= $is_vertical
        ? ' w-[45%] py-3 px-4 md:w-full md:py-8 md:px-12'
        : ' w-full py-8 px-12 md:w-[45%] md:py-3 md:px-4';
} else {
    $image_wrapper_classes .= $is_vertical ? ' w-full py-8 px-12' : ' w-[45%] py-3 px-4';
}

$image_classes = '!object-contain group-hover:brightness-[80%] transition-all duration-300 rounded-md';

if ($responsive && $is_vertical) {
    $image_classes .= ' max-md:drop-shadow-md size-[120px] md:size-[265px]';
}

$placeholder_src = (function_exists('wc_placeholder_img_src') && wc_placeholder_img_src('full'))
    ? wc_placeholder_img_src('full')
    : get_template_directory_uri() . '/assets/image/woocommerce-placeholder.webp';

$content_classes = 'text-cynBlack flex flex-1 flex-col';

if ($responsive && $is_vertical) {
    $content_classes .= ' max-md:gap-2 max-md:p-2 md:gap-4 md:p-3 max-md:justify-center max-md:items-start';
} else {
    $content_classes .= ' gap-4 p-3';
}

$price_wrapper_classes = '';

if ($responsive && $is_vertical) {
    $price_wrapper_classes .= ' max-md:flex';
}

$average_rating = (float) $product->get_average_rating();
$rating_count   = (int) $product->get_rating_count();
$show_rating    = wc_review_ratings_enabled() && $rating_count >= 0;
?>

<div class="product-card relative h-full <?php echo esc_attr($class); ?>">
    <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="<?php echo esc_attr($link_classes); ?>">
        <div class="<?php echo esc_attr($image_wrapper_classes); ?>">
            <?php if (has_post_thumbnail($product_id)) : ?>
                <?php echo get_the_post_thumbnail($product_id, 'full', ['class' => $image_classes]); ?>
            <?php else : ?>
                <img src="<?php echo esc_url($placeholder_src); ?>" class="<?php echo esc_attr($image_classes); ?> group-hover:brightness-65" alt="">
            <?php endif; ?>
        </div>

        <div class="<?php echo esc_attr($content_classes); ?>">
            <div class="flex flex-col gap-1">
                <p class="text-base font-medium line-clamp-2"><?php echo esc_html(get_the_title($product_id)); ?></p>

                <?php if (! empty($writer)) : ?>
                    <span class="text-sm font-medium text-cynBlack/60">
                        <?php echo esc_html(implode('، ', $writer)); ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($show_rating) :
                StarRating::echo($average_rating, [
                    'id_prefix'    => 'productCardStar' . $product_id,
                    'class'        => 'flex items-center gap-0.5',
                    'stroke_color' => '#1E1311',
                ]);
            endif; ?>

            <div class="<?php echo esc_attr($price_wrapper_classes); ?>">
                <?php if ($is_out_of_stock) : ?>
                    <span class="inline-flex items-center rounded-xl py-1.5 px-3 text-sm font-medium bg-[#E9E9E9] text-[#DD4A4A] w-fit">
                        <?php esc_html_e('ناموجود', 'naghoos'); ?>
                    </span>
                <?php elseif ($prices['has_discount']) : ?>
                    <span class="inline-flex items-center gap-1 rounded-xl py-1.5 px-3 text-sm font-medium bg-[#EE9191] text-white w-fit">
                        <span class="line-through"><?php echo esc_html(WooCommerce::formatCardPriceAmount($prices['regular_price'])); ?></span>
                        <span aria-hidden="true">/</span>
                        <span><?php echo esc_html(WooCommerce::formatCardPriceThousands($prices['sale_price'])); ?></span>
                    </span>
                <?php else : ?>
                    <span class="inline-flex items-center rounded-xl py-1.5 px-3 text-sm font-medium bg-[#E9E9E9] text-cynBlack w-fit">
                        <?php echo esc_html(WooCommerce::formatCardPriceThousands($prices['final_price'])); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </a>

    <?php if ($is_out_of_stock) : ?>
        <div class="absolute top-2 right-2 bg-[#DD4A4A] py-2 px-3 rounded-tr-2xl rounded-bl-2xl flex justify-center items-center">
            <span class="text-xs font-normal text-white"><?php esc_html_e('ناموجود', 'naghoos'); ?></span>
        </div>
    <?php elseif ($percent) : ?>
        <div class="absolute top-2 right-2 bg-[#DD4A4A] py-2 px-3 rounded-tr-2xl rounded-bl-2xl flex justify-center items-center">
            <span class="text-xs font-normal text-white"><?php echo esc_html($percent . ' % ' . __('تخفیف', 'naghoos')); ?></span>
        </div>
    <?php endif; ?>
</div>