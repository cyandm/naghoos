<?php

use Cyan\Theme\Helpers\Icon;

if (!class_exists('WooCommerce')) {
	return;
}

if (is_cart() || is_checkout()) {
	return;
}

$cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>

<a href="<?php echo esc_url(wc_get_cart_url()); ?>"
	class="mobile-cart-fab fixed bottom-4 left-4 z-30 lg:hidden flex items-center justify-center size-12 rounded-full bg-cynRed border border-cynBlack"
	aria-label="<?php esc_attr_e('سبد خرید', 'naghoos'); ?>">
	<span class="size-8 stroke-[1.5] text-white">
		<?php Icon::print('Shopping-Cart'); ?>
	</span>
	<span class="mobile-cart-count absolute -top-1 -right-1 size-5 bg-white border border-[#C6C6C6] text-cynBlack text-sm font-medium rounded-full flex items-center justify-center">
		<?php echo (int) $cart_count; ?>
	</span>
</a>