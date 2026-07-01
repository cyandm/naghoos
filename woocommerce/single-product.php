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

$reviews_enabled = wc_reviews_enabled();

$wishlist_is_liked = ThemeWooCommerce::isProductInCurrentUserWishlist($product->get_id());
$wishlist_toggle_url = ThemeWooCommerce::getWishlistToggleUrl($product->get_id());

// Get featured image as fallback for video covers
$featured_image_id = get_post_thumbnail_id(get_the_ID());

$product_excert = get_field('product_excert', $product->get_id());
$product_excert_voice_id  = get_field('product_excert_voice', $product->get_id());
$product_excert_voice_url = $product_excert_voice_id ? wp_get_attachment_url($product_excert_voice_id) : '';
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
								<div class="w-full h-full flex items-center justify-center bg-transparent overflow-hidden [&_img]:w-full [&_img]:h-full [&_img]:object-cover cursor-zoom-in m-auto"
									data-fancybox-delegate="product-gallery"
									data-fancybox-index="<?php echo (int) $index; ?>">
									<?php echo wp_get_attachment_image($img_id, 'full', false, ['class' => 'w-full h-full object-cover p-20']); ?>
								</div>
							</swiper-slide>
						<?php else : ?>
							<swiper-slide class="!h-full">
								<div class="w-full h-full flex items-center justify-center bg-transparent overflow-hidden [&_img]:w-full [&_img]:h-full [&_img]:object-cover">
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
				<h1 class="text-3xl leading-14 text-cynBlack"><?php echo get_the_title(); ?></h1>

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
						echo '<span class="text-[#dd4a4a]">' . __('ناموجود', 'taghechian') . '</span>';
					} else {
						echo $product->get_price_html();
					}
					?>
				</p>
			</div>

			<?php if (!empty($product_size_guide)) : ?>
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

			<hr class="border-cynBgItem/30 h-px w-full my-5">

			<form class="cart" method="post" enctype="multipart/form-data">
				<div class="flex items-center justify-between gap-3 flex-wrap">
					<div class="flex items-center gap-2">
						<button type="button" id="shareBtn" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynRed hover:bg-cynRed hover:text-cynWhite transition-all duration-300 share-product p-3" aria-label="<?php echo esc_attr(__('اشتراک', 'taghechian')); ?>">
							<i class="size-6 stroke-[1.5]"><?php Icon::print('Share-1'); ?></i>
						</button>

						<?php if ($reviews_enabled) : ?>
							<a href="#comments" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynRed hover:bg-cynRed hover:text-cynWhite transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('نظرات', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Messages,-Chat-18'); ?></i>
							</a>
						<?php endif; ?>

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

			<?php if ($reviews_enabled) : ?>
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
			<?php endif; ?>

			<?php if ($product_excert || $product_excert_voice_url) : ?>

				<div class="mt-7 flex flex-col gap-3">

					<?php if ($product_excert) : ?>
						<!-- product excert -->
						<div class="accordion-item bg-white overflow-hidden">
							<button class="accordion-button w-full flex items-center justify-between text-right cursor-pointer" data-accordion-target="accordion-excert" aria-expanded="false">
								<span class="font-medium text-xl text-cynBlack flex-1 text-right">
									<?php _e('خلاصه کتاب', 'naghoos'); ?>
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

					<?php if ($product_excert_voice_url) : ?>

						<hr class="border-cynBgItem/30 h-px w-full my-3">

						<!-- product excert voice -->
						<div class="accordion-item bg-white overflow-hidden">
							<button class="accordion-button w-full flex items-center justify-between text-right cursor-pointer" data-accordion-target="accordion-excert_voice" aria-expanded="false">
								<span class="font-medium text-xl text-cynBlack flex-1 text-right">
									<?php _e('خلاصه صوتی', 'naghoos'); ?>
								</span>
								<i class="accordion-icon rotate-45 size-6 stroke-2 text-cynBlack transition-transform duration-300 flex-shrink-0">
									<?php Icon::print('Delete,-Disabled'); ?>
								</i>
							</button>
							<div class="accordion-content grid transition-all duration-300 ease-in-out" data-accordion-content="accordion-excert_voice" style="grid-template-rows: 0fr;">
								<div class="overflow-hidden">
									<div
										class="waveform-container flex items-center gap-3 bg-[#f3f3f3] rounded-2xl px-4 py-3"
										data-url="<?php echo esc_url($product_excert_voice_url); ?>"
										dir="ltr">

										<button type="button" class="play-btn flex-shrink-0 size-10 rounded-full bg-cynRed text-white flex items-center justify-center transition-opacity hover:opacity-90 [&.playing_.play-icon]:hidden [&.playing_.pause-icon]:block" aria-label="<?php esc_attr_e('پخش', 'naghoos'); ?>">
											<i class="play-icon size-6">
												<img src="<?php echo esc_url(THEME_IMAGES_URI . '/play.svg'); ?>" alt="<?php esc_attr_e('پخش', 'naghoos'); ?>" class="w-6 h-6 relative z-10" />
											</i>

											<i class="pause-icon hidden size-6"><?php Icon::print('Pause'); ?></i>
										</button>

										<div class="waveform-id flex-1 min-w-0"></div>

										<span class="duration text-sm font-medium text-cynBlack/70 flex-shrink-0 tabular-nums w-fit text-right">۰۰:۰۰</span>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>

	</section>

	<?php if ($related_products_query->have_posts()) : ?>
		<section class="mt-16 flex flex-col gap-3 md:gap-5">

			<div class="max-md:text-center">
				<p class="text-3xl font-bold text-cynBlack leading-11"><?php _e('شاید بپسندید', 'taghechian'); ?></p>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-3 w-full [&_.product-card]:h-full">
				<?php while ($related_products_query->have_posts()) : $related_products_query->the_post(); ?>
					<?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
				<?php endwhile; ?>
			</div>

		</section>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<?php if ($reviews_enabled) : ?>
		<section class="product-reviews mt-14">
			<?php if (function_exists('comments_template')) {
				comments_template('/woocommerce/single-product-reviews.php');
			} ?>
		</section>
	<?php endif; ?>

</main>

<?php get_footer(); ?>