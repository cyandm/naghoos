<?php

use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$attributes   = [];

for ($i = 1; $i <= 3; $i++) {
    $title = get_field('home_attributes_title_' . $i, $home_page_id);
    $image = get_field('home_attributes_image_' . $i, $home_page_id);

    if ($title !== '' || !empty($image)) {
        $attributes[] = [
            'title' => $title,
            'image' => $image,
        ];
    }
}

if (empty($attributes)) {
    return;
}
?>

<section class="container my-15">
    <div class="flex flex-wrap justify-center gap-4">
        <?php foreach ($attributes as $attribute) : ?>
            <div class="w-full sm:w-[calc(50%-0.5rem)] xl:w-[calc(33.333%-0.67rem)]">
                <?php Templates::getCard('attribute', $attribute); ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
