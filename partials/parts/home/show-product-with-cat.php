<?php

use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$home_show_products_with_cat_title = get_field('home_show_products_with_cat_title', $home_page_id);
$home_show_products_with_cat_under_title = get_field('home_show_products_with_cat_under_title', $home_page_id);
$home_show_products_with_cat_selected = get_field('home_show_products_with_cat_selected', $home_page_id);
$home_show_products_with_cat_button = get_field('home_show_products_with_cat_button', $home_page_id);

if (empty($home_show_products_with_cat_selected)) {
    return;
}

$category_terms = is_array($home_show_products_with_cat_selected)
    ? array_map('intval', $home_show_products_with_cat_selected)
    : [(int) $home_show_products_with_cat_selected];

$home_show_products_with_cat_query = new WP_Query([
    'post_type'      => 'product',
    'tax_query'      => [
        [
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $category_terms,
        ],
    ],
    'posts_per_page' => 4,
]);

if (! $home_show_products_with_cat_query->have_posts()) {
    return;
}

if (empty($home_show_products_with_cat_title) && empty($home_show_products_with_cat_button)) {
    return;
}

wp_reset_postdata();
?>

<section class="flex flex-col gap-4 my-14 container">
    <div class="flex justify-between items-center">
        <div class="flex flex-col gap-2 max-md:justify-center max-md:text-center max-md:w-full">
            <?php if ($home_show_products_with_cat_title) : ?>
                <p class="text-3xl font-bold"><?php echo esc_html($home_show_products_with_cat_title); ?></p>
            <?php endif; ?>
            <?php if ($home_show_products_with_cat_under_title) : ?>
                <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($home_show_products_with_cat_under_title); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($home_show_products_with_cat_button['url'])) : ?>
            <a href="<?php echo esc_url($home_show_products_with_cat_button['url']); ?>" class="primary-btn max-md:hidden"><?php echo esc_html($home_show_products_with_cat_button['title'] ?? ''); ?></a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-3 w-full [&_.product-card]:h-full">
        <?php
        while ($home_show_products_with_cat_query->have_posts()) :
            $home_show_products_with_cat_query->the_post();
        ?>
            <?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
        <?php endwhile;
        wp_reset_postdata(); ?>
    </div>

    <?php if (!empty($home_show_products_with_cat_button['url'])) : ?>
        <div class="w-full">
            <a href="<?php echo esc_url($home_show_products_with_cat_button['url']); ?>" class="primary-btn md:hidden w-full flex justify-center items-center"><?php echo esc_html($home_show_products_with_cat_button['title'] ?? ''); ?></a>
        </div>
    <?php endif; ?>
</section>