<?php

$args  = get_query_var('args', []);
$title = $args['title'] ?? '';
$image = $args['image'] ?? '';

if ($title === '' && empty($image)) {
    return;
}
?>

<div class="bg-[#F2F2F2]/40 rounded-3xl p-3 flex flex-row items-center justify-center gap-3 h-full w-full">
    <?php if (!empty($image)) : ?>
        <div class="shrink-0">
            <?php echo wp_get_attachment_image((int) $image, 'full', false, [
                'class' => 'size-40 object-contain',
                'alt'   => $title !== '' ? esc_attr($title) : '',
            ]); ?>
        </div>
    <?php endif; ?>
    <?php if ($title !== '') : ?>
        <p class="text-xl font-semibold text-cynBlack/60 text-right">
            <?php echo esc_html($title); ?>
        </p>
    <?php endif; ?>
</div>