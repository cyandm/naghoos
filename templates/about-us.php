<?php /* Template Name: About Us */ ?>

<?php

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

$about_img_hero_desktop = get_field('about_img_hero_desktop');
$about_img_hero_mobile = get_field('about_img_hero_mobile');
$about_text_one = get_field('about_text_one');
$about_video_title = get_field('about_video_title');
$about_video_file = wp_get_attachment_url(get_field('about_video_file'));
$about_video_cover = wp_get_attachment_url(get_field('about_video_cover'));
$about_personnel_title = get_field('about_personnel_title');
$about_personnel_select = get_field('about_personnel_select');
$about_text_two = get_field('about_text_two');

if (!empty($about_personnel_select)) {
    $post_ids = is_array($about_personnel_select) ? $about_personnel_select : [(int) $about_personnel_select];
    $about_personnel_query = new WP_Query([
        'post_type'      => 'personnel',
        'post__in'       => array_map('intval', $post_ids),
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ]);
} else {
    $about_personnel_query = new WP_Query([
        'post_type'      => 'personnel',
        'posts_per_page' => 12,
        'order'          => 'DESC',
    ]);
}

get_header(); ?>

<main class="about-us -mt-16 md:-mt-20">

    <?php if ($about_img_hero_desktop || $about_img_hero_mobile) : ?>
        <section>
            <div class="w-full">
                <?php echo wp_get_attachment_image($about_img_hero_desktop, 'full', false, ['class' => 'w-full h-full object-cover max-md:hidden']); ?>
                <?php echo wp_get_attachment_image($about_img_hero_mobile, 'full', false, ['class' => 'w-full h-full object-cover md:hidden']); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($about_text_one) : ?>
        <section class="container mt-6">
            <div class="text-base font font-medium leading-10 [&_h2]:text-2xl [&_h2]:font-semibold [&_h3]:text-2xl [&_h3]:font-semibold [&>p]:mt-3 [&>p]:mb-4 [&_blockquote]:p-5 [&_blockquote]:border [&_blockquote]:border-cynBlack/10 [&_blockquote]:rounded-2xl">
                <?php echo $about_text_one ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($about_video_file) && !empty($about_video_cover)) : ?>
        <section class="container my-14">
            <div class="flex max-h-[40rem] relative [&>div]:w-full [&>div]:rounded-3xl">

                <video class="video video-plyr w-full h-full object-contain" playsinline controls data-poster="<?php echo esc_url($about_video_cover); ?>" src="<?php echo esc_url($about_video_file); ?>"></video>

                <div class="video-cover absolute top-0 left-0 w-full h-full bg-cover bg-center bg-no-repeat overflow-hidden rounded-3xl cursor-pointer flex flex-col items-center justify-center opacity-100 transition-all duration-300 pointer-events-auto" style="background-image: url(<?= $about_video_cover ?>);">

                    <div class="flex flex-col items-center justify-center gap-4 bg-black/35 w-full h-full">
                        <p class="text-cynWhite text-2xl md:text-4xl font-semibold"><?= $about_video_title ?></p>
                        <i class="size-20 max-md:size-14 text-cynWhite"><?php Icon::print('Play') ?></i>
                    </div>

                </div>

            </div>
        </section>
    <?php endif; ?>

    <?php if ($about_personnel_query->have_posts()) : ?>
        <section class="flex flex-col gap-5 my-14">

            <div class="flex container">
                <p class="text-3xl font-bold text-cynBlack">
                    <?php echo $about_personnel_title ?>
                </p>
            </div>

            <swiper-container class="w-full" space-between="12" slides-per-view="auto" loop="true" navigation="false">

                <?php while ($about_personnel_query->have_posts()) : $about_personnel_query->the_post(); ?>
                    <swiper-slide class="max-w-[183px] h-auto flex">
                        <?php Templates::getCard('personnel', ['person' => get_the_ID()]); ?>
                    </swiper-slide>
                <?php endwhile; ?>

            </swiper-container>
        </section>
    <?php endif; ?>

    <?php if ($about_text_two) : ?>
        <section class="container ">
            <div class="text-base font font-medium leading-10 [&_h2]:text-2xl [&_h2]:font-semibold [&_h3]:text-2xl [&_h3]:font-semibold [&>p]:mt-3 [&>p]:mb-4 [&_blockquote]:p-5 [&_blockquote]:border [&_blockquote]:border-cynBlack/10 [&_blockquote]:rounded-2xl">
                <?php echo $about_text_two ?>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>