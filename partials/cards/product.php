<?php

use Cyan\Theme\Classes\WooCommerce;

$args       = get_query_var('args', []);
$class      = !empty($args['class']) ? $args['class'] : '';
$product_id = !empty($args['product']) ? (int) $args['product'] : get_the_ID();
$product    = wc_get_product($product_id);
if (!$product) {
    return;
}

$prices = WooCommerce::getProductPrices($product);
$percent = WooCommerce::getProductDiscountPercent($product);
$is_out_of_stock = WooCommerce::isProductFullyOutOfStock($product);
$writer = wc_get_product_terms($product_id, 'pa_writer', ['fields' => 'names']);
?>

<div class="product-card relative h-full <?php echo $class; ?>">
    <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="flex h-full flex-col relative group rounded-3xl backdrop-blur-sm shadow-cart">
        <div class="py-8 px-12 shrink-0">
            <?php echo has_post_thumbnail($product_id) ? get_the_post_thumbnail($product_id, 'full', ['class' => '!object-contain !w-full !h-full group-hover:brightness-[80%] transition-all duration-300']) : '<img src="' . esc_url((function_exists('wc_placeholder_img_src') && wc_placeholder_img_src('full')) ? wc_placeholder_img_src('full') : get_template_directory_uri() . '/assets/image/woocommerce-placeholder.webp') . '" class="!object-contain !w-full !h-full group-hover:brightness-65 transition-all duration-300" alt="placeholder">'; ?>
        </div>
        <div class="text-cynBlack flex flex-1 flex-col gap-4 p-3">
            <div class="flex flex-col gap-1">
                <p class="text-base font-medium line-clamp-2"><?php echo esc_html(get_the_title($product_id)); ?></p>

                <?php if (!empty($writer)) : ?>
                    <span class="text-sm font-medium text-cynBlack/60">
                        <?php echo implode('، ', $writer); ?>
                    </span>
                <?php endif; ?>

            </div>

            <div class="text-left mt-auto [&_p]:bg-[#E9E9E9] [&_p]:py-1.5 [&_p]:px-3 [&_p]:rounded-xl [&_p]:w-fit">

                <?php if ($is_out_of_stock) : ?>

                    <p class="text-sm font-medium text-cynRed">
                        <?php esc_html_e('ناموجود', 'taghechian'); ?>
                    </p>

                <?php elseif ($prices['has_discount']) : ?>

                    <p class="text-sm text-cynBlack line-through">
                        <?php echo number_format($prices['regular_price'], 0); ?>
                    </p>

                    <p class="text-sm font-medium text-cynRed">
                        <?php echo number_format($prices['sale_price'], 0); ?>
                    </p>

                <?php else : ?>

                    <p class="text-sm font-medium bg-[#E9E9E9] py-1.5 px-3 rounded-xl">
                        <?php echo wc_price($prices['final_price']); ?>
                    </p>

                <?php endif; ?>

            </div>
        </div>
    </a>

    <?php if ($percent && ! $is_out_of_stock) : ?>
        <div class="absolute top-1.5 right-1.5 bg-[#DD4A4A] py-1 px-1.5">
            <span class="text-xs font-normal text-white"><?php echo $percent . '%' . ' ' ?>تخفیف</span>
        </div>
    <?php endif; ?>
</div>