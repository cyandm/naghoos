<?php

defined('ABSPATH') || exit;

use Cyan\Theme\Helpers\Icon;

$args       = get_query_var('args', []);
$page_title = ! empty($args['page_title']) ? (string) $args['page_title'] : __('بلاگ', 'naghoos');

$term = get_queried_object();
$archive_base_url = '';

if ($term instanceof WP_Term && in_array($term->taxonomy, ['category', 'post_tag'], true)) {
    $term_link = get_term_link($term);

    if (! is_wp_error($term_link)) {
        $archive_base_url = remove_query_arg('paged', $term_link);
    }
}

if ($archive_base_url === '') {
    $archive_base_url = remove_query_arg('paged', get_pagenum_link(1, false));
}

$archive_filters_current = isset($_GET['orderby']) ? sanitize_key(wp_unslash((string) $_GET['orderby'])) : '';
$archive_filters_params  = isset($_GET) ? wp_unslash($_GET) : [];
unset($archive_filters_params['paged']);

$archive_orderby_filters = [
    [
        'orderby' => 'date',
        'label'   => __('جدیدترین', 'naghoos'),
        'icon'    => 'fire',
    ],
    [
        'orderby' => 'date-asc',
        'label'   => __('قدیمی‌ترین', 'naghoos'),
        'icon'    => 'sort-ascending',
    ],
    [
        'orderby' => 'popularity',
        'label'   => __('پربازدیدترین', 'naghoos'),
        'icon'    => 'Heart,-Favorite,-Love',
    ],
];

$build_sort_url = static function (string $orderby) use ($archive_filters_params, $archive_base_url, $archive_filters_current): string {
    $params = $archive_filters_params;

    if ($orderby === 'date') {
        $active = ($archive_filters_current === '' || $archive_filters_current === 'date');
    } else {
        $active = ($archive_filters_current === $orderby);
    }

    if ($active) {
        unset($params['orderby']);
    } else {
        $params['orderby'] = $orderby;
    }

    return add_query_arg($params, $archive_base_url);
};

$is_sort_active = static function (string $orderby) use ($archive_filters_current): bool {
    if ($orderby === 'date') {
        return $archive_filters_current === '' || $archive_filters_current === 'date';
    }

    return $archive_filters_current === $orderby;
};
?>

<div class="flex justify-between items-center flex-wrap gap-3">
    <h1 class="text-3xl font-bold text-cynBlack leading-11 max-md:text-center">
        <?php echo esc_html($page_title); ?>
    </h1>

    <div class="flex gap-2 flex-wrap">
        <?php foreach ($archive_orderby_filters as $filter) :
            $active = $is_sort_active($filter['orderby']);
            $filter_url = $build_sort_url($filter['orderby']);
            ?>
            <a
                href="<?php echo esc_url($filter_url); ?>"
                class="max-xl:hidden py-3 px-3 rounded-3xl flex gap-1 items-center transition-colors no-underline hover:bg-cynRed hover:text-cynwhite hover:[&_span]:text-cynWhite hover:[&_i]:text-cynWhite transition-all duration-300 <?php echo $active ? 'bg-cynRed text-white' : 'bg-cynBgItem/15 text-cynBlack'; ?>">
                <span class="text-sm font-medium"><?php echo esc_html($filter['label']); ?></span>
                <i class="size-5 stroke-[1.5]">
                    <?php Icon::print($filter['icon']); ?>
                </i>
            </a>
        <?php endforeach; ?>

        <div class="xl:hidden cursor-pointer relative py-3 px-3 rounded-3xl flex gap-1 items-center transition-all duration-300 bg-cynBgItem/15 hover:bg-cynRed hover:text-cynwhite hover:[&_span]:text-cynWhite hover:[&_i]:text-cynWhite">
            <i class="size-5 text-cynBlack">
                <?php Icon::print('sort-ascending'); ?>
            </i>
            <span class="text-sm font-medium text-cynBlack"><?php esc_html_e('مرتب سازی', 'naghoos'); ?></span>
            <select
                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                aria-label="<?php esc_attr_e('مرتب سازی', 'naghoos'); ?>"
                data-blog-archive-sort>
                <?php foreach ($archive_orderby_filters as $filter) :
                    $active = $is_sort_active($filter['orderby']);
                    ?>
                    <option value="<?php echo esc_url($build_sort_url($filter['orderby'])); ?>" <?php selected($active); ?>>
                        <?php echo esc_html($filter['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div
            class="lg:hidden cursor-pointer py-3 px-3 rounded-3xl flex gap-1 items-center transition-all duration-300 bg-cynBgItem/15 hover:bg-cynRed hover:text-cynwhite hover:[&_span]:text-cynWhite hover:[&_i]:text-cynWhite"
            role="button"
            tabindex="0"
            aria-label="<?php esc_attr_e('نمایش فیلتر ها', 'naghoos'); ?>"
            modal-opener
            data-modal-name="blog-filter-modal">
            <i class="size-5 text-cynBlack">
                <?php Icon::print('Filter,-Sort-1'); ?>
            </i>
            <span class="text-sm font-medium text-cynBlack"><?php esc_html_e('نمایش فیلتر ها', 'naghoos'); ?></span>
        </div>
    </div>
</div>
