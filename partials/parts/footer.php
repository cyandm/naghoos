<?php

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

$address_text = get_option('address_text');
$address_link = get_option('address_link');
$phone_number = get_option('phone_number');
$phone_number_support = get_option('phone_number_support');
$telegram_link = get_option('telegram_link');
$telegram_text = get_option('telegram_text');
$instagram_link = get_option('instagram_link');
$instagram_text = get_option('instagram_text');
$whatsapp_number = get_option('whatsapp_number');
$email_address = get_option('email_address');
$enamad_code = get_option('cyn_enamad_code');
$samandehi_code = get_option('cyn_samandehi_code');
$developer_code = get_option('cyn_developer_code');

$location = [];
for ($i = 1; $i <= 6; $i++) {
	$img  = get_option('location_image_' . $i);
	$link = get_option('location_link_' . $i);

	if (!empty($img) && !empty($link)) {
		$location[] = [
			'img'  => $img,
			'link' => $link,
		];
	}
}
?>

<section class="bg-cynRed rounded-4xl py-10 px-5">

	<div class="flex gap-8 max-lg:flex-wrap overflow-hidden items-start">

		<div class="lg:flex-1 max-lg:w-[calc(50%-2rem)] max-sm:w-full">

			<?php wp_nav_menu([
				'menu_id' => '',
				'menu_class' => "text-cynWhite text-sm font-medium flex flex-col gap-4 [&>li]:w-fit [&>li]:flex [&>li]:items-center [&>li]:before:content-['●'] [&>li]:before:text-white/30 [&>li]:before:text-sm [&>li]:before:leading-none [&>li]:before:-me-3 [&_li]:whitespace-nowrap [&>li]:hover:before:text-cynBlack [&>li]:before:transition-all [&>li]:before:duration-300 [&>li>a]:ps-4",
				'depth' => '',
				'theme_location' => 'footer-menu-col-1',
				'container' => 'ul'
			]); ?>

		</div>

		<div class="lg:flex-1 max-lg:w-[calc(50%-2rem)] max-sm:w-full">

			<?php wp_nav_menu([
				'menu_id' => '',
				'menu_class' => "text-cynWhite text-sm font-medium flex flex-col gap-4 [&>li]:w-fit [&>li]:flex [&>li]:items-center [&>li]:before:content-['●'] [&>li]:before:text-white/30 [&>li]:before:text-sm [&>li]:before:leading-none [&>li]:before:-me-3 [&_li]:whitespace-nowrap [&>li]:hover:before:text-cynBlack [&>li]:before:transition-all [&>li]:before:duration-300 [&>li>a]:ps-4",
				'depth' => '',
				'theme_location' => 'footer-menu-col-2',
				'container' => 'ul'
			]); ?>

		</div>

		<div class="lg:flex-1 max-lg:w-[calc(50%-2rem)] max-sm:w-full">

			<?php wp_nav_menu([
				'menu_id' => '',
				'menu_class' => "text-cynWhite text-sm font-medium flex flex-col gap-4 [&>li]:w-fit [&>li]:flex [&>li]:items-center [&>li]:before:content-['●'] [&>li]:before:text-white/30 [&>li]:before:text-sm [&>li]:before:leading-none [&>li]:before:-me-3 [&_li]:whitespace-nowrap [&>li]:hover:before:text-cynBlack [&>li]:before:transition-all [&>li]:before:duration-300 [&>li>a]:ps-4",
				'depth' => '',
				'theme_location' => 'footer-menu-col-3',
				'container' => 'ul'
			]); ?>

		</div>

		<?php if ($address_text || $address_link) : ?>

			<div class="lg:flex-2 max-lg:w-[calc(50%-2rem)] max-sm:w-full flex flex-col gap-1 group [&>p]:before:content-['●'] [&>p]:before:text-white/30 [&>p]:before:text-sm [&>p]:before:leading-none [&>p]:before:me-0.5 hover:[&>p]:before:text-cynBlack [&>p]:before:transition-all [&>p]:before:duration-300 text-cynWhite">

				<p class="text-sm font-medium">
					<?php _e('آدرس:', 'naghoos') ?>
				</p>

				<?php if ($address_link): ?>
					<a href="<?php echo $address_link ?>" rel="noopener noreferrer" target="_blank" class="text-xs font-medium leading-6">
						<?php echo $address_text ?>
					</a>
				<?php else: ?>
					<sapn class="text-xs font-medium leading-6">
						<?php echo $address_text ?>
					</sapn>
				<?php endif ?>

				<?php if ($location): ?>

					<div class="flex gap-3 items-center mt-2">

						<?php foreach ($location as $loc): ?>

							<a href="<?php echo $loc['link'] ?>" rel="noopener noreferrer" target="_blank" class="size-8 flex items-center justify-center text-cynBlack rounded-[10px] overflow-hidden">
								<img src="<?php echo esc_url($loc['img']) ?>" alt="<?php echo basename($loc['img'], '.svg'); ?>" class="size-full object-contain">
							</a>

						<?php endforeach; ?>

					</div>

				<?php endif; ?>

			</div>

		<?php endif; ?>

		<?php if ($phone_number || $phone_number_support) : ?>

			<div class="lg:flex-1 max-lg:w-[calc(50%-2rem)] max-sm:w-full flex flex-col gap-1 group [&>p]:before:content-['●'] [&>p]:before:text-white/30 [&>p]:before:text-sm [&>p]:before:leading-none [&>p]:before:me-0.5 hover:[&>p]:before:text-cynBlack [&>p]:before:transition-all [&>p]:before:duration-300 text-cynWhite whitespace-nowrap">

				<p class="text-sm font-medium ">
					<?php _e('شماره تماس:', 'naghoos') ?>
				</p>

				<?php if ($phone_number): ?>
					<a href="tel:<?php echo $phone_number ?>" rel="noopener noreferrer" class="text-xs font-medium leading-6">
						<?php echo $phone_number ?>
					</a>
				<?php endif; ?>
				<?php if ($phone_number_support): ?>
					<a href="tel:<?php echo $phone_number_support ?>" rel="noopener noreferrer" class="text-xs font-medium leading-6">
						<?php echo $phone_number_support ?>
					</a>
				<?php endif ?>

			</div>

		<?php endif; ?>

		<?php
		$socials = [];

		if (!empty($telegram_link)) {
			$socials[] = [
				'link' => $telegram_link,
				'icon' => 'Telegram',
			];
		}

		if (!empty($instagram_link)) {
			$socials[] = [
				'link' => $instagram_link,
				'icon' => 'Instagram',
			];
		}

		if (!empty($whatsapp_number)) {
			$socials[] = [
				'link' => 'https://wa.me/' . preg_replace('/\D/', '', $whatsapp_number),
				'icon' => 'Whatsup',
			];
		}

		if (!empty($email_address)) {
			$socials[] = [
				'link' => 'mailto:' . $email_address,
				'icon' => 'Emails,-Letter,-Mail-1',
			];
		}
		?>

		<?php if (!empty($socials)) : ?>

			<div class="lg:flex-1 max-lg:w-[calc(50%-2rem)] max-sm:w-full flex flex-col gap-3 group [&>p]:before:content-['●'] [&>p]:before:text-white/30 [&>p]:before:text-sm [&>p]:before:leading-none [&>p]:before:me-0.5 hover:[&>p]:before:text-cynBlack [&>p]:before:transition-all [&>p]:before:duration-300 text-cynWhite">

				<p class="text-sm font-medium">
					<?php _e('راه های ارتباطی:', 'naghoos') ?>
				</p>

				<div class="flex gap-3 items-center">

					<?php foreach ($socials as $social) : ?>

						<a href="<?php echo esc_url($social['link']); ?>" rel="noopener noreferrer" target="_blank" class="size-8 flex items-center justify-center text-cynBlack rounded-[10px] overflow-hidden">
							<i class="size-6 stroke-[1.5] text-white">
								<?php Icon::print($social['icon']); ?>
							</i>
						</a>

					<?php endforeach; ?>

				</div>

			</div>

		<?php endif; ?>

		<?php if ($enamad_code || $samandehi_code) : ?>
			<div class="lg:flex-2 max-lg:w-full text-cynWhite h-[stretch] flex items-center justify-center lg:justify-end">

				<div class="flex flex-row lg:flex-col gap-5 max-lg:items-center max-lg:justify-center text-center">
					<?php if ($enamad_code) : ?>
						<div class="flex items-center justify-center size-[70px]">
							<?php echo $enamad_code; ?>
						</div>
					<?php endif; ?>

					<?php if ($samandehi_code) : ?>
						<div class="flex items-center justify-center size-[70px]">
							<?php echo $samandehi_code; ?>
						</div>
					<?php endif; ?>

				</div>

			</div>
		<?php endif; ?>

	</div>

</section>

<section class="container text-cynBlack text-sm font-medium text-center mt-3">
	<p class="whitespace-nowrap [&>a]:text-cynBlue"><?php echo $developer_code; ?></p>
</section>