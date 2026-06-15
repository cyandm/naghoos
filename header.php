<?php

/**
 * Header for wordpress theme
 * its must include only head and body tags
 * header templates located in /partials/header/
 * @package CyanTheme
 */

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

$render_template = $args['render_template'] ?? true;
?>
<!DOCTYPE html>
<?php
// Determine text direction based on current language
$current_lang = 'fa'; // Default to Persian
if (function_exists('pll_current_language')) {
	$current_lang = pll_current_language();
} elseif (function_exists('icl_get_languages')) {
	// WPML fallback
	$current_lang = ICL_LANGUAGE_CODE;
} else {
	// WordPress locale fallback
	$locale = get_locale();
	if (strpos($locale, 'en') === 0) {
		$current_lang = 'en';
	}
}
$text_direction = ($current_lang === 'en') ? 'ltr' : 'rtl';

$noise_url = get_template_directory_uri() . '/assets/image/noise.svg';
?>

<html <?php language_attributes(); ?> dir="<?php echo esc_attr($text_direction); ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/assets/css/dist/plyr.css' ?>" />
</head>

<body class=" overflow-x-hidden bg-cynBG">
	<?php wp_body_open(); ?>

	<?php if ($render_template) : ?>

		<div class="icon hidden" id="chevron-down">
			<?php Icon::print('Arrow-28') ?>
		</div>

		<header class="py-7 relative z-10 before:absolute before:content-[''] before:top-0 before:w-full before:h-1/2 before:bg-[url('<?php echo $noise_url ?>">
			<?php Templates::getPart('desktop-header'); ?>
			<?php Templates::getPart('mobile-header'); ?>
		</header>

	<?php endif; ?>