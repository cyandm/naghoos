<?php

$args  = get_query_var('args', []);
$title = $args['title'] ?? '';
$image = $args['image'] ?? '';

if ($title === '' && empty($image)) {
    return;
}
?>

<div class="bg-[#F2F2F2] rounded-2xl p-5 lg:p-6 flex flex-row items-center justify-between gap-4 h-full w-full">
    <?php if (!empty($image)) : ?>
        <div class="shrink-0">
            <?php echo wp_get_attachment_image((int) $image, 'full', false, [
                'class' => 'w-[72px] h-[72px] lg:w-[88px] lg:h-[88px] object-contain',
                'alt'   => $title !== '' ? esc_attr($title) : '',
            ]); ?>
        </div>
    <?php endif; ?>
    <?php if ($title !== '') : ?>
        <p class="text-base font-medium text-cynBlack/70 text-right flex-1">
            <?php echo esc_html($title); ?>
        </p>
    <?php endif; ?>
</div>