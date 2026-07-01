<?php

use Cyan\Theme\Classes\WooCommerce;

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

$link_classes = 'flex h-full relative group rounded-3xl bg-white/20 hover:bg-white transition-all duration-300 shadow-cart w-full items-stretch';

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
    $image_classes .= ' max-md:drop-shadow-md size-[265px]';
}

$placeholder_src = (function_exists('wc_placeholder_img_src') && wc_placeholder_img_src('full'))
    ? wc_placeholder_img_src('full')
    : get_template_directory_uri() . '/assets/image/woocommerce-placeholder.webp';

$content_classes = 'text-cynBlack flex flex-1 flex-col';

if ($responsive && $is_vertical) {
    $content_classes .= ' max-md:justify-center max-md:items-center max-md:text-center max-md:gap-2 max-md:px-2 md:gap-4 md:p-3';
} else {
    $content_classes .= ' gap-4 p-3';
}

$price_classes = 'mt-auto [&_p]:bg-[#E9E9E9] [&_p]:py-1.5 [&_p]:px-3 [&_p]:rounded-xl [&_p]:w-fit';

if ($responsive && $is_vertical) {
    $price_classes .= ' max-md:flex max-md:flex-col max-md:items-center md:text-left';
} else {
    $price_classes .= ' text-left';
}

$average_rating = (float) $product->get_average_rating();
$rating_count   = (int) $product->get_rating_count();
$show_rating    = wc_review_ratings_enabled() && $rating_count > 0;
$full_stars     = (int) floor($average_rating);
$half_star      = ($average_rating - $full_stars) * 100;
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
            <div class="flex flex-col gap-1 max-md:items-center">
                <p class="text-base font-medium line-clamp-2"><?php echo esc_html(get_the_title($product_id)); ?></p>

                <?php if (! empty($writer)) : ?>
                    <span class="text-sm font-medium text-cynBlack/60">
                        <?php echo esc_html(implode('، ', $writer)); ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($show_rating) : ?>
                <span class="flex items-center justify-center gap-0.5 max-md:mx-auto">
                    <?php for ($i = 1; $i <= 5; $i++) :
                        if ($i <= $full_stars) {
                            $fill_percentage = 100;
                        } elseif ($i === $full_stars + 1) {
                            $fill_percentage = $half_star;
                        } else {
                            $fill_percentage = 0;
                        }

                        $gradient_id = 'productCardStar' . $product_id . $i;
                    ?>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 18 18" aria-hidden="true">
                            <defs>
                                <linearGradient id="<?php echo esc_attr($gradient_id); ?>" x1="100%" y1="0%" x2="0%" y2="0%">
                                    <stop offset="<?php echo esc_attr((string) $fill_percentage); ?>%" stop-color="#fecf00" />
                                    <stop offset="<?php echo esc_attr((string) $fill_percentage); ?>%" stop-color="#E0E0E0" />
                                </linearGradient>
                            </defs>
                            <path d="M16.963,6.786c-.088-.271-.323-.469-.605-.51l-4.62-.671L9.672,1.418c-.252-.512-1.093-.512-1.345,0l-2.066,4.186-4.62,.671c-.282,.041-.517,.239-.605,.51-.088,.271-.015,.57,.19,.769l3.343,3.258-.79,4.601c-.048,.282,.067,.566,.298,.734,.231,.167,.538,.189,.79,.057l4.132-2.173,4.132,2.173c.11,.058,.229,.086,.349,.086,.155,0,.31-.048,.441-.143,.231-.168,.347-.452,.298-.734l-.79-4.601,3.343-3.258c.205-.199,.278-.498,.19-.769Z" fill="url(#<?php echo esc_attr($gradient_id); ?>)" />
                        </svg>
                    <?php endfor; ?>
                </span>
            <?php endif; ?>

            <div class="<?php echo esc_attr($price_classes); ?>">
                <?php if ($is_out_of_stock) : ?>
                    <p class="text-sm font-medium text-cynRed">
                        <?php esc_html_e('ناموجود', 'taghechian'); ?>
                    </p>
                <?php elseif ($prices['has_discount']) : ?>
                    <p class="text-sm text-cynBlack line-through">
                        <?php echo esc_html(number_format($prices['regular_price'], 0)); ?>
                    </p>
                    <p class="text-sm font-medium text-cynRed">
                        <?php echo esc_html(number_format($prices['sale_price'], 0)); ?>
                    </p>
                <?php else : ?>
                    <p class="text-sm font-medium">
                        <?php echo wp_kses_post(wc_price($prices['final_price'])); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </a>

    <?php if ($percent && ! $is_out_of_stock) : ?>
        <div class="absolute top-2 right-2 bg-[#DD4A4A] py-1 px-3 rounded-tr-2xl rounded-bl-2xl">
            <span class="text-xs font-normal text-white"><?php echo esc_html($percent . '% تخفیف'); ?></span>
        </div>
    <?php endif; ?>
</div>