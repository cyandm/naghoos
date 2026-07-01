<?php

use Cyan\Theme\Helpers\Templates;

$home_slider = new WP_Query([
    'post_type' => 'slider',
    'posts_per_page' => -1,
    'tax_query' => [
        [
            'taxonomy' => 'slider_place',
            'field'    => 'slug',
            'terms'    => 'home',
        ],
    ],
]);

if (!$home_slider->have_posts()) return;
?>

<section class="home-slider w-full max-md:-mt-4">
    <div class="relative">

        <swiper-container class="h-[620px] lg:h-[740px]" slides-per-view="1" space-between="0" autoplay="true" loop="true" direction="vertical" pagination="true" pagination-clickable="true" pagination-el=".swiper-pagination" fade="true">
            <?php
            if ($home_slider->have_posts()) :
                while ($home_slider->have_posts()) :
                    $home_slider->the_post();
            ?>
                    <swiper-slide class="h-full">
                        <?php Templates::getCard('slider', ['post-id' => get_the_ID()]) ?>
                    </swiper-slide>
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </swiper-container>
        <div class="swiper-pagination [&>.swiper-pagination-bullet]:opacity-100 [&>.swiper-pagination-bullet]:size-6 [&>.swiper-pagination-bullet]:border-2 [&>.swiper-pagination-bullet]:border-transparent [&>.swiper-pagination-bullet]:rounded-full [&>.swiper-pagination-bullet]:flex [&>.swiper-pagination-bullet:nth-child(4n+1)]:bg-gray-300 [&>.swiper-pagination-bullet:nth-child(4n+2)]:bg-yellow-300 [&>.swiper-pagination-bullet:nth-child(4n+3)]:bg-violet-300 [&>.swiper-pagination-bullet:nth-child(4n+4)]:bg-blue-300 [&>.swiper-pagination-bullet.swiper-pagination-bullet-active]:!bg-cynRed [&>.swiper-pagination-bullet.swiper-pagination-bullet-active]:!border-cynBlack absolute bottom-1/2 left-2/11 max-sm:hidden z-10 flex flex-col justify-center items-center gap-1.5"></div>
    </div>
</section>