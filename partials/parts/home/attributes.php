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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 lg:gap-5">
        <?php foreach ($attributes as $attribute) :
            Templates::getCard('attribute', $attribute);
        endforeach; ?>
    </div>
</section>
