<?php
/*
Template Name: 404
Description: A template for displaying a 404 error page.
*/
?>

<?php get_header(); ?>

<main class="container mx-auto px-4">
	<section class="mt-16 text-center">

		<div class="flex justify-center">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/404.svg" class="max-w-lg">
		</div>


		<p class="text-3xl font-bold text-cynBlack">
			<?php _e('متاسفیم!', 'naghoos'); ?>
		</p>

		<p class="text-xl font-semibold text-cynBlack/50 mt-1">
			<?php _e('صفحه مورد نظر یافت نشد', 'naghoos'); ?>
		</p>

		<div class="mt-2 flex justify-center">
			<a href="/" class="primary-btn w-full max-w-xs sm:w-auto">
				<?php _e('بازگشت به صفحه اصلی', 'naghoos'); ?>
			</a>
		</div>

	</section>
</main>

<?php get_footer(); ?>