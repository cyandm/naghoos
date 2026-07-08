<?php

/**
 * Cart Page
 *
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Classes\WooCommerce as ThemeWooCommerce;

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<?php
$total_saving = ThemeWooCommerce::cyn_get_cart_special_price_saving();
?>

<div class="woocommerce-cart-form-wrapper" dir="rtl">
	<h1 class="text-2xl md:text-3xl font-medium text-cynBlack mb-5 md:mb-8"><?php the_title(); ?></h1>

	<div class="cart-page-grid items-start">
		<div class="cart-table-col min-w-0">
			<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
				<?php do_action('woocommerce_before_cart_table'); ?>

				<div class="hidden md:grid grid-cols-12 gap-4 px-6 lg:px-10 py-4 bg-cynBgItem/30 rounded-2xl text-sm md:text-base font-medium text-cynBlack">
					<div class="col-span-5 text-start"><?php esc_html_e('نام محصول', 'naghoos'); ?></div>
					<div class="col-span-2 text-start"><?php esc_html_e('قیمت', 'naghoos'); ?></div>
					<div class="col-span-3 text-start"><?php esc_html_e('تعداد', 'naghoos'); ?></div>
					<div class="col-span-2 text-end"><?php esc_html_e('قیمت نهایی', 'naghoos'); ?></div>
				</div>

				<div class="cart-items-container flex flex-col max-md:gap-3">
					<?php
					foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
						$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
						$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

						if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
							$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
							$writers           = wc_get_product_terms($product_id, 'pa_writer', ['fields' => 'names']);
							$writer_name       = ! empty($writers) ? implode('، ', $writers) : '';
					?>
							<div class="cart-item hidden md:grid grid-cols-12 gap-4 px-4 lg:px-6 py-5 border-b border-cynBlack/10 items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
								<div class="col-span-5 flex items-center gap-3 min-w-0">
									<div class="shrink-0 size-14 lg:size-16 rounded-lg overflow-hidden shadow-sm bg-white">
										<?php
										$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
										if (!$product_permalink) {
											echo $thumbnail;
										} else {
											printf('<a href="%s" class="block size-full [&_img]:size-full [&_img]:object-cover">%s</a>', esc_url($product_permalink), $thumbnail);
										}
										?>
									</div>
									<div class="min-w-0 text-start">
										<?php
										$name_html = wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
										if (!$product_permalink) {
											echo '<p class="text-sm lg:text-base font-medium text-cynBlack leading-6 truncate">' . $name_html . '</p>';
										} else {
											echo '<a href="' . esc_url($product_permalink) . '" class="text-sm lg:text-base font-medium text-cynBlack leading-6 truncate hover:text-cynRed transition-colors block">' . $name_html . '</a>';
										}
										if ($writer_name !== '') :
										?>
											<p class="text-xs lg:text-sm text-cynBlack/50 mt-0.5 truncate"><?php echo esc_html($writer_name); ?></p>
										<?php
										endif;
										echo wc_get_formatted_cart_item_data($cart_item);
										?>
									</div>
								</div>

								<div class="col-span-2 text-start">
									<span class="text-sm font-medium text-cynBlack">
										<?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
									</span>
								</div>

								<div class="col-span-3 text-start">
									<div class="bg-cynBgItem/30 rounded-lg flex justify-center items-center gap-6 px-6 py-2">
										<div class="flex items-center overflow-hidden">
											<button type="button" class="quantity-btn quantity-plus p-1 md:p-2 bg-white border border-cynBlack/10 rounded-md hover:bg-cynYellow text-cynBlack font-bold transition-colors size-6 md:size-8 flex justify-center items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
												<?php Icon::Print('plus'); ?>
											</button>

											<?php
											echo woocommerce_quantity_input(
												array(
													'input_name'   => "cart[{$cart_item_key}][qty]",
													'input_value'  => $cart_item['quantity'],
													'max_value'    => $_product->get_max_purchase_quantity(),
													'min_value'    => '1',
													'product_name' => $_product->get_name(),
													'classes'      => array('input-text', 'product-quantity', 'text', '!border-0', '!border-none', '!outline-none', 'focus:outline-none', 'text-center', 'bg-cynBgItem/30', 'w-6', 'h-6', 'md:!w-8', 'md:!h-8', 'md:!min-h-8', 'focus:outline-none', 'focus:ring-0', 'text-xs', 'font-normal'),
												),
												$_product,
												false
											);
											?>

											<button type="button" class="quantity-btn quantity-minus p-1 md:p-2 bg-white border border-cynBlack/10 rounded-md hover:bg-cynYellow text-cynBlack font-bold transition-colors size-6 md:size-8 flex justify-center items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
												<?php Icon::Print('minus'); ?>
											</button>
										</div>

										<a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
											class="text-[#C315A5] hover:text-pink-500 transition-colors"
											aria-label="<?php echo esc_attr(sprintf(__('حذف %s از سبد خرید', 'woocommerce'), $_product->get_name())); ?>"
											data-product_id="<?php echo esc_attr($_product->get_id()); ?>"
											data-product_sku="<?php echo esc_attr($_product->get_sku()); ?>">
											<div class="size-5 stroke-[1.5]">
												<?php Icon::print('trash-delete-bin-2-1'); ?>
											</div>
										</a>
									</div>
								</div>

								<div class="col-span-2 text-end">
									<span class="text-sm lg:text-base font-medium text-cynBlack item-subtotal">
										<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
									</span>
								</div>
							</div>

							<div class="cart-item md:hidden border border-cynBlack/10 rounded-2xl overflow-hidden text-sm" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
								<div class="flex items-center gap-3 p-3 border-b border-cynBlack/10">
									<div class="size-14 rounded-lg overflow-hidden shrink-0 shadow-sm">
										<?php
										if (!$product_permalink) {
											echo $thumbnail;
										} else {
											printf('<a href="%s" class="block size-full [&_img]:size-full [&_img]:object-cover">%s</a>', esc_url($product_permalink), $thumbnail);
										}
										?>
									</div>
									<div class="min-w-0 flex-1 text-start">
										<?php if (!$product_permalink) : ?>
											<p class="font-medium text-cynBlack"><?php echo wp_kses_post($_product->get_name()); ?></p>
										<?php else : ?>
											<a href="<?php echo esc_url($product_permalink); ?>" class="font-medium text-cynBlack block truncate"><?php echo wp_kses_post($_product->get_name()); ?></a>
										<?php endif; ?>
										<?php if ($writer_name !== '') : ?>
											<p class="text-xs text-cynBlack/50 mt-0.5"><?php echo esc_html($writer_name); ?></p>
										<?php endif; ?>
									</div>
								</div>

								<div class="flex items-center justify-between px-4 py-3 border-b border-cynBlack/10">
									<span class="text-cynBlack/60"><?php esc_html_e('قیمت', 'naghoos'); ?></span>
									<span class="font-medium text-cynBlack"><?php echo WC()->cart->get_product_price($_product); ?></span>
								</div>

								<div class="flex items-center justify-between px-4 py-3 border-b border-cynBlack/10">
									<span class="text-cynBlack/60"><?php esc_html_e('تعداد', 'naghoos'); ?></span>
									<div class="bg-cynBgItem/30 rounded-lg flex justify-center items-center gap-6 px-6 py-2">
										<div class="flex items-center overflow-hidden">
											<button type="button" class="quantity-btn quantity-plus p-1 md:p-2 bg-white border border-cynBlack/10 rounded-md hover:bg-cynYellow text-cynBlack font-bold transition-colors size-6 md:size-8 flex justify-center items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
												<?php Icon::Print('plus'); ?>
											</button>

											<?php
											echo woocommerce_quantity_input(
												array(
													'input_name'   => "cart[{$cart_item_key}][qty]",
													'input_value'  => $cart_item['quantity'],
													'max_value'    => $_product->get_max_purchase_quantity(),
													'min_value'    => '1',
													'product_name' => $_product->get_name(),
													'classes'      => array('input-text', 'product-quantity', 'text', '!border-0', '!border-none', '!outline-none', 'focus:outline-none', 'text-center', 'bg-cynBgItem/30', 'w-6', 'h-6', 'md:!w-8', 'md:!h-8', 'md:!min-h-8', 'focus:outline-none', 'focus:ring-0', 'text-xs', 'font-normal'),
												),
												$_product,
												false
											);
											?>

											<button type="button" class="quantity-btn quantity-minus p-1 md:p-2 bg-white border border-cynBlack/10 rounded-md hover:bg-cynYellow text-cynBlack font-bold transition-colors size-6 md:size-8 flex justify-center items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
												<?php Icon::Print('minus'); ?>
											</button>
										</div>

										<a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
											class="text-[#C315A5] hover:text-pink-500 transition-colors"
											aria-label="<?php echo esc_attr(sprintf(__('حذف %s از سبد خرید', 'woocommerce'), $_product->get_name())); ?>"
											data-product_id="<?php echo esc_attr($_product->get_id()); ?>"
											data-product_sku="<?php echo esc_attr($_product->get_sku()); ?>">
											<div class="size-5 stroke-[1.5]">
												<?php Icon::print('trash-delete-bin-2-1'); ?>
											</div>
										</a>
									</div>
								</div>

								<div class="flex items-center justify-between px-4 py-3">
									<span class="text-cynBlack/60"><?php esc_html_e('قیمت نهایی', 'naghoos'); ?></span>
									<span class="font-medium text-cynBlack item-subtotal"><?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?></span>
								</div>
							</div>
					<?php
						}
					}
					?>
				</div>

				<?php do_action('woocommerce_cart_contents'); ?>
				<?php do_action('woocommerce_after_cart_contents'); ?>
				<?php do_action('woocommerce_after_cart_table'); ?>
			</form>
		</div>

		<aside class="cart-totals-col">
			<div class="cart-collaterals bg-cynWhite rounded-3xl border border-cynBlack/30 p-5 md:p-6 lg:sticky lg:top-6">
				<?php do_action('woocommerce_before_cart_collaterals'); ?>

				<div class="cart-totals-wrapper">
					<p class="text-xl md:text-2xl text-cynBlack text-center mb-5 md:mb-6"><?php esc_html_e('جمع کل سبد خرید', 'naghoos'); ?></p>

					<div class="flex justify-between items-center py-3">
						<span class="text-cynBlack/80 font-medium text-sm md:text-base"><?php esc_html_e('مجموع سبد خرید', 'naghoos'); ?></span>
						<span class="text-sm md:text-base font-medium text-cynBlack cart-subtotal-amount"><?php wc_cart_totals_subtotal_html(); ?></span>
					</div>

					<?php if ($total_saving > 0) : ?>
						<div class="h-px w-full bg-cynBlack/10"></div>
						<div class="flex justify-between items-center py-3">
							<span class="text-cynBlack/80 font-medium text-sm md:text-base"><?php esc_html_e('سود شما از این خرید', 'naghoos'); ?></span>
							<span class="text-cynRed text-sm md:text-base font-medium cart-saving-amount"><?php echo wc_price($total_saving); ?></span>
						</div>
					<?php endif; ?>

					<div class="h-px w-full bg-cynBlack/10"></div>

					<div class="flex justify-between items-center py-4 cart-totals-payable">
						<span class="font-medium text-sm md:text-base text-cynBlue"><?php esc_html_e('قابل پرداخت', 'naghoos'); ?></span>
						<span class="text-base md:text-lg font-medium text-cynBlue cart-total-amount"><?php wc_cart_totals_order_total_html(); ?></span>
					</div>
				</div>

				<?php do_action('woocommerce_after_cart_totals'); ?>

				<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="primary-btn !rounded-full block w-full text-center mt-4 md:mt-5">
					<?php esc_html_e('تائید و تکمیل خرید', 'naghoos'); ?>
				</a>
			</div>
		</aside>
	</div>
</div>

<?php
$recommended_products_query = ThemeWooCommerce::getCartRecommendedProductsQuery();
?>

<?php if ($recommended_products_query->have_posts()): ?>

	<?php
	$recommended_product_ids    = array_map('intval', wp_list_pluck($recommended_products_query->posts, 'ID'));
	?>

	<section class="flex flex-col gap-3 md:gap-5 my-15">

		<div class="flex max-md:justify-center flex-col gap-2">
			<p class="text-3xl font-bold text-cynBlack">
				<?php echo esc_html(__('محصولات پیشنهادی', 'naghoos')); ?>
			</p>
			<span class="text-cynBlack text-base font-medium">
				<?php echo esc_html(__('بر اساس انتخاب‌های شما', 'naghoos')); ?>
			</span>
		</div>

		<swiper-container class="w-full" space-between="8" slides-per-view="1.15" autoplay="true" delay="5000" loop="true" navigation="false" breakpoints='{ "240": { "slidesPerView": 1.15 }, "768": { "slidesPerView": 2.5 }, "1024": { "slidesPerView": 3.5 }, "1280": { "slidesPerView": 4.15 }, "1536": { "slidesPerView": 5.15 } }'>

			<?php while ($recommended_products_query->have_posts()) : $recommended_products_query->the_post(); ?>
				<swiper-slide class="py-1.5 px-1">
					<?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
				</swiper-slide>
			<?php endwhile; ?>

		</swiper-container>
	</section>

	<?php wp_reset_postdata(); ?>
<?php endif; ?>