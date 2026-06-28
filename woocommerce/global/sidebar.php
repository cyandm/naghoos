<?php

/**
 * Sidebar
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/sidebar.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

use Cyan\Theme\Helpers\Icon;

$sidebar_suffix = isset($sidebar_suffix) ? sanitize_html_class($sidebar_suffix) : '';
$sid = function ($id) use ($sidebar_suffix) {
	return $id . ($sidebar_suffix ? '-' . $sidebar_suffix : '');
};

$uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
$exclude_cat_ids = ($uncategorized && ! is_wp_error($uncategorized)) ? [(int) $uncategorized->term_id] : [];

$product_cats = get_terms([
	'taxonomy'   => 'product_cat',
	'parent'     => 0,
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
	'exclude'    => $exclude_cat_ids,
]);
$current_cat_id = is_tax('product_cat') ? get_queried_object_id() : 0;

$product_ages = get_terms([
	'taxonomy'   => 'product_age',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
]);
$current_age_slug = isset($_GET['product_age']) ? sanitize_title(wp_unslash($_GET['product_age'])) : '';

// Base URL for taxonomy filters – keep current query, drop paged
$shop_base_link = (is_shop() || is_product_taxonomy()) ? remove_query_arg('paged', get_pagenum_link(1, false)) : '';

// Query keys to preserve when switching category (so filters don’t reset)
$sidebar_filter_query_keys = ['min_price', 'max_price', 'instock', 'product_age'];

if (! function_exists('taghechian_term_link_with_filters')) {
	/**
	 * Append current sidebar filter query args to a term link (e.g. category) so filters stay in URL.
	 *
	 * @param string $term_link Term URL (e.g. from get_term_link()).
	 * @return string
	 */
	function taghechian_term_link_with_filters($term_link)
	{
		global $sidebar_filter_query_keys;
		if (! is_array($sidebar_filter_query_keys) || empty($_GET)) {
			return $term_link;
		}
		$params = array_intersect_key($_GET, array_flip($sidebar_filter_query_keys));
		$params = array_map(function ($v) {
			return is_string($v) ? wc_clean(wp_unslash($v)) : $v;
		}, $params);
		return add_query_arg($params, $term_link);
	}
}

if (! function_exists('taghechian_taxonomy_filter_term_link')) {
	/**
	 * Build URL that toggles a single taxonomy filter (product_age) in the current URL.
	 *
	 * @param string $base_link  Base URL (with current query).
	 * @param string $taxonomy   Taxonomy name (query var).
	 * @param object $term       Term object.
	 * @param bool   $is_active  Whether this term is currently selected.
	 * @return string
	 */
	function taghechian_taxonomy_filter_term_link($base_link, $taxonomy, $term, $is_active)
	{
		if (! $base_link) {
			return '#';
		}
		$link = remove_query_arg($taxonomy, $base_link);
		if (! $is_active) {
			$link = add_query_arg($taxonomy, $term->slug, $link);
		}
		return $link;
	}
}

?>

