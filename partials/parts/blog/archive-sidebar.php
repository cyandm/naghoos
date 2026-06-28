<?php

defined('ABSPATH') || exit;

use Cyan\Theme\Helpers\Icon;

$part_args      = get_query_var('args', []);
$sidebar_suffix = isset($part_args['sidebar_suffix']) ? sanitize_html_class((string) $part_args['sidebar_suffix']) : '';

$sid = static function (string $id) use ($sidebar_suffix): string {
    return $id . ($sidebar_suffix ? '-' . $sidebar_suffix : '');
};

$blog_categories = get_terms([
    'taxonomy'   => 'category',
    'parent'     => 0,
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
    'exclude'    => [(int) get_option('default_category')],
]);

$blog_tags = get_terms([
    'taxonomy'   => 'post_tag',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

if (is_wp_error($blog_categories)) {
    $blog_categories = [];
}

if (is_wp_error($blog_tags)) {
    $blog_tags = [];
}

$current_cat_id = is_category() ? get_queried_object_id() : 0;
$current_tag_id = is_tag() ? get_queried_object_id() : 0;
?>

<aside class="blog-archive-sidebar sticky top-2">
    <?php if (! empty($blog_categories)) : ?>
        <div class="filter-cat rounded-3xl border border-cynBlack bg-[#f9f9f9] p-4">
            <button
                type="button"
                class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none"
                data-accordion-target="<?php echo esc_attr($sid('blog-archive-cat-main')); ?>"
                data-accordion-icon-rotate="180"
                aria-expanded="true"
                aria-controls="<?php echo esc_attr($sid('blog-archive-cat-main')); ?>">
                <span class="text-sm font-medium"><?php esc_html_e('دسته‌بندی ها', 'naghoos'); ?></span>
                <i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 shrink-0" style="transform: rotate(180deg);">
                    <?php Icon::print('Arrow-28'); ?>
                </i>
            </button>
            <div
                id="<?php echo esc_attr($sid('blog-archive-cat-main')); ?>"
                class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0"
                data-accordion-content="<?php echo esc_attr($sid('blog-archive-cat-main')); ?>"
                style="grid-template-rows: 1fr;">
                <div class="min-h-0 overflow-hidden">
                    <div class="pt-3 space-y-3">
                        <?php foreach ($blog_categories as $parent) :
                            $children = get_terms([
                                'taxonomy'   => 'category',
                                'parent'     => $parent->term_id,
                                'hide_empty' => true,
                                'orderby'    => 'name',
                                'order'      => 'ASC',
                            ]);
                            $has_children = ! empty($children) && ! is_wp_error($children);
                            $parent_link  = get_term_link($parent);

                            if (is_wp_error($parent_link)) {
                                continue;
                            }

                            $is_parent_current = $current_cat_id === (int) $parent->term_id;
                            $child_ids         = $has_children ? array_map(static fn($child) => (int) $child->term_id, $children) : [];
                            $current_in_children = in_array($current_cat_id, $child_ids, true);
                            $parent_open       = $is_parent_current || $current_in_children;
                            $parent_id         = $sid('blog-archive-cat-' . (int) $parent->term_id);
                            $parent_div_class  = 'filter-cat-parent rounded-xl transition-all duration-300 ';

                            if ($is_parent_current && ! $has_children) {
                                $parent_div_class .= 'bg-cynRed text-white';
                            } else {
                                $parent_div_class .= 'bg-cynWhite';
                                if (! $has_children) {
                                    $parent_div_class .= ' hover:bg-cynRed hover:text-white hover:[&_a]:text-white';
                                }
                            }
                        ?>
                            <div class="<?php echo esc_attr($parent_div_class); ?>">
                                <?php if ($has_children) : ?>
                                    <button
                                        type="button"
                                        class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none text-sm text-cynBlack rounded-xl bg-white/60 hover:bg-white/80 transition-colors py-1.5 px-2 text-start border-none"
                                        data-accordion-target="<?php echo esc_attr($parent_id); ?>"
                                        data-accordion-icon-rotate="180"
                                        aria-expanded="<?php echo $parent_open ? 'true' : 'false'; ?>"
                                        aria-controls="<?php echo esc_attr($parent_id); ?>">
                                        <span class="text-xs font-medium leading-6"><?php echo esc_html($parent->name); ?></span>
                                        <i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 shrink-0" style="transform: rotate(<?php echo $parent_open ? '180' : '0'; ?>deg);">
                                            <?php Icon::print('Arrow-28'); ?>
                                        </i>
                                    </button>
                                    <div
                                        id="<?php echo esc_attr($parent_id); ?>"
                                        class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0"
                                        data-accordion-content="<?php echo esc_attr($parent_id); ?>"
                                        style="grid-template-rows: <?php echo $parent_open ? '1fr' : '0fr'; ?>;">
                                        <div class="min-h-0 overflow-hidden">
                                            <div class="mt-3 flex flex-col gap-1.5">
                                                <?php foreach ($children as $child) :
                                                    $child_link = get_term_link($child);

                                                    if (is_wp_error($child_link)) {
                                                        continue;
                                                    }

                                                    $is_active = $current_cat_id === (int) $child->term_id;
                                                ?>
                                                    <a
                                                        href="<?php echo esc_url($child_link); ?>"
                                                        class="block p-1 rounded-md text-xs transition-all duration-300 no-underline hover:bg-cynRed hover:text-cynwhite hover:[&_a]:text-cynWhite <?php echo $is_active ? 'bg-cynRed text-white' : 'text-cynBlack bg-cynWhite'; ?>">
                                                        <?php echo esc_html($child->name); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <a href="<?php echo esc_url($parent_link); ?>" class="block py-1.5 px-2 rounded-xl text-xs font-medium leading-6 transition-colors no-underline <?php echo $is_parent_current ? 'text-white' : 'text-cynBlack'; ?>">
                                        <?php echo esc_html($parent->name); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (! empty($blog_tags)) : ?>
        <div class="filter-tag rounded-3xl border border-cynBlack/10 bg-cynBgItem/15 p-4 mt-4">
            <button
                type="button"
                class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none"
                data-accordion-target="<?php echo esc_attr($sid('blog-archive-tag-main')); ?>"
                data-accordion-icon-rotate="180"
                aria-expanded="false"
                aria-controls="<?php echo esc_attr($sid('blog-archive-tag-main')); ?>">
                <span class="text-sm font-medium"><?php esc_html_e('برچسب‌ها', 'naghoos'); ?></span>
                <i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 shrink-0" style="transform: rotate(0deg);">
                    <?php Icon::print('Arrow-28'); ?>
                </i>
            </button>
            <div
                id="<?php echo esc_attr($sid('blog-archive-tag-main')); ?>"
                class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0"
                data-accordion-content="<?php echo esc_attr($sid('blog-archive-tag-main')); ?>"
                style="grid-template-rows: 0fr;">
                <div class="min-h-0 overflow-hidden">
                    <div class="pt-3 flex flex-wrap gap-2">
                        <?php foreach ($blog_tags as $tag) :
                            $tag_link = get_term_link($tag);

                            if (is_wp_error($tag_link)) {
                                continue;
                            }

                            $is_active = $current_tag_id === (int) $tag->term_id;
                        ?>
                            <a
                                href="<?php echo esc_url($tag_link); ?>"
                                class="inline-flex items-center justify-center rounded-xl py-2 px-3 text-xs font-medium transition-all duration-300 no-underline <?php echo $is_active ? 'bg-cynRed text-white' : 'text-cynBlack bg-cynWhite hover:bg-cynRed/20'; ?>">
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</aside>