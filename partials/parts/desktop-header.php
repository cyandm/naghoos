<?php

/**
 * Desktop Header
 * @package Naghoos
 */

use Cyan\Theme\Helpers\Icon;
?>

<section class="container relative z-50">

	<div class="flex justify-between items-center max-lg:[&>div]:flex-1 border border-cynBorder rounded-3xl md:rounded-full bg-cynWhite px-1.5 py-2 lg:px-10 lg:py-4.5">

		<div class="mobile-menu flex lg:hidden gap-2">
			<div class="bg-cynWhite p-2.5 rounded-full border border-cynRed" modal-opener data-modal-name="menu-modal">
				<span class="size-6 [&_svg]:scale-x-[-1] [&_svg]:transform stroke-[1.5]">
					<?php Icon::print('menu-burger-square-6') ?>
				</span>
			</div>

			<a href="<?= get_site_url() . '/?s=&search-type=all' ?>" class="lg:hidden block bg-cynWhite p-2.5 rounded-full border border-cynRed">
				<span class="size-6 stroke-[1.5]">
					<?php Icon::print('Search,-Loupe'); ?>
				</span>
			</a>
		</div>

		<div class="flex gap-3 items-center justify-center">

			<div class="logo [&_img]:size-12">
				<?php the_custom_logo(); ?>
			</div>

			<div class="desktop-menu hidden lg:flex">
				<?php wp_nav_menu([
					'menu_id' => 'main-menu',
					'menu_class' => 'gap-3 text-sm font-medium flex items-center text-cynBlack [&>li>a]:px-2 [&>li>a]:py-1.5 [&>li>a]:rounded-xl [&>li>a]:transition-all [&>li>a]:duration-300 [&>li>a:hover]:text-cynWhite [&>li>a:hover]:bg-cynRed [&>li>a[aria-current=page]]:text-cynWhite [&>li>a[aria-current=page]]:bg-cynRed [&>li>ul>li>a:hover]:text-cynRed [&>li>ul>li>a]:transition-all [&>li>ul>li>a]:duration-300 [&_li]:duration-300 [&_li]:transition-all [&_li_a_svg]:transition-all [&_li_a_svg]:duration-200 [&_li:hover_svg]:rotate-180',
					'depth' => '3',
					'theme_location' => 'header-menu',
					'container' => 'ul'
				]); ?>
			</div>
		</div>

		<div class="flex justify-end gap-3">

			<form id="search-form" class="hidden lg:flex justify-between items-center relative">
				<i class="absolute inset-0 top-1/2 -translate-y-1/2 flex items-center ps-3.5 pointer-events-none text-[#8C847F] size-9">
					<?php Icon::print('Search,-Loupe'); ?>
				</i>

				<input type="text"
					id="email-address-icon"
					name="s"
					value="<?php the_search_query() ?>"
					class="text-cynBlack text-base font-medium rounded-full border border-cynBorder/10 focus:outline-none focus:ring-cynRed focus:border-cynRed block w-full ps-10 p-3 pt-3.5 transition-all duration-300 md:min-w-64"
					placeholder="<?php _e('جستجو کنید', 'naghoos'); ?>">
			</form>

			<div class="relative flex justify-center group" id="login-btn">
				<a href="<?= !is_user_logged_in() ? get_site_url() . '/panel' : '#' ?>" class="flex items-center justify-between gap-1 text-cynWhite bg-cynBlack min-w-34 py-3 ps-4 <?php echo is_user_logged_in() ? 'md:!ps-6 !pe-3' : 'pe-5'; ?> rounded-full text-sm font-medium transition-all duration-300 group-hover:bg-cynRed group-hover:text-cynWhite">

					<?php if (!is_user_logged_in()): ?>
						<i class="size-6 text-cynRed stroke-2 group-hover:text-cynWhite transition-all duration-300">
							<?php Icon::print('User,-Profile'); ?>
						</i>
					<?php endif; ?>

					<span class="inline-block max-w-[110px] truncate whitespace-nowrap" title="<?= is_user_logged_in() ? esc_html(wp_get_current_user()->display_name) : '' ?>"><?= is_user_logged_in() ? esc_html(wp_get_current_user()->display_name) : __('ورود / ثبت نام', 'naghoos'); ?></span>

					<?php if (is_user_logged_in()): ?>
						<i class=" size-6 text-cynWhite group-hover:rotate-180 transition-all duration-300 stroke-2">
							<?php Icon::print('Arrow-28'); ?>
						</i>
					<?php endif; ?>

				</a>

				<?php if (is_user_logged_in()) : ?>
					<div class="absolute top-13.5 md:top-14 left-0 right-0 w-full p-4 bg-cynWhite text-cynBlack rounded-3xl shadow-item border border-cynBlack pointer-events-none group-hover:pointer-events-auto invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-300" id="navlogged">

						<?php wp_nav_menu([
							'menu_id' => 'login-menu',
							'menu_class' => 'flex flex-col gap-2 text-xs font-semibold text-cynBlack [&_li]:first:border-t-0 [&_li]:border-t [&_li]:border-[#F7EAEA] [&_li]:py-2 [&>li>a:hover]:text-cynOrange [&_li_a]:duration-200 [&_li_a]:transition-all',
							'depth' => '3',
							'theme_location' => 'login-menu',
							'container' => 'ul'
						]); ?>

					</div>
				<?php endif; ?>

			</div>

			<?php if (class_exists('WooCommerce')): ?>

				<a href="<?= (class_exists('WooCommerce')) ? wc_get_cart_url() : '#' ?>" class="hidden lg:flex items-center justify-center bg-cynWhite relative group border border-cynBlack hover:border-cynRed transition-all duration-300 p-2.5 rounded-full w-[50px]">

					<?php $cart_count = (isset(WC()->cart)) ? WC()->cart->get_cart_contents_count() : 0; ?>

					<span class="size-6 stroke-[1.5] text-cynRed">
						<?php Icon::print('Shopping-Cart'); ?>
					</span>

					<div class="absolute -top-1 -right-1 size-5 bg-white border border-[#C6C6C6] text-cynBlack text-sm font-medium rounded-full flex items-center justify-center">
						<span><?= $cart_count ?></span>
					</div>
				</a>

			<?php endif; ?>

		</div>

	</div>

</section>