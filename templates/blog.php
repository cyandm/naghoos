<?php
/* Template Name: Blog */

use Cyan\Theme\Classes\ViewsCount;
use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$blogs_bests_weekly_title = get_field('blogs_bests_weekly_title');
$blogs_bests_weekly_select = ViewsCount::resolveWeeklyBests(get_field('blogs_bests_weekly_select'), 5);
$blogs_newest_img = wp_get_attachment_image_url(get_field('blogs_newest_img'), 'full');
$blogs_newest_limit = 12;
$blogs_newest_ids = array_values(array_filter(array_map('intval', (array) get_field('blogs_newest_select'))));
$blogs_newest_ids = array_slice(array_unique($blogs_newest_ids), 0, $blogs_newest_limit);

if (count($blogs_newest_ids) < $blogs_newest_limit) {
    $newest_query = new WP_Query([
        'post_type' => post_type_exists('blog') ? 'blog' : 'post',
        'post_status' => 'publish',
        'posts_per_page' => $blogs_newest_limit - count($blogs_newest_ids),
        'fields' => 'ids',
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => $blogs_newest_ids,
    ]);
    $blogs_newest_ids = array_merge($blogs_newest_ids, array_map('intval', $newest_query->posts));
}
$blogs_categories_title = get_field('blogs_categories_title');

get_header();
?>

<main class="blog-page">

    <?php if ($blogs_bests_weekly_select && count($blogs_bests_weekly_select) > 0) : ?>
        <section class="container">
            <h1 class="hidden"><?php _e('بلاگ', 'naghoos'); ?></h1>
            <div class="flex flex-col gap-3 w-full">
                <h2 class="text-3xl font-bold text-cynBlack leading-11 max-md:text-center"><?php echo esc_html(!empty($blogs_bests_weekly_title) ? $blogs_bests_weekly_title : __('بهترین های این هفته', 'naghoos')); ?></h2>

                <div class="hidden md:grid w-full grid-cols-[minmax(0,1fr)_minmax(0,1.35fr)_minmax(0,1fr)] grid-rows-2 gap-4 [&>*:nth-child(2)]:col-start-2 [&>*:nth-child(2)]:row-span-2 [&>*:nth-child(2)]:row-start-1 [&>*:nth-child(3)]:col-start-3 [&>*:nth-child(3)]:row-start-1 [&>*:nth-child(4)]:col-start-1 [&>*:nth-child(4)]:row-start-2 [&>*:nth-child(5)]:col-start-3 [&>*:nth-child(5)]:row-start-2 [&_img]:h-[152px] [&>*:nth-child(2)_img]:h-full [&_a]:h-[216px] [&>*:nth-child(2)]:lg:!h-[450px] [&>*:nth-child(2)]:h-full [&>*:nth-child(2)_img]:md:h-[380px] [&>*:nth-child(2)_img]:lg:h-[385px] [&_img]:aspect-auto">
                    <?php foreach ($blogs_bests_weekly_select as $blog_selected) : ?>
                        <?php Templates::getCard('blog', ['post_id' => $blog_selected]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="md:hidden mt-3">
            <swiper-container slides-per-view="1.25" loop="true" space-between="12" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' pagination="true" pagination-clickable="true" pagination-bullet-active-class="bg-cynRed" pagination-bullet-inactive-class="bg-[#D9D9D9]">
                <?php foreach ($blogs_bests_weekly_select as $blog_selected) : ?>
                    <swiper-slide>
                        <?php Templates::getCard('blog-horizontal', ['post_id' => $blog_selected]); ?>
                    </swiper-slide>
                <?php endforeach; ?>
            </swiper-container>
        </section>
    <?php endif; ?>

    <?php if ($blogs_newest_ids) : ?>
        <section class="my-14 bg-cover bg-center bg-no-repeat py-11" style="background-image: url('<?php echo esc_url(!empty($blogs_newest_img) ? $blogs_newest_img : ''); ?>');">

            <swiper-container slides-per-view="1.25" loop="true" space-between="12" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}'>
                <?php foreach ($blogs_newest_ids as $blog_selected) : ?>
                    <swiper-slide>
                        <?php Templates::getCard('blog-vertical', ['post_id' => $blog_selected]); ?>
                    </swiper-slide>
                <?php endforeach; ?>
            </swiper-container>

        </section>
    <?php endif; ?>
</main>

<?php get_footer(); ?>