<aside class="shop-sidebar sticky top-2">
	<?php
	if (is_active_sidebar('shop')) {
		dynamic_sidebar('shop');
	}
	?>

	<?php
	$instock_active = isset($_GET['instock']) && $_GET['instock'] === '1';
	?>
	<div class="flex justify-between items-center gap-2 rounded-3xl border border-cynBorder bg-cynBgItem/15 p-4">
		<span class="text-cynBlack text-xs font-medium"><?php _e('نمایش کالاهای موجود', 'taghechian'); ?></span>
		<button type="button" role="switch" aria-checked="<?php echo $instock_active ? 'true' : 'false'; ?>" data-instock-toggle class="instock-toggle-btn <?php echo $instock_active ? 'is-on ' : ''; ?>relative h-6 w-11 flex-shrink-0 rounded-full transition-all duration-300 focus:outline-none" aria-label="<?php esc_attr_e('نمایش کالاهای موجود', 'taghechian'); ?>">
			<span class="absolute top-1 start-1 h-4 w-4 rounded-full bg-white shadow-sm transition-all duration-300" data-toggle-knob></span>
		</button>
	</div>

	<div class="filter-cat rounded-3xl border border-cynBorder bg-cynBgItem/15 p-4 mt-4">
		<!-- Product categories (main) -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-cat-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="true" aria-controls="<?php echo esc_attr($sid('filter-cat-main')); ?>">
			<span class="text-sm font-medium"><?php _e('دسته‌بندی کالاها', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-all duration-300 flex-shrink-0" style="transform: rotate(180deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>
		<div id="<?php echo esc_attr($sid('filter-cat-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-cat-main')); ?>" style="grid-template-rows: 1fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-3">
					<?php
					if (!empty($product_cats) && !is_wp_error($product_cats)) {
						foreach ($product_cats as $parent) {
							$children = get_terms([
								'taxonomy'   => 'product_cat',
								'parent'     => $parent->term_id,
								'hide_empty' => true,
								'orderby'    => 'name',
								'order'      => 'ASC',
							]);
							$has_children = !empty($children) && !is_wp_error($children);
							$parent_link = taghechian_term_link_with_filters(get_term_link($parent));
							$is_parent_current = ($current_cat_id === (int) $parent->term_id);
							$child_ids = $has_children ? array_map(function ($c) {
								return (int) $c->term_id;
							}, $children) : [];
							$current_in_children = in_array($current_cat_id, $child_ids, true);
							$parent_open = $is_parent_current || $current_in_children;
							$parent_id = $sid('filter-cat-' . (int) $parent->term_id);
							$parent_div_class = 'filter-cat-parent rounded-xl transition-all duration-300 ';
							if ($is_parent_current && ! $has_children) {
								$parent_div_class .= 'bg-cynRed text-white';
							} else {
								$parent_div_class .= 'bg-cynWhite';
								if (! $has_children) {
									$parent_div_class .= ' hover:bg-cynRed hover:text-white hover:[&_a]:!text-white';
								}
							}
					?>
							<div class="<?php echo esc_attr($parent_div_class); ?>">
								<?php if ($has_children) : ?>
									<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none text-sm text-cynBlack rounded-xl bg-white/60 hover:bg-white/80 transition-all py-2 px-3 text-start border-none" data-accordion-target="<?php echo esc_attr($parent_id); ?>" data-accordion-icon-rotate="180" aria-expanded="<?php echo $parent_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($parent_id); ?>">
										<span class="text-xs font-medium leading-6"><?php echo esc_html($parent->name); ?></span>
										<i class="accordion-icon size-6 stroke-[1.5] transition-all duration-300 flex-shrink-0" style="transform: rotate(<?php echo $parent_open ? '180' : '0'; ?>deg);">
											<?php Icon::print('Arrow-28'); ?>
										</i>
									</button>
									<div id="<?php echo esc_attr($parent_id); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0 px-3" data-accordion-content="<?php echo esc_attr($parent_id); ?>" style="grid-template-rows: <?php echo $parent_open ? '1fr' : '0fr'; ?>;">
										<div class="min-h-0 overflow-hidden">
											<div class="mt-1 flex flex-col gap-1.5 pb-2">
												<?php
												foreach ($children as $child) {
													$child_link = taghechian_term_link_with_filters(get_term_link($child));
													$is_active = ($current_cat_id === (int) $child->term_id);
												?>
													<a href="<?php echo esc_url($child_link); ?>" class="block p-1 rounded-md text-xs text-cynBlack transition-all duration-300 <?php echo $is_active ? 'bg-cynRed text-white' : 'bg-[#F5F5F5] hover:bg-cynRed hover:text-white'; ?>">
														<?php echo esc_html($child->name); ?>
													</a>
												<?php } ?>
											</div>
										</div>
									</div>
								<?php else : ?>
									<a href="<?php echo esc_url($parent_link); ?>" class="block py-1.5 px-2 text-xs font-medium leading-6 hover:text-white transition-all duration-300 <?php echo $is_parent_current ? 'text-white' : 'text-cynBlack'; ?>">
										<?php echo esc_html($parent->name); ?>
									</a>
								<?php endif; ?>
							</div>
					<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="filter-age rounded-3xl border border-cynBorder bg-cynBgItem/15 p-4 mt-4">
		<!-- Product filter by age -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-age-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-age-main')); ?>">
			<span class="text-sm font-medium"><?php _e('گروه سنی', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-all duration-300 flex-shrink-0" style="transform: rotate(0deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>
		<div id="<?php echo esc_attr($sid('filter-age-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-age-main')); ?>" style="grid-template-rows: 0fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-1.5">
					<?php
					if ($shop_base_link && ! empty($product_ages) && ! is_wp_error($product_ages)) {
						foreach ($product_ages as $age) {
							$age_link = taghechian_taxonomy_filter_term_link($shop_base_link, 'product_age', $age, $current_age_slug === $age->slug);
							$is_active = ($current_age_slug === $age->slug);
					?>
							<a href="<?php echo esc_url($age_link); ?>" class="block rounded-xl py-2 px-3 text-xs font-medium leading-6 transition-all duration-300 hover:bg-cynRed hover:text-white <?php echo $is_active ? 'bg-cynRed text-white' : 'bg-cynWhite text-cynBlack'; ?>">
								<?php echo esc_html($age->name); ?>
							</a>
					<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="filter-price rounded-3xl border border-cynBorder bg-cynBgItem/15 p-4 mt-4">
		<!-- Product filter by price -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-price-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-price-main')); ?>">
			<span class="text-sm font-medium"><?php _e('رنج قیمت', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-all duration-300 flex-shrink-0" style="transform: rotate(0deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>

		<div id="<?php echo esc_attr($sid('filter-price-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-price-main')); ?>" style="grid-template-rows: 0fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-1.5">
					<?php
					if ((is_shop() || is_product_taxonomy()) && class_exists('WC_Widget_Price_Filter')) {
						the_widget(
							'WC_Widget_Price_Filter',
							array('title' => ''),
							array(
								'before_widget' => '',
								'after_widget'  => '',
								'before_title'  => '',
								'after_title'   => '',
							)
						);
					}
					?>
				</div>
			</div>
		</div>
	</div>

</aside>
<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
