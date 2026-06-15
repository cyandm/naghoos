<?php /* Template Name: Contact Us */ ?>
<?php

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$contact_title = get_field('contact_title');
$under_title = get_field('under_title');
$contact_img = get_field('contact_img');
$contact_box_title = get_field('contact_box_title');
$visit_title = get_field('visit_title');
$opening_hours_title = get_field('opening_hours_title');
$map_address_title = get_field('map_address_title');
$contact_info_title = get_field('contact_info_title');

$address_text = get_option('address_text');
$phone_number = get_option('phone_number');
$whatsapp_number = get_option('whatsapp_number');
$instagram_text = get_option('instagram_text');
$instagram_link = get_option('instagram_link');
$email_address = get_option('email_address');
$working_hours = get_option('working_hours');
$working_days = get_option('working_days');

// Build location array
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

get_header();
?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="container">

      <section class="flex max-lg:flex-col-reverse">

            <div class="w-full lg:w-1/2 flex flex-col gap-4 justify-center">

                  <div>
                        <p class="text-3xl font-bold">
                              <?php echo $contact_title ? $contact_title : __('ارتباط با انتشارات ناقوس', 'jonubgard'); ?>
                        </p>

                        <p class="text-cynBlack/50 text-xl mt-1 font-semibold">
                              <?php echo $under_title ? $under_title : __('چیزی هست که بخوای به ما بگی', 'jonubgard'); ?>
                        </p>
                  </div>

                  <form hx-post="<?php echo rest_url('cyn/v1/contact_form') ?>" hx-target=".result" hx-swap="innerHTML" hx-on::after-request="const resultEl = document.querySelector('.result'); resultEl.style.display = 'block'; resultEl.style.opacity = '1'; resultEl.textContent = '<?php _e('با موفقیت ارسال شد', 'jonubgard'); ?>'; setTimeout(() => { resultEl.style.transition = 'opacity 0.5s ease-out'; resultEl.style.opacity = '0'; setTimeout(() => { resultEl.style.display = 'none'; }, 500); }, 5000);" action="" method="post" id="contact_form" class="flex flex-col gap-4 w-5/6 max-lg:w-full [&_label]:w-full [&_input]:w-full [&_textarea]:w-full">

                        <label for="fullname" class="relative">
                              <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                                    <?php icon::print('User,-Profile-7'); ?>
                              </div>
                              <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    required
                                    placeholder="<?php _e('نام و نام خانوادگی'); ?>"
                                    class="focus:outline-none w-full font-medium text-base ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20" />
                        </label>

                        <label for="phone" class="relative">
                              <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                                    <?php icon::print('Phone,-Call-11'); ?>
                              </div>
                              <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    placeholder="<?php _e('شماره تماس'); ?>"
                                    pattern="[0-9]{11}"
                                    required
                                    dir="rtl"
                                    class="focus:outline-none w-full font-medium text-base ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20" />

                        </label>

                        <label for="email" class="relative">
                              <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                                    <?php icon::print('email-mail-letter'); ?>
                              </div>
                              <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="<?php _e('ایمیل (اختیاری)'); ?>"
                                    class="focus:outline-none w-full font-medium text-base ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20" />
                        </label>

                        <label for="message" class="relative">
                              <div class="size-6 text-cynBlack/60 absolute start-3 top-3">
                                    <?php icon::print('Chat,-Messages-1'); ?>
                              </div>
                              <textarea name="message" id="message" rows="3" maxlength="65525" placeholder="<?php _e('پیام شما'); ?>" required class="focus:outline-none w-full text-cynTextGray resize-y m-0 align-bottom ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20"></textarea>
                        </label>

                        <div class="flex justify-end ">
                              <button type="submit" class="primary-btn flex justify-center items-center gap-1">
                                    <span class="size-6 stroke-[1.5]">
                                          <?php icon::print('Send') ?>
                                    </span>
                                    <span class="pt-1">
                                          <?php _e('ارسال پیام'); ?>
                                    </span>
                              </button>
                        </div>

                  </form>

                  <div class="result bg-green-500 text-white text-base font-semibold rounded-2xl p-3 shadow-item fixed top-4 right-4 z-50 pt-4" style="display:none; opacity: 0; transition: opacity 0.5s ease-out;"></div>

            </div>

            <div class="flex justify-center items-center lg:w-1/2">
                  <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-contain sm:w-2/3 lg:w-full']) ?>
            </div>

      </section>

      <section class="bg-white rounded-3xl border border-cynBlack/20 mt-9 p-6">

            <p class="text-3xl font-bold text-cynBlack">
                  <?php echo $contact_box_title ? $contact_box_title : __('مشتاق دیدار شماییم', 'jonubgard'); ?>
            </p>

            <div class="flex flex-col mt-5">

                  <div class="flex flex-col md:flex-row text-xl gap-3">

                        <div class="flex flex-col gap-3 flex-1 md:p-4">

                              <p class="text-xl font-bold text-cynBlack">
                                    <?php echo $visit_title ? $visit_title : __('مراجعه حضوری', 'jonubgard'); ?>
                              </p>

                              <div class="font-semibold text-sm text-cynBlack/60">
                                    <a href="<?php echo get_option('address_link'); ?>">
                                          <?php echo $address_text; ?>
                                    </a>
                              </div>

                              <p class="font-bold text-cynBlack text-xl">
                                    <?php echo $map_address_title ? $map_address_title : __('آدرس روی نقشه', 'jonubgard'); ?>
                              </p>

                              <?php if ($location) : ?>
                                    <div class="flex gap-3 items-center">
                                          <?php foreach ($location as $loc): ?>
                                                <a href="<?php echo $loc['link'] ?>" rel="noopener noreferrer" target="_blank" class="size-8 flex items-center justify-center text-cynBlack rounded-[10px] overflow-hidden">
                                                      <img src="<?php echo esc_url($loc['img']) ?>" alt="<?php echo basename($loc['img'], '.svg'); ?>" class="size-full object-contain">
                                                </a>
                                          <?php endforeach; ?>
                                    </div>
                              <?php endif; ?>

                        </div>

                        <?php if ($working_days && $working_hours): ?>
                              <div class="flex flex-col gap-3 text-cynBlack flex-1 md:p-4 max-md:mt-4">
                                    <p class="text-xl font-bold ">
                                          <?php echo $opening_hours_title ? $opening_hours_title : __('ساعات فعال', 'jonubgard'); ?>
                                    </p>
                                    <p class="font-semibold text-sm text-cynBlack/60">
                                          <?php echo $working_days; ?>
                                    </p>
                                    <p class=" font-semibold text-sm text-cynBlack/60">
                                          <?php echo $working_hours; ?>
                                    </p>
                              </div>
                        <?php endif; ?>

                  </div>

                  <div class="flex flex-col gap-3 md:p-4">

                        <p class="font-bold text-cynBlack text-xl pt-6">
                              <?php echo $contact_info_title ? $contact_info_title : __('شماره تماس و شبکه های اجتماعی', 'jonubgard'); ?>
                        </p>

                        <div class="flex flex-col gap-3">

                              <div class="flex lg:items-center gap-3 lg:gap-6 max-lg:flex-col">

                                    <a href="tel:<?php echo $phone_number; ?>" class="flex items-center gap-1 text-sm font-semibold whitespace-nowrap max-lg:text-base">

                                          <i class="bg-cynRed p-1 rounded-full flex justify-center items-center size-7 text-white stroke-[1.5]">
                                                <?php icon::print('Phone,-Call-11'); ?>
                                          </i>

                                          <span class="text-cynBlack/60 pt-1"><?php echo $phone_number; ?></span>
                                    </a>

                                    <a href="<?php echo 'https://wa.me/' . $whatsapp_number; ?>" class="flex items-center gap-1 text-sm font-semibold whitespace-nowrap max-lg:text-base">
                                          <i class="bg-cynRed p-1 rounded-full flex justify-center items-center size-7 text-white stroke-[1.5]">
                                                <?php icon::print('Whatsup'); ?>
                                          </i>
                                          <span class="text-cynBlack/60 pt-1"><?php echo $whatsapp_number; ?></span>
                                    </a>

                              </div>

                              <div class="flex lg:items-center gap-3 lg:gap-6 max-lg:flex-col">

                                    <a href="<?php echo $instagram_link; ?>" class="flex items-center gap-1 text-sm font-semibold whitespace-nowrap max-lg:text-base">
                                          <i class="bg-cynRed p-1 rounded-full flex justify-center items-center size-7 text-white stroke-[1.5]">
                                                <?php icon::print('Instagram'); ?>
                                          </i>
                                          <span class="text-cynBlack/60 pt-1"><?php echo $instagram_text; ?></span>
                                    </a>

                                    <a href="mailto:<?php echo $email_address; ?>" class="flex items-center gap-1 text-sm font-semibold whitespace-nowrap max-lg:text-base">
                                          <i class="bg-cynRed p-1 rounded-full flex justify-center items-center size-7 text-white stroke-[1.5]">
                                                <?php icon::print('Emails,-Letter,-Mail-1'); ?>
                                          </i>
                                          <span class="text-cynBlack/60 pt-1"><?php echo $email_address; ?></span>
                                    </a>

                              </div>

                        </div>

                  </div>

            </div>

      </section>

</main>


<?php get_footer(); ?>