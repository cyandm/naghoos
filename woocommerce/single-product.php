<?php

/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
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
use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Classes\WooCommerce as ThemeWooCommerce;

// Get current product
$product = wc_get_product(get_the_ID());

// Check if product exists
if (!$product) {
	return;
}


$wishlist_is_liked = ThemeWooCommerce::isProductInCurrentUserWishlist($product->get_id());
$wishlist_toggle_url = ThemeWooCommerce::getWishlistToggleUrl($product->get_id());

// Get featured image as fallback for video covers
$featured_image_id = get_post_thumbnail_id(get_the_ID());

$product_excert = get_field('product_excert', $product->get_id());
$product_excert_voice = get_field('product_excert_voice', $product->get_id());
$product_size_guide = get_field('product_size_guide', $product->get_id());

$related_products_query = ThemeWooCommerce::getRelatedProductsQuery($product->get_id());

$attributes = $product->get_attributes();

get_header();
?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="single-product-page container">

	<?php do_action('woocommerce_before_single_product'); ?>

	<section class="flex flex-col md:flex-row gap-3">

		<?php
		$featured_id  = $product->get_image_id();
		$gallery_ids = $product->get_gallery_image_ids();
		$image_ids   = array_filter(array_merge([$featured_id], $gallery_ids ?: []));
		if (empty($image_ids)) {
			$image_ids = [0];
		}
		?>

		<div class="sr-only" aria-hidden="true">
			<?php foreach ($image_ids as $img_id) : ?>
				<?php if ($img_id) : ?>
					<a href="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'full')); ?>" data-fancybox="product-gallery"></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<div class="w-full md:w-1/2 flex flex-col-reverse md:flex-row gap-2 h-fit md:sticky md:top-1">
			<!-- Thumbnails Slider -->
			<div class="flex-shrink-0 flex md:flex-col gap-2 order-first md:order-1">
				<swiper-container
					id="product-gallery-thumbs"
					class="product-gallery-thumbs w-full h-auto md:w-[100px] lg:w-[120px] md:h-auto max-h-[815px]"
					slides-per-view="auto"
					space-between="8"
					direction="horizontal"
					breakpoints='{"768":{"direction":"vertical","slidesPerView":"auto","spaceBetween":8}}'>
					<?php foreach ($image_ids as $index => $img_id) : ?>
						<?php if ($img_id) : ?>
							<swiper-slide class="w-[120px] h-[140px] md:w-[100px] lg:w-[120px] cursor-pointer opacity-60 hover:opacity-100 transition-opacity [&.swiper-slide-thumb-active]:!opacity-100 overflow-hidden border border-cynBlack/10 py-4 px-6">
								<div
									class="w-full h-full cursor-pointer"
									data-fancybox-delegate="product-gallery"
									data-fancybox-index="<?php echo (int) $index; ?>">
									<?php echo wp_get_attachment_image($img_id, 'full', false, ['class' => 'w-full h-full object-cover']); ?>
								</div>
							</swiper-slide>
						<?php else : ?>
							<swiper-slide class="!w-[120px] !h-[135px] cursor-pointer opacity-60 [&.swiper-slide-thumb-active]:!opacity-100">
								<div class="w-full h-full"><?php echo wc_placeholder_img('thumbnail'); ?></div>
							</swiper-slide>
						<?php endif; ?>
					<?php endforeach; ?>
				</swiper-container>
			</div>

			<!-- Main Gallery Slider -->
			<div class="relative flex-1 overflow-hidden order-2 md:order-2">
				<swiper-container
					id="product-gallery-main"
					class="product-gallery-main w-full h-full overflow-hidden"
					slides-per-view="1"
					space-between="0"
					loop="true"
					thumbs-swiper="#product-gallery-thumbs"
					navigation="true"
					navigation-next-el="#productGalleryNext"
					navigation-prev-el="#productGalleryPrev">
					<?php foreach ($image_ids as $index => $img_id) : ?>
						<?php if ($img_id) : ?>
							<swiper-slide class="!h-full">
								<div class="w-[450px] h-full flex items-center justify-center bg-[#FCFCFC] overflow-hidden [&_img]:w-full [&_img]:h-full [&_img]:object-cover cursor-zoom-in m-auto"
									data-fancybox-delegate="product-gallery"
									data-fancybox-index="<?php echo (int) $index; ?>">
									<?php echo wp_get_attachment_image($img_id, 'full', false, ['class' => 'w-full h-full object-cover']); ?>
								</div>
							</swiper-slide>
						<?php else : ?>
							<swiper-slide class="!h-full">
								<div class="w-full h-full flex items-center justify-center bg-[#FCFCFC] overflow-hidden [&_img]:w-full [&_img]:h-full [&_img]:object-cover">
									<?php echo wc_placeholder_img('woocommerce_single'); ?>
								</div>
							</swiper-slide>
						<?php endif; ?>
					<?php endforeach; ?>
				</swiper-container>
				<!-- Navigation Buttons -->
				<div class="absolute left-1/2 -translate-x-1/2 bottom-3 z-10 flex items-center justify-center gap-3">
					<button type="button" id="productGalleryPrev" class="size-9 rounded-full bg-cynBlack text-white flex items-center justify-center hover:opacity-90 transition-opacity duration-300" aria-label="<?php echo esc_attr(__('قبلی', 'taghechian')); ?>">
						<i class="size-5 stroke-[1.5]"><?php Icon::print('Arrow-19'); ?></i>
					</button>
					<button type="button" id="productGalleryNext" class="size-9 rounded-full bg-cynBlack text-white flex items-center justify-center hover:opacity-90 transition-opacity duration-300" aria-label="<?php echo esc_attr(__('بعدی', 'taghechian')); ?>">
						<i class="size-5 stroke-[1.5]"><?php Icon::print('Arrow-27'); ?></i>
					</button>
				</div>
			</div>
		</div>

		<div class="w-full md:w-1/2">

			<div class="flex flex-col gap-1">
				<h1 class="text-3xl font-[Dinar] leading-14 text-cynBlack"><?php echo get_the_title(); ?></h1>

				<div class="flex items-center gap-2 flex-wrap">
					<?php
					$categories = get_the_terms(get_the_ID(), 'product_cat');
					if ($categories && !is_wp_error($categories)) :
						foreach ($categories as $category) :
							// Skip uncategorized
							if ($category->slug === 'uncategorized' || $category->slug === 'بدون-دسته-بندی') {
								continue;
							}
					?>
							<a href="<?php echo get_term_link($category); ?>" class="text-cynBlack text-xs font-medium py-1 px-3 rounded-md bg-cynBgItem/15 whitespace-nowrap">
								<?php echo $category->name; ?>
							</a>
					<?php
						endforeach;
					endif;
					?>
				</div>
			</div>

			<?php
			$stock_quantity = $product->get_stock_quantity();
			$is_in_stock = $product->is_in_stock();
			?>

			<div id="stock-status-wrapper" class="mt-3.5 transition-all duration-300 ease-out <?php echo (!$is_in_stock || ($stock_quantity !== null && $stock_quantity <= 4)) ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-1 pointer-events-none'; ?>">
				<div class="py-1 px-3 rounded-md bg-[#dd4a4a]/14 flex items-center justify-center w-fit">
					<p class="text-[#dd4a4a] text-xs font-medium" id="stock-status-text">
						<?php if (!$is_in_stock) : ?>
							<?php _e('ناموجود', 'taghechian'); ?>
						<?php elseif ($stock_quantity !== null && $stock_quantity <= 4) : ?>
							<?php printf(__('فقط %s عدد باقیمانده', 'taghechian'), $stock_quantity); ?>
						<?php endif; ?>
					</p>
				</div>
			</div>

			<?php if (!empty($attributes) && is_array($attributes)) : ?>

				<hr class="border-cynBgItem/30 h-px w-full my-5">

				<div class="text-base font-medium text-cynBlack flex flex-col gap-5 [&_div]:flex [&_div]:items-center [&_div]:justify-between [&_div]:bg-[#e9e9e9] [&_div]:p-2 [&_div]:rounded-xl">

					<?php foreach ($attributes as $attribute) :

						if (!$attribute->get_visible()) {
							continue;
						}

						$label = wc_attribute_label($attribute->get_name());

						if ($attribute->is_taxonomy()) :

							$terms = wc_get_product_terms(
								$product->get_id(),
								$attribute->get_name(),
								['fields' => 'names']
							);

							if (!empty($terms)) : ?>
								<div class="flex justify-between">
									<span class="font-medium text-cynBlack">
										<?php echo esc_html($label); ?>
									</span>
									<span>
										<?php echo esc_html(implode('، ', $terms)); ?>
									</span>
								</div>
							<?php endif; ?>

							<?php else :

							$options = $attribute->get_options();

							if (!empty($options)) : ?>
								<div class="flex justify-between">
									<span class="font-medium text-cynBlack">
										<?php echo esc_html($label); ?>
									</span>
									<span>
										<?php echo esc_html(implode('، ', $options)); ?>
									</span>
								</div>
							<?php endif; ?>

					<?php endif;

					endforeach; ?>

				</div>

			<?php endif; ?>

			<hr class="border-cynBgItem/30 h-px w-full my-5">

			<div class="flex items-center justify-between">
				<p class="text-cynBlack text-xl font-medium">
					<?php _e('قیمت', 'taghechian'); ?>
				</p>
				<p class="text-cynBlack text-xl font-medium transition-all duration-300 [&_span.price]:flex [&_span.price]:flex-col [&_del]:font-medium [&_del]:text-base [&_ins]:text-[#DD4A4A] [&_ins]:no-underline" id="product-price-display">
					<?php
					// Check stock status
					if (!$is_in_stock) {
						// Out of stock - show "unavailable" label instead of price
						echo '<span class="text-[#dd4a4a]">' . __('ناموجود', 'taghechian') . '</span>';
					} elseif ($product->is_type('variable')) {
						$prices = $product->get_variation_prices(true);
						$min_price = current($prices['price']);
						$max_price = end($prices['price']);

						if ($min_price === $max_price) {
							// All variations have same price
							echo wc_price($min_price);
						} else {
							// Different prices - show minimum with "from" prefix
							echo sprintf(__('از %s', 'taghechian'), wc_price($min_price));
						}
					} else {
						// Simple product
						echo $product->get_price_html();
					}
					?>
				</p>
			</div>

			<?php
			$has_size_attr = false;
			if ($product->is_type('variable')) {
				$size_attr_names = array_merge(
					array_keys($product->get_variation_attributes()),
					array_keys($product->get_attributes())
				);
				foreach ($size_attr_names as $attr_name) {
					$slug = strtolower($attr_name);
					if (strpos($slug, 'size') !== false || strpos($slug, 'سایز') !== false || strpos($slug, 'اندازه') !== false) {
						$has_size_attr = true;
						break;
					}
				}
			}
			?>

			<?php if ($has_size_attr) : ?>

				<hr class="border-cynBgItem/30 h-px w-full my-5">

				<div class="flex flex-col gap-3 w-full">

					<div class="flex items-center justify-between">

						<div class="flex items-center gap-2 justify-between w-full">

							<p class="text-cynBlack text-xl font-medium">
								<?php _e('انتخاب سایز', 'taghechian'); ?>
							</p>

							<?php if ($product_size_guide): ?>

								<div class="text-cynBlue flex items-center pt-0.5 justify-between" id="sizeGuide" modal-opener data-modal-name="size-guide-modal">
									<span class="text-sm font-normal cursor-pointer">
										<?php _e('راهنمای سایز', 'taghechian'); ?>
									</span>

									<i class="size-5"><?php Icon::print('ruler-1'); ?></i>
								</div>

								<div class="container flex justify-center items-center h-fit top-1/2 -translate-y-1/2 fixed inset-0 z-50 opacity-0 pointer-events-none w-full md:!w-6/10 data-[active='true']:opacity-100 data-[active='true']:pointer-events-auto duration-500"
									modal
									data-modal-name="size-guide-modal"
									data-active="false">

									<div class="w-full px-6 pb-6 pt-8 bg-cynWhite rounded-3xl shadow-item flex flex-col gap-6 justify-center items-center relative border border-cynBlack/10">

										<div class="absolute top-3 right-3 w-fit cursor-pointer flex items-center"
											modal-closer
											data-modal-name="size-guide-modal">

											<i class="size-8 text-cynBlack"><?php Icon::print('Delete,-Disabled'); ?></i>
											<span class="text-xs font-semibold pb-0.5">
												<?php _e('بستن', 'taghechian'); ?>
											</span>
										</div>

										<div class="flex flex-col gap-4 text-cynBlack w-full">

											<p class="text-2xl font-normal text-center">
												<?php _e('راهنمای سایز', 'taghechian'); ?>
											</p>

											<p class="text-sm font-light text-center">
												<?php _e('برای انتخاب سایز مناسب، اندازه‌های خود را با جدول زیر مقایسه کنید.', 'taghechian'); ?>
											</p>

											<div class="w-full overflow-x-auto text-sm text-center transition-all [&_table]:w-full [&_table]:border-collapse [&_table]:rounded-xl [&_table]:overflow-hidden [&_th]:bg-[#ffd000] [&_th]:text-black [&_th]:font-semibold [&_th]:px-4 [&_th]:py-3 [&_th]:border [&_th]:border-gray-200 [&_td]:px-4 [&_td]:py-3 [&_td]:border [&_td]:border-gray-200 [&_tr:nth-child(even)]:bg-gray-50 [&_tr]:transition-colors [&_tr]:duration-200 [&_tr:hover]:bg-yellow-50">
												<?= $product_size_guide ?>
											</div>

										</div>

									</div>

								</div>

							<?php endif; ?>

						</div>

						<div class="w-fit xl:hidden">

							<div class="flex items-center gap-2">

								<button id="sizePrev" class="text-cynBlack cursor-pointer pointer-events-auto">
									<div class="size-5">
										<?php icon::print('Arrow-19') ?>
									</div>
								</button>

								<button id="sizeNext" class="text-cynBlack cursor-pointer pointer-events-auto">
									<div class="size-5">
										<?php icon::print('Arrow-27') ?>
									</div>
								</button>

							</div>

						</div>

					</div>

					<div class="w-full">
						<?php if ($product->is_type('variable')) : ?>
							<?php
							$available_variations = $product->get_available_variations();
							$attributes = $product->get_variation_attributes();

							// Get size attribute (try common attribute names)
							$size_attribute = null;
							$size_attribute_name = '';
							foreach ($attributes as $attribute_name => $options) {
								$attr_slug = strtolower($attribute_name);
								if (strpos($attr_slug, 'size') !== false || strpos($attr_slug, 'سایز') !== false || strpos($attr_slug, 'اندازه') !== false) {
									$size_attribute = $options;
									$size_attribute_name = $attribute_name;
									break;
								}
							}

							if ($size_attribute) :
							?>
								<swiper-container
									class="size-swiper bg-[#f9f9f9]"
									slides-per-view="auto"
									space-between="0"
									grab-cursor="true"
									navigation="true"
									navigation-next-el="#sizeNext"
									navigation-prev-el="#sizePrev">
									<?php foreach ($size_attribute as $size) : ?>
										<swiper-slide class="!w-auto">
											<button
												type="button"
												class="size-option bg-[#f9f9f9] min-w-[72px] h-9 px-4 py-2 text-sm font-medium text-cynBlack/50 border border-transparent hover:rounded-lg hover:border-cynBgItem transition-all duration-300 hover:text-cynBlack [&.active]:text-cynBlack [&.active]:border-cynBgItem [&.active]:bg-white [&.active]:rounded-lg"
												data-size="<?php echo esc_attr($size); ?>"
												data-attribute="attribute_<?php echo sanitize_title($size_attribute_name); ?>">
												<?php echo esc_html($size); ?>
											</button>
										</swiper-slide>
									<?php endforeach; ?>
								</swiper-container>
							<?php endif; ?>
						<?php endif; ?>
					</div>

				</div>

			<?php endif; ?>

			<?php if (!empty($product_size_guide) && ($product->is_type('simple') || ($product->is_type('variable') && !$has_size_attr))) : ?>
				<hr class="border-cynBgItem/30 h-px w-full my-5">

				<div class="flex items-center justify-between">

					<div class="flex items-center gap-2 justify-between w-full">

						<p class="text-cynBlack text-xl font-medium">
							<?php _e('سایز', 'taghechian'); ?>
						</p>

						<?php if ($product_size_guide): ?>

							<div class="text-cynBlue flex items-center pt-0.5" id="sizeGuide" modal-opener data-modal-name="size-guide-modal">
								<span class="text-sm font-normal cursor-pointer">
									<?php _e('اطلاعات سایز', 'taghechian'); ?>
								</span>

								<i class="size-5"><?php Icon::print('ruler-1'); ?></i>
							</div>

							<div class="container flex justify-center items-center h-fit top-1/2 -translate-y-1/2 fixed inset-0 z-50 opacity-0 pointer-events-none w-full md:!w-6/10 data-[active='true']:opacity-100 data-[active='true']:pointer-events-auto duration-500"
								modal
								data-modal-name="size-guide-modal"
								data-active="false">

								<div class="w-full px-6 pb-6 pt-8 bg-cynWhite rounded-3xl shadow-item flex flex-col gap-6 justify-center items-center relative border border-cynBlack/10">

									<div class="absolute top-3 right-3 w-fit cursor-pointer flex items-center"
										modal-closer
										data-modal-name="size-guide-modal">

										<i class="size-8 text-cynBlack"><?php Icon::print('Delete,-Disabled'); ?></i>
										<span class="text-xs font-semibold pb-0.5">
											<?php _e('بستن', 'taghechian'); ?>
										</span>
									</div>

									<div class="flex flex-col gap-4 text-cynBlack w-full">

										<p class="text-2xl font-normal text-center">
											<?php _e('راهنمای سایز', 'taghechian'); ?>
										</p>

										<p class="text-sm font-light text-center">
											<?php _e('برای انتخاب سایز مناسب، اندازه‌های خود را با جدول زیر مقایسه کنید.', 'taghechian'); ?>
										</p>

										<div class="w-full overflow-x-auto text-sm text-center transition-all [&_table]:w-full [&_table]:border-collapse [&_table]:rounded-xl [&_table]:overflow-hidden [&_th]:bg-[#ffd000] [&_th]:text-black [&_th]:font-semibold [&_th]:px-4 [&_th]:py-3 [&_th]:border [&_th]:border-gray-200 [&_td]:px-4 [&_td]:py-3 [&_td]:border [&_td]:border-gray-200 [&_tr:nth-child(even)]:bg-gray-50 [&_tr]:transition-colors [&_tr]:duration-200 [&_tr:hover]:bg-yellow-50">
											<?= $product_size_guide ?>
										</div>

									</div>

								</div>

							</div>

						<?php endif; ?>

					</div>

				</div>

			<?php endif; ?>

			<?php
			$has_color_attr = false;

			if ($product->is_type('variable')) {
				foreach ($product->get_variation_attributes() as $attr_name => $opts) {
					$slug = strtolower($attr_name);
					if (strpos($slug, 'color') !== false || strpos($slug, 'رنگ') !== false) {
						$has_color_attr = true;
						break;
					}
				}
			}
			?>

			<?php if ($has_color_attr) : ?>
				<div class="flex flex-col gap-3 w-full mt-7">

					<div class="flex items-center">

						<p class="text-cynBlack text-xl font-medium">
							<?php _e('انتخاب رنگ', 'taghechian'); ?>
						</p>

					</div>

					<div class="w-full">
						<?php if ($product->is_type('variable')) : ?>
							<?php
							$available_variations = $product->get_available_variations();
							$attributes = $product->get_variation_attributes();

							// Get color attribute
							$color_attribute = null;
							$color_attribute_name = '';
							foreach ($attributes as $attribute_name => $options) {
								$attr_slug = strtolower($attribute_name);
								if (strpos($attr_slug, 'color') !== false || strpos($attr_slug, 'رنگ') !== false) {
									$color_attribute = $options;
									$color_attribute_name = $attribute_name;
									break;
								}
							}

							if ($color_attribute) :
							?>
								<div class="flex items-center gap-3">
									<?php
									foreach ($color_attribute as $color_term_slug) :
										// Get taxonomy name - check if it already has 'pa_' prefix
										$taxonomy_name = (strpos($color_attribute_name, 'pa_') === 0) ? $color_attribute_name : 'pa_' . $color_attribute_name;

										// Get term by slug
										$term = get_term_by('slug', $color_term_slug, $taxonomy_name);
										if ($term) {
											$color_code = get_term_meta($term->term_id, 'term_color', true);
											$color_code = $color_code ? $color_code : '#cccccc';
									?>
											<button
												type="button"
												class="color-option group grid grid-cols-[auto_0fr] hover:grid-cols-[auto_1fr] [&.active]:grid-cols-[auto_1fr] items-center rounded-full border border-transparent hover:border-cynBgItem [&.active]:border-cynBgItem [&.active_.color-circle]:border-cynBgItem/25 [&.active_.color-circle]:hover:border-cynBgItem/25 p-1 transition-all duration-300"
												data-color="<?php echo esc_attr($color_term_slug); ?>"
												data-color-name="<?php echo esc_attr($term->name); ?>"
												data-attribute="attribute_<?php echo sanitize_title($color_attribute_name); ?>"
												title="<?php echo esc_attr($term->name); ?>"
												aria-label="<?php echo esc_attr($term->name); ?>">

												<span class="color-circle size-7 rounded-full border border-transparent hover:border-cynBgItem/25 transition-all duration-300 <?php echo $term->name == 'سفید' && $color_term_slug == 'white' ? '!border-cynBgItem/25' : ''; ?>" style="background-color: <?php echo esc_attr($color_code); ?>"></span>

												<div class="overflow-hidden">
													<span class="color-label block text-sm font-medium text-cynBlack whitespace-nowrap px-3">
														<?php echo esc_html($term->name); ?>
													</span>
												</div>
											</button>
									<?php
										}
									endforeach;
									?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>

				</div>
			<?php endif; ?>

			<hr class="border-cynBgItem/30 h-px w-full my-5">

			<?php if ($product->is_type('variable')) : ?>
				<form class="variations_form cart" method="post" enctype="multipart/form-data" data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo htmlspecialchars(wp_json_encode($product->get_available_variations())); ?>">

					<!-- WooCommerce expects .variations container with selects inside -->
					<div class="variations" style="display:none;" aria-hidden="true">
						<?php
						$attributes = $product->get_variation_attributes();
						foreach ($attributes as $attribute_name => $options) :
							$selected = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) ? wc_clean(stripslashes(urldecode($_REQUEST['attribute_' . sanitize_title($attribute_name)]))) : $product->get_variation_default_attribute($attribute_name);
						?>
							<select class="variation-select" name="attribute_<?php echo sanitize_title($attribute_name); ?>" data-attribute_name="attribute_<?php echo sanitize_title($attribute_name); ?>" data-show_option_none="yes">
								<option value=""><?php echo wc_attribute_label($attribute_name); ?></option>
								<?php
								if (!empty($options)) {
									foreach ($options as $option) {
										echo '<option value="' . esc_attr($option) . '" ' . selected($selected, $option, false) . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $option)) . '</option>';
									}
								}
								?>
							</select>
						<?php endforeach; ?>
					</div>

					<!-- WooCommerce script updates this; we hide it and sync to our #product-price-display / #stock-status-wrapper via JS -->
					<div class="single_variation_wrap" style="display:none !important;">
						<div class="single_variation"></div>
					</div>

					<div id="variation-i18n" data-out-of-stock="ناموجود" data-only-left="فقط" data-items-left="عدد باقیمانده" hidden></div>


					<input type="hidden" name="variation_id" class="variation_id" value="0" />
					<input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>" />

					<div class="flex items-center justify-between gap-3 flex-wrap">
						<div class="flex items-center gap-2">
							<button type="button" id="shareBtn" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynRed hover:bg-cynRed hover:text-cynWhite transition-all duration-300 share-product p-3" aria-label="<?php echo esc_attr(__('اشتراک', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Share-1'); ?></i>
							</button>

							<a href="#comments" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynRed hover:bg-cynRed transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('نظرات', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Messages,-Chat-18'); ?></i>
							</a>

							<!-- <//?php if (is_user_logged_in()) : ?>
								<a href="<//?php echo esc_url($wishlist_toggle_url); ?>" class="wishlist-heart-btn rounded-full border flex items-center justify-center transition-all duration-300 p-3 <//?php echo $wishlist_is_liked ? 'is-liked border-[#cf255d] bg-[#cf255d] text-white hover:bg-[#b91f53] hover:border-[#b91f53]' : 'border-cynBlack/10 text-cynBlack hover:border-cynRed hover:bg-cynRed'; ?>" aria-label="<//?php echo esc_attr(__('علاقه‌مندی', 'taghechian')); ?>" aria-pressed="<//?php echo $wishlist_is_liked ? 'true' : 'false'; ?>">
									<//?php if ($wishlist_is_liked) : ?>
										<svg class="size-5" viewBox="0 0 20 20" aria-hidden="true">
											<path fill="currentColor" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656z" />
										</svg>
									<//?php else : ?>
										<i class="size-6 stroke-[1.5]"><//?php Icon::print('Heart'); ?></i>
									<//?php endif; ?>
								</a>
							<//?php else : ?>
								<button type="button" class="wishlist-heart-btn wishlist-heart-guest rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynRed hover:bg-cynRed transition-all duration-300 p-3" aria-label="<//?php echo esc_attr(__('علاقه‌مندی', 'taghechian')); ?>">
									<i class="size-6 stroke-[1.5]"><//?php Icon::print('Heart'); ?></i>
								</button>
							<//?php endif; ?> -->
						</div>

						<button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button primary-btn flex items-center justify-center gap-2 !py-3 !px-6 text-base font-medium text-cynBlack">
							<span><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
							<i class="size-5"><?php Icon::print('Basket,-Shopping-Cart-6'); ?></i>
						</button>
					</div>
				</form>
			<?php else : ?>
				<form class="cart" method="post" enctype="multipart/form-data">
					<div class="flex items-center justify-between gap-3 flex-wrap">
						<div class="flex items-center gap-2">
							<button type="button" id="shareBtn" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynRed hover:bg-cynRed hover:text-cynWhite transition-all duration-300 share-product p-3" aria-label="<?php echo esc_attr(__('اشتراک', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Share-1'); ?></i>
							</button>

							<a href="#comments" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynRed hover:bg-cynRed hover:text-cynWhite transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('نظرات', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Messages,-Chat-18'); ?></i>
							</a>

							<!-- <//?php if (is_user_logged_in()) : ?>
								<a href="<//?php echo esc_url($wishlist_toggle_url); ?>" class="wishlist-heart-btn rounded-full border flex items-center justify-center transition-all duration-300 p-3 <//?php echo $wishlist_is_liked ? 'is-liked border-[#cf255d] bg-[#cf255d] text-white hover:bg-[#b91f53] hover:border-[#b91f53]' : 'border-cynBlack/10 text-cynBlack hover:border-cynRed hover:bg-cynRed'; ?>" aria-label="<//?php echo esc_attr(__('علاقه‌مندی', 'taghechian')); ?>" aria-pressed="<//?php echo $wishlist_is_liked ? 'true' : 'false'; ?>">
									<//?php if ($wishlist_is_liked) : ?>
										<svg class="size-5" viewBox="0 0 20 20" aria-hidden="true">
											<path fill="currentColor" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656z" />
										</svg>
									<//?php else : ?>
										<i class="size-6 stroke-[1.5]"><//?php Icon::print('Heart'); ?></i>
									<//?php endif; ?>
								</a>
							<//?php else : ?>
								<button type="button" class="wishlist-heart-btn wishlist-heart-guest rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynRed hover:bg-cynRed transition-all duration-300 p-3" aria-label="<//?php echo esc_attr(__('علاقه‌مندی', 'taghechian')); ?>">
									<i class="size-6 stroke-[1.5]"><//?php Icon::print('Heart'); ?></i>
								</button>
							<//?php endif; ?> -->
						</div>

						<button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button primary-btn flex items-center justify-center gap-2 !py-3 !px-6 text-base font-medium text-cynBlack <?php echo !$product->is_in_stock() ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''; ?>" <?php echo !$product->is_in_stock() ? 'disabled' : ''; ?>>
							<span><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
							<i class="size-5"><?php Icon::print('Basket,-Shopping-Cart-6'); ?></i>
						</button>
					</div>
				</form>
			<?php endif; ?>

			<div class="text-cynBlack text-base font-medium mt-5">
				<span>
					<?php
					$comments_count = get_comments_number();
					if ($comments_count == 0) {
						_e('دیدگاهی برای این محصول ثبت نشده است', 'taghechian');
					} else {
						$comments_link = '<a href="#reviews" class="text-cynBlue underline">'
							. $comments_count . ' دیدگاه' .
							'</a>';

						printf(
							__(' %s برای این محصول', 'taghechian'),
							$comments_link
						);
					}
					?>
				</span>
			</div>

			<?php if ($product_excert || $product_excert_voice) : ?>

				<div class="mt-7 flex flex-col gap-3">

					<?php if ($product_excert) : ?>
						<!-- product excert -->
						<div class="accordion-item bg-white overflow-hidden">
							<button class="accordion-button w-full flex items-center justify-between text-right cursor-pointer" data-accordion-target="accordion-excert" aria-expanded="false">
								<span class="font-medium text-xl text-cynBlack flex-1 text-right">
									<?php _e('شناسنامه محصول', 'taghechian'); ?>
								</span>
								<i class="accordion-icon rotate-45 size-6 stroke-2 text-cynBlack transition-transform duration-300 flex-shrink-0">
									<?php Icon::print('Delete,-Disabled'); ?>
								</i>
							</button>
							<div class="accordion-content grid transition-all duration-300 ease-in-out" data-accordion-content="accordion-excert" style="grid-template-rows: 0fr;">
								<div class="overflow-hidden">
									<div class="text-sm font-light leading-8 text-cynBlack/80 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:px-5 [&_h2]:py-2 [&_h2]:bg-[#f0f0f0] [&_h2]:rounded-l-3xl [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:px-5 [&_h3]:py-2 [&_h3]:bg-[#f0f0f0] [&_h3]:rounded-l-3xl [&_h4]:text-xl [&_h4]:font-semibold [&_h4]:px-5 [&_h4]:py-2 [&_h4]:bg-[#f0f0f0] [&_h4]:rounded-l-3xl [&_p]:px-2 [&_p]:py-3">
										<?php echo $product_excert; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($product_excert_voice) : ?>

						<hr class="border-cynBgItem/30 h-px w-full my-3">

						<!-- product excert voice -->
						<div class="accordion-item bg-white overflow-hidden">
							<button class="accordion-button w-full flex items-center justify-between text-right cursor-pointer" data-accordion-target="accordion-excert_voice" aria-expanded="false">
								<span class="font-medium text-xl text-cynBlack flex-1 text-right">
									<?php _e('جزئیات محصول', 'taghechian'); ?>
								</span>
								<i class="accordion-icon rotate-45 size-6 stroke-2 text-cynBlack transition-transform duration-300 flex-shrink-0">
									<?php Icon::print('Delete,-Disabled'); ?>
								</i>
							</button>
							<div class="accordion-content grid transition-all duration-300 ease-in-out" data-accordion-content="accordion-excert_voice" style="grid-template-rows: 0fr;">
								<div class="overflow-hidden">
									<div class="text-sm font-light leading-8 text-cynBlack/80 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:px-5 [&_h2]:py-2 [&_h2]:bg-[#f0f0f0] [&_h2]:rounded-l-3xl [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:px-5 [&_h3]:py-2 [&_h3]:bg-[#f0f0f0] [&_h3]:rounded-l-3xl [&_h4]:text-xl [&_h4]:font-semibold [&_h4]:px-5 [&_h4]:py-2 [&_h4]:bg-[#f0f0f0] [&_h4]:rounded-l-3xl [&_p]:px-2 [&_p]:py-3">
										<?php echo $product_excert_voice; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>

	</section>

	<section class="product-reviews mt-14">

		<?php if (function_exists('comments_template')) {
			comments_template('/woocommerce/single-product-reviews.php');
		}
		?>

	</section>

	<?php if ($related_products_query->have_posts()) : ?>
		<section class="mt-14 flex flex-col gap-3 md:gap-5">

			<div class="max-md:text-center">
				<p class="text-3xl md:text-[40px] text-cynBlack leading-11 font-[Dinar]"><?php _e('شاید بپسندید', 'taghechian'); ?></p>
			</div>

			<div class="relative">
				<swiper-container class="w-full" slides-per-view="1.25" space-between="12" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 3.15 }, "768":  { "slidesPerView": 3.15 }, "1024": { "slidesPerView": 4.25 }, "1280": { "slidesPerView": 5, "centeredSlides": false }}' loop="true" autoplay="true" pagination="false" navigation="true" navigation-next-el="#relatedProductsNext" navigation-prev-el="#relatedProductsPrev">
					<?php while ($related_products_query->have_posts()) : $related_products_query->the_post(); ?>
						<swiper-slide>
							<?php Templates::getCard('product'); ?>
						</swiper-slide>
					<?php endwhile; ?>
				</swiper-container>

				<div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-4 pointer-events-none z-10">
					<button id="relatedProductsPrev" class="bg-cynBlack p-1 cursor-pointer rounded-full pointer-events-auto">
						<i class="text-white size-7 stroke-[1.5]">
							<?php icon::print('Arrow-19') ?>
						</i>
					</button>
					<button id="relatedProductsNext" class="bg-cynBlack p-1 cursor-pointer rounded-full pointer-events-auto">
						<i class="text-white size-7 stroke-[1.5]">
							<?php icon::print('Arrow-27') ?>
						</i>
					</button>
				</div>

			</div>

		</section>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

</main>

<?php get_footer(); ?>