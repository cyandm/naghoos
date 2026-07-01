<?php

use Cyan\Theme\Classes\WooCommerce;
use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$home_discounted_products_title = get_field('home_discounted_products_title', $home_page_id);
$home_discounted_products_under_title = get_field('home_discounted_products_under_title', $home_page_id);
$home_discounted_products_selected = get_field('home_discounted_products_selected', $home_page_id);
$home_discounted_products_button = get_field('home_discounted_products_button', $home_page_id);
$home_discounted_products_background = get_field('home_discounted_products_background', $home_page_id);

if (!empty($home_discounted_products_selected)) {
    $post_ids = is_array($home_discounted_products_selected) ? $home_discounted_products_selected : [(int) $home_discounted_products_selected];
    $home_discounted_products_query = new WP_Query([
        'post_type'      => 'product',
        'post__in'       => array_map('intval', $post_ids),
        'orderby'        => 'post__in',
        'posts_per_page' => 4,
    ]);
} else {
    $on_sale_ids = wc_get_product_ids_on_sale();

    if (! empty($on_sale_ids)) {
        usort($on_sale_ids, static function (int $a, int $b): int {
            $product_a = wc_get_product($a);
            $product_b = wc_get_product($b);
            $percent_a = $product_a ? (int) WooCommerce::getProductDiscountPercent($product_a) : 0;
            $percent_b = $product_b ? (int) WooCommerce::getProductDiscountPercent($product_b) : 0;

            return $percent_b <=> $percent_a;
        });

        $on_sale_ids = array_slice($on_sale_ids, 0, 4);
    }

    $home_discounted_products_query = new WP_Query([
        'post_type'      => 'product',
        'post__in'       => ! empty($on_sale_ids) ? $on_sale_ids : [0],
        'orderby'        => 'post__in',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
    ]);
}

if (!$home_discounted_products_query->have_posts()) {
    return;
}

if (empty($home_discounted_products_title) && empty($home_discounted_products_button)) {
    return;
}

$background_url = '';

if (is_array($home_discounted_products_background) && ! empty($home_discounted_products_background['url'])) {
    $background_url = $home_discounted_products_background['url'];
} elseif (is_numeric($home_discounted_products_background)) {
    $background_url = wp_get_attachment_image_url((int) $home_discounted_products_background, 'full') ?: '';
}

wp_reset_postdata();
?>

<section class="my-16 py-6 md:py-12 bg-cover bg-center bg-no-repeat"<?php echo $background_url ? ' style="background-image: url(' . esc_url($background_url) . ');"' : ''; ?>>
    <div class="container flex flex-col gap-4">
        <div class="flex justify-between items-center">
            <div class="flex flex-col gap-2 max-md:justify-center max-md:text-center max-md:w-full">
                <?php if ($home_discounted_products_title) : ?>
                    <p class="text-3xl font-bold"><?php echo esc_html($home_discounted_products_title); ?></p>
                <?php endif; ?>
                <?php if ($home_discounted_products_under_title) : ?>
                    <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($home_discounted_products_under_title); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($home_discounted_products_button['url'])) : ?>
                <a href="<?php echo esc_url($home_discounted_products_button['url']); ?>" class="primary-btn max-md:hidden"><?php echo esc_html($home_discounted_products_button['title'] ?? ''); ?></a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-3 w-full [&_.product-card]:h-full">
            <?php
            while ($home_discounted_products_query->have_posts()) :
                $home_discounted_products_query->the_post();
            ?>
                <?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>

        <?php if (!empty($home_discounted_products_button['url'])) : ?>
            <div class="w-full">
                <a href="<?php echo esc_url($home_discounted_products_button['url']); ?>" class="primary-btn md:hidden w-full flex justify-center items-center"><?php echo esc_html($home_discounted_products_button['title'] ?? ''); ?></a>
            </div>
        <?php endif; ?>
    </div>
</section>