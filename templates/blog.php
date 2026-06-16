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

$blogs_categories = get_terms([
    'taxonomy' => 'category',
    'hide_empty' => true,
    'exclude' => [(int) get_option('default_category')],
]);

if (is_wp_error($blogs_categories)) {
    $blogs_categories = [];
}

$blog_post_type       = post_type_exists('blog') ? 'blog' : 'post';
$blog_posts_per_page  = 24;
$blog_page_url        = get_permalink();
$blog_tab             = isset($_GET['blog_tab']) ? sanitize_key(wp_unslash($_GET['blog_tab'])) : 'all';
$blog_paged           = max(1, (int) ($_GET['blog_paged'] ?? 1));
$blog_valid_tabs      = ['all'];

foreach ($blogs_categories as $category) {
    $blog_valid_tabs[] = $category->slug;
}

if (! in_array($blog_tab, $blog_valid_tabs, true)) {
    $blog_tab = 'all';
}

$blog_category_posts = [];

$all_query = new WP_Query([
    'post_type'      => $blog_post_type,
    'post_status'    => 'publish',
    'posts_per_page' => $blog_posts_per_page,
    'paged'          => $blog_tab === 'all' ? $blog_paged : 1,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

$blog_category_posts['all'] = [
    'post_ids' => array_map('intval', wp_list_pluck($all_query->posts, 'ID')),
    'current'  => $blog_tab === 'all' ? $blog_paged : 1,
    'total'    => (int) $all_query->max_num_pages,
    'tab'      => 'all',
];

wp_reset_postdata();

foreach ($blogs_categories as $category) {
    $category_query = new WP_Query([
        'post_type'      => $blog_post_type,
        'post_status'    => 'publish',
        'posts_per_page' => $blog_posts_per_page,
        'paged'          => $blog_tab === $category->slug ? $blog_paged : 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'cat'            => (int) $category->term_id,
    ]);

    $blog_category_posts[$category->slug] = [
        'post_ids' => array_map('intval', wp_list_pluck($category_query->posts, 'ID')),
        'current'  => $blog_tab === $category->slug ? $blog_paged : 1,
        'total'    => (int) $category_query->max_num_pages,
        'tab'      => $category->slug,
    ];

    wp_reset_postdata();
}

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
                        <?php Templates::getCard('blog', ['post_id' => $blog_selected, 'layout' => 'horizontal', 'responsive' => true]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="md:hidden mt-3 best-weekly-swiper">
            <swiper-container slides-per-view="1.25" centered-slides="true" loop="true" space-between="12" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' pagination="true" pagination-clickable="true">
                <?php foreach ($blogs_bests_weekly_select as $blog_selected) : ?>
                    <swiper-slide>
                        <?php Templates::getCard('blog', ['post_id' => $blog_selected, 'layout' => 'vertical', 'responsive' => false]); ?>
                    </swiper-slide>
                <?php endforeach; ?>
            </swiper-container>
        </section>
    <?php endif; ?>

    <?php if ($blogs_newest_ids) : ?>
        <section class="my-14 bg-cover bg-center bg-no-repeat py-11" style="background-image: url('<?php echo esc_url(!empty($blogs_newest_img) ? $blogs_newest_img : ''); ?>');">

            <swiper-container slides-per-view="auto" loop="true" centered-slides="true" initial-slide="1" slides-offset-before="20" slides-offset-after="20" space-between="12" breakpoints='{ "320":  { "slidesPerView": 1 }, "425":  { "slidesPerView": 1.20 }, "530":  { "slidesPerView": 1.50 }, "768":  { "slidesPerView": 2.15 }, "920":  { "slidesPerView": 2.5 }, "1280": { "slidesPerView": 3.5 }, "1650": { "slidesPerView": 4.5 }}'>
                <?php foreach ($blogs_newest_ids as $blog_selected) : ?>
                    <swiper-slide>
                        <?php Templates::getCard('blog', ['post_id' => $blog_selected, 'layout' => 'horizontal', 'responsive' => false, 'fixed' => true]); ?>
                    </swiper-slide>
                <?php endforeach; ?>
            </swiper-container>

        </section>
    <?php endif; ?>

    <section id="blog-categories" class="container flex flex-col gap-3">
        <div class="flex flex-col md:flex-row gap-3 w-full justify-between">
            <h2 class="text-3xl font-bold text-cynBlack leading-11 max-md:text-center"><?php echo esc_html(!empty($blogs_categories_title) ? $blogs_categories_title : __('دسته بندی ها', 'naghoos')); ?></h2>

            <div class="flex flex-wrap gap-2 justify-center items-center" role="tablist">
                <button
                    type="button"
                    role="tab"
                    id="tab-category-all"
                    aria-selected="<?php echo $blog_tab === 'all' ? 'true' : 'false'; ?>"
                    aria-controls="tab-content-all"
                    class="shrink-0 text-base font-semibold py-2 px-4 rounded-xl transition-all duration-300 <?php echo $blog_tab === 'all' ? 'bg-cynRed text-white' : 'text-cynBlack bg-cynBlack/10 hover:bg-cynRed hover:text-white'; ?>">
                    <?php esc_html_e('همه', 'naghoos'); ?>
                </button>

                <?php foreach ($blogs_categories as $category) : ?>
                    <button
                        type="button"
                        role="tab"
                        id="tab-category-<?php echo esc_attr($category->slug); ?>"
                        aria-selected="<?php echo $blog_tab === $category->slug ? 'true' : 'false'; ?>"
                        aria-controls="tab-content-<?php echo esc_attr($category->slug); ?>"
                        class="shrink-0 text-base font-semibold py-2 px-4 rounded-xl transition-all duration-300 <?php echo $blog_tab === $category->slug ? 'bg-cynRed text-white' : 'text-cynBlack bg-cynBlack/10 hover:bg-cynRed hover:text-white'; ?>">
                        <?php echo esc_html($category->name); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="blog-tab-contents">
            <?php foreach ($blog_category_posts as $tab_name => $tab_data) : ?>
                <div
                    role="tabpanel"
                    id="tab-content-<?php echo esc_attr($tab_name); ?>"
                    aria-labelledby="tab-category-<?php echo esc_attr($tab_name); ?>"
                    aria-hidden="<?php echo $blog_tab === $tab_name ? 'false' : 'true'; ?>"
                    class="blog-tab-content<?php echo $blog_tab === $tab_name ? ' is-active' : ''; ?>">
                    <?php
                    $post_ids = array_values(array_filter(array_map('intval', (array) ($tab_data['post_ids'] ?? []))));
                    $current  = max(1, (int) ($tab_data['current'] ?? 1));
                    $total    = max(0, (int) ($tab_data['total'] ?? 0));
                    $tab      = sanitize_key((string) ($tab_data['tab'] ?? 'all'));

                    if (empty($post_ids)) : ?>
                        <p class="col-span-full text-center text-sm font-medium text-cynBlack/50">
                            <?php esc_html_e('پستی یافت نشد.', 'naghoos'); ?>
                        </p>
                    <?php
                    else :
                        foreach ($post_ids as $post_id) :
                            Templates::getCard('blog', [
                                'post_id'    => $post_id,
                                'layout'     => 'horizontal',
                                'responsive' => true,
                                'fixed'      => true,
                            ]);
                        endforeach;

                        Templates::getPart('blog/pagination', [
                            'current'  => $current,
                            'total'    => $total,
                            'tab'      => $tab,
                            'base_url' => $blog_page_url,
                        ]);
                    endif;
                    ?>
                </div>
            <?php endforeach; ?>
        </div>

    </section>

</main>

<?php get_footer(); ?>