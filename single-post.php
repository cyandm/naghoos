<?php

/**
 * The template for displaying single blog posts
 * 
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 * @package CyanTheme
 */

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

$current_post_id = get_the_ID();
$categories = get_the_category();
$tags = get_the_tags();

$related_args = [
    'post_type' => 'post',
    'posts_per_page' => 4,
    'post__not_in' => [$current_post_id],
];

if (!empty($categories)) {
    $related_args['category__in'] = wp_list_pluck($categories, 'term_id');
} elseif ($tags && !empty($tags)) {
    $related_args['tag__in'] = wp_list_pluck(array_values($tags), 'term_id');
}

$related_posts_query = new WP_Query($related_args);

get_header(); ?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="single-post">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <section class="container flex flex-col gap-2 mb-5">

                <div class="text-cynBlack/50 text-xs font-medium flex gap-2 items-center"><?php the_category('|'); ?></div>

                <h1 class="text-2xl md:text-3xl font-semibold text-cynBlack leading-11"><?php the_title(); ?></h1>

                <img src="<?= get_template_directory_uri(); ?>/assets/image/zigzag.svg" alt="border" class="w-32">

                <div class="flex gap-4 items-center mt-2">

                    <div class="flex flex-row gap-1 items-center justify-center">

                        <i class="size-4 stroke-[1.5] text-cynBlack/40">
                            <?php Icon::print('User,-Profile'); ?>
                        </i>
                        <span class="text-cynBlack/40 text-xs font-medium"><?php the_author(); ?></span>

                    </div>

                    <div class="flex flex-row gap-1 items-center justify-center">

                        <i class="size-4 stroke-[1.5] text-cynBlack/40">
                            <?php Icon::print('calendar-schedule-1-1') ?>
                        </i>
                        <span class="text-cynBlack/40 text-xs font-medium"><?= get_the_date(); ?></span>

                    </div>

                </div>

                <div class="flex justify-between p-6 rounded-3xl bg-cynWhite border border-cynBlack/10 mt-2 items-center">

                    <div class="flex items-center">
                        <i class="size-6 text-cynBlack cursor-pointer stroke-[1.5]" id="shareBtn">
                            <?php Icon::print('Share-2') ?>
                        </i>
                    </div>

                    <div class="flex items-center gap-2">

                        <span class="text-cynBlack text-base font-medium"><?= get_comments_number(); ?></span>

                        <i class="size-6 text-cynBlack stroke-[1.5]">
                            <?php Icon::print('Chat,-Messages,-Bubble-6') ?>
                        </i>

                    </div>

                </div>
            </section>

            <section class="container single-post-content">

                <div class="w-full">
                    <?php the_post_thumbnail('full', ['class' => 'w-full h-[320px] md:h-[460px] lg:h-[770px] object-cover object-center']) ?>
                </div>

                <div class="text-cynBlack [&_a]:text-cynBlue [&_a]:font-normal [&_h2]:text-2xl [&_h2]:my-4 [&_h3]:text-xl [&_h3]:my-4 [&_h4]:text-xl [&_h4]:my-4 [&_p]:text-base [&_p]:font-light [&_p]:leading-8 [&_p]:my-4 [&_img]:w-full [&_img]:max-h-96 [&_img]:object-cover [&_blockquote]:bg-[#E5E5E5] [&_blockquote]:px-2 [&_blockquote]:my-4 [&_blockquote]:text-base [&_blockquote]:font-medium">
                    <?php the_content(); ?>
                </div>

            </section>

            <!-- Comments Section -->
            <?php if (comments_open() || get_comments_number()) : ?>
                <section class="container comments-section mt-5">
                    <?php Templates::getPart('comment'); ?>
                </section>
            <?php endif; ?>

            <!-- Related Posts Section -->
            <section class="container flex flex-col gap-3.5 my-14">

                <div class="w-full">
                    <p class="text-3xl font-bold text-cynBlack leading-11 max-md:text-center"><?php _e('شاید بپسندید', 'taghechian'); ?></p>
                </div>

                <?php if ($related_posts_query->have_posts()) : ?>
                    <div class="flex flex-col gap-5 md:grid md:grid-cols-4 md:gap-3 [&_img]:h-[142px] [&_img]:md:h-[184px] [&_img]:aspect-square [&_img]:md:aspect-auto">
                        <?php while ($related_posts_query->have_posts()) : $related_posts_query->the_post(); ?>
                            <?php Templates::getCard('blog'); ?>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>
                <?php endif; ?>

            </section>

    <?php endwhile;
    endif; ?>

</main>

<?php get_footer();
