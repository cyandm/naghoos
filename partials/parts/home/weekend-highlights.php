<?php

use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$home_highlights_title = get_field('home_highlights_title', $home_page_id);
$home_highlights_select = get_field('home_highlights_select', $home_page_id);

if (!empty($home_highlights_select)) {
    $post_ids = is_array($home_highlights_select) ? $home_highlights_select : [(int) $home_highlights_select];
    $home_highlights_query = new WP_Query([
        'post_type'      => 'product',
        'post__in'       => array_map('intval', $post_ids),
        'orderby'        => 'post__in',
        'posts_per_page' => 4,
    ]);
} else {
    $home_highlights_query = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'order'          => 'DESC',
    ]);
}
?>

<?php if ($home_highlights_query->have_posts()) : ?>

    <section class="container flex flex-col gap-3 md:gap-5 my-15">

        <p class="text-3xl font-bold text-cynBlack max-md:text-center max-md:w-full">
            <?php echo esc_html($home_highlights_title); ?>
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:gap-5 overflow-visible max-md:hidden">
            <?php
            $index = 0;
            while ($home_highlights_query->have_posts()) :
                $home_highlights_query->the_post();
            ?>
                <div class="overflow-visible md:pt-2">
                    <?php Templates::getCard('product-animation', [
                        'product' => get_the_ID(),
                        'variant' => $index % 4,
                    ]); ?>
                </div>
            <?php
                $index++;
            endwhile;
            ?>
        </div>

        <swiper-container class="w-full md:hidden" space-between="8" slides-per-view="auto" loop="true" pagination="true" pagination-clickable="true" pagination-el=".highlight-pagination">
            <?php
            while ($home_highlights_query->have_posts()) :
                $home_highlights_query->the_post();
            ?>
                <swiper-slide class="w-full h-auto flex">
                    <?php Templates::getCard('product-animation', ['product' => get_the_ID()]); ?>
                </swiper-slide>
            <?php endwhile; ?>
        </swiper-container>

        <div class="highlight-pagination [&>.swiper-pagination-bullet]:bg-[#D9D9D9] [&>.swiper-pagination-bullet]:opacity-100 [&>.swiper-pagination-bullet]:size-1.5 [&>.swiper-pagination-bullet]:rounded-full [&>.swiper-pagination-bullet]:flex [&>.swiper-pagination-bullet.swiper-pagination-bullet-active]:!bg-cynRed z-10 flex flex-row justify-center items-center gap-1 md:hidden"></div>

    </section>

    <?php wp_reset_postdata(); ?>

<?php endif; ?>