<?php

use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$home_history_title = get_field('home_history_title', $home_page_id);
$home_history_title_content = get_field('home_history_title_content', $home_page_id);
$home_history_content = get_field('home_history_content', $home_page_id);
$home_history_image = get_field('home_history_image', $home_page_id);

if ($home_history_image && $home_history_title && $home_history_content): ?>
    <section class="container my-15 ">
        <div class="flex flex-col gap-3 md:gap-5">
            <p class="text-3xl font-bold text-cynBlack">
                <?php echo $home_history_title; ?>
            </p>

            <div class="flex flex-col md:flex-row items-center gap-3 md:gap-11">
                <div class="w-full md:w-4/9">
                    <?php echo wp_get_attachment_image($home_history_image, 'full', false, ['class' => 'w-full h-[324px] md:h-[554px] lg:h-[504px] object-cover object-center rounded-3xl']); ?>
                </div>
                <div class="w-full md:w-5/9 flex flex-col gap-2 md:gap-4 text-base md:text-xl font-normal text-cynBlack/80 leading-9">
                    <p class="text-2xl font-medium text-cynBlack">
                        <?php echo $home_history_title_content; ?>
                    </p>
                    <?php echo $home_history_content; ?>
                </div>
            </div>

        </div>

    </section>
<?php endif; ?>