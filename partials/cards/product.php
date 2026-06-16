<?php

use Cyan\Theme\Classes\WooCommerce;

$args       = get_query_var('args', []);
$product_id = !empty($args['product']) ? (int) $args['product'] : get_the_ID();
$product    = wc_get_product($product_id);
if (!$product) {
    return;
}

$prices = WooCommerce::getProductPrices($product);
$percent = WooCommerce::getProductDiscountPercent($product);
$is_out_of_stock = WooCommerce::isProductFullyOutOfStock($product);
?>

<div class="product-card relative">
    <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="block relative group p-2 rounded-3xl backdrop-blur-sm shadow-cart">
        <div class="py-6 px-4 rounded-2xl overflow-hidden">
            <?php echo has_post_thumbnail($product_id) ? get_the_post_thumbnail($product_id, 'full', ['class' => '!object-contain !w-full h-[320px] lg:!h-[500px] group-hover:brightness-[80%] transition-all duration-300 rounded-2xl']) : '<img src="' . esc_url((function_exists('wc_placeholder_img_src') && wc_placeholder_img_src('full')) ? wc_placeholder_img_src('full') : get_template_directory_uri() . '/assets/image/woocommerce-placeholder.webp') . '" class="!object-cover !w-full h-[320px] lg:!h-[500px] group-hover:brightness-65 transition-all duration-300 rounded-2xl" alt="placeholder">'; ?>
        </div>
        <div class="p-3 md:p-4 text-cynBlack flex justify-between items-end gap-1 md:gap-3 h-full">
            <p class="text-base font-medium"><?php echo esc_html(get_the_title($product_id)); ?></p>

            <div class="text-left">

                <?php if ($is_out_of_stock) : ?>

                    <p class="text-base font-medium text-cynRed">
                        <?php esc_html_e('ناموجود', 'taghechian'); ?>
                    </p>

                <?php elseif ($prices['has_discount']) : ?>

                    <p class="text-sm text-cynBlack line-through">
                        <?php echo number_format($prices['regular_price'], 0); ?>
                    </p>

                    <p class="text-base font-medium text-cynRed">
                        <?php echo number_format($prices['sale_price'], 0); ?>
                    </p>

                <?php else : ?>

                    <p class="text-base font-medium">
                        <?php echo number_format($prices['final_price'], 0); ?>
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