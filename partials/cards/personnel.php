<?php
$args         = get_query_var('args', []);
$personnel_id = ! empty($args['person']) ? (int) $args['person'] : get_the_ID();

if (!$personnel_id) {
	return;
}
?>

<div class="personnel-card relative group bg-[#D7D7D7] cursor-pointer">
	<?php echo get_the_post_thumbnail($personnel_id, 'full', ['class' => 'w-full h-full object-cover']); ?>

	<div class="personnel-card__overlay absolute top-0 h-full w-full p-4 flex justify-center items-end opacity-0 bg-transparent group-hover:opacity-100 group-hover:bg-cynBlack/50 transition-all duration-300">
		<span class="text-lg font-semibold text-cynWhite text-center">
			<?php echo esc_html(get_the_title($personnel_id)); ?>
		</span>
	</div>
</div>
