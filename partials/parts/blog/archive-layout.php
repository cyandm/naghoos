<?php

defined('ABSPATH') || exit;

use Cyan\Theme\Classes\ViewsCount;
use Cyan\Theme\Helpers\Templates;

global $wp_query;

$term           = get_queried_object();
$posts_per_page = 24;
$paged          = max(1, (int) get_query_var('paged'));
$orderby        = isset($_GET['orderby']) ? sanitize_key(wp_unslash((string) $_GET['orderby'])) : '';

$query_args = array_merge($wp_query->query_vars, [
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
]);

if ($orderby === 'date-asc') {
    $query_args['orderby'] = 'date';
    $query_args['order']   = 'ASC';
    unset($query_args['meta_key'], $query_args['meta_value'], $query_args['meta_value_num']);
} elseif ($orderby === 'popularity') {
    $query_args['meta_key'] = ViewsCount::META_KEY;
    $query_args['orderby']  = 'meta_value_num';
    $query_args['order']    = 'DESC';
} else {
    $query_args['orderby'] = 'date';
    $query_args['order']   = 'DESC';
    unset($query_args['meta_key'], $query_args['meta_value'], $query_args['meta_value_num']);
}

$archive_query = new WP_Query($query_args);

if (is_category()) {
    $page_title = single_cat_title('', false);
} elseif (is_tag()) {
    $page_title = single_tag_title('', false);
} elseif (is_author()) {
    $page_title = get_the_author();
} elseif (is_date()) {
    $page_title = get_the_date(_x('F Y', 'monthly archives date format', 'naghoos'));
} else {
    $page_title = __('بلاگ', 'naghoos');
}

$current_page = $paged;
$total_pages  = (int) $archive_query->max_num_pages;
?>

<main class="blog-archive">
    <section class="container grid grid-cols-1 lg:grid-cols-13 gap-6">
        <aside class="lg:col-span-4 xl:col-span-3 order-2 lg:order-1 max-lg:hidden">
            <?php Templates::getPart('blog/archive-sidebar'); ?>
        </aside>

        <div class="lg:col-span-9 xl:col-span-10 order-1 lg:order-2 flex flex-col gap-3">
            <?php Templates::getPart('blog/archive-toolbar', ['page_title' => $page_title]); ?>

            <?php if ($archive_query->have_posts()) : ?>
                <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4 mt-2">
                    <?php
                    while ($archive_query->have_posts()) :
                        $archive_query->the_post();
                        Templates::getCard('blog', [
                            'post_id'    => get_the_ID(),
                            'layout'     => 'horizontal',
                            'responsive' => true,
                            'fixed'      => true,
                        ]);
                    endwhile;

                    wp_reset_postdata();
                    ?>

                    <?php
                    Templates::getPart('blog/pagination', [
                        'mode'    => 'archive',
                        'current' => $current_page,
                        'total'   => $total_pages,
                    ]);
                    ?>
                </div>
            <?php else : ?>
                <p class="text-center text-sm font-medium text-cynBlack/50 mt-8">
                    <?php esc_html_e('پستی یافت نشد.', 'naghoos'); ?>
                </p>
            <?php endif; ?>

            <?php if ($term instanceof WP_Term && ! empty($term->description)) : ?>
                <div class="mt-4 rounded-3xl bg-cynBgItem/15 border border-cynBlack/10 p-4 text-cynBlack text-base leading-8 [&_a]:text-cynRed [&_p]:my-3">
                    <?php echo apply_filters('the_content', $term->description); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php Templates::getPart('blog/archive-mobile-filter'); ?>
</main>
