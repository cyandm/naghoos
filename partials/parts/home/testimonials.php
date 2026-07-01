<?php

use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$home_testimonial_title = get_field('home_testimonial_title', $home_page_id);
$home_testimonial_select = get_field('home_testimonial_select', $home_page_id);

if (!empty($home_testimonial_select)) {
    $post_ids = is_array($home_testimonial_select) ? $home_testimonial_select : [(int) $home_testimonial_select];
    $home_testimonial_query = new WP_Query([
        'post_type'      => 'testimonial',
        'post__in'       => array_map('intval', $post_ids),
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ]);
} else {
    $home_testimonial_query = new WP_Query([
        'post_type'      => 'testimonial',
        'posts_per_page' => 12,
        'order'          => 'DESC',
    ]);
}
?>

<?php if ($home_testimonial_query->have_posts()) : ?>

    <section class="flex flex-col gap-5 my-15">

        <div class="flex container">
            <p class="text-3xl font-bold text-cynBlack">
                <?php echo esc_html($home_testimonial_title); ?>
            </p>
        </div>

        <swiper-container class="w-full" space-between="8" initial-slide="2" centered-slides="true" slides-per-view="1.15" autoplay="true" delay="5000" loop="true" navigation="false" breakpoints='{ "240": { "slidesPerView": 1.15 }, "768": { "slidesPerView": 3.5 } }'>

            <?php while ($home_testimonial_query->have_posts()) : $home_testimonial_query->the_post(); ?>
                <swiper-slide class="py-1.5 px-1">
                    <?php Templates::getCard('testimonial', ['testimonial' => get_the_ID()]); ?>
                </swiper-slide>
            <?php endwhile; ?>

        </swiper-container>
    </section>

    <?php wp_reset_postdata(); ?>

<?php endif; ?>