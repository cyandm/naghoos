<?php

use Cyan\Theme\Helpers\Icon;

$home_page_id = get_option('page_on_front');
$home_customer_club_title = get_field('home_customer_club_title', $home_page_id);
$home_customer_club_under_title = get_field('home_customer_club_under_title', $home_page_id);
$home_customer_club_image = get_template_directory_uri() . '/assets/image/noise-white.svg';
$home_customer_club_taghcheh_image = get_template_directory_uri() . '/assets/image/taghcheh.svg';

if ($home_customer_club_image && $home_customer_club_title && $home_customer_club_under_title): ?>

    <section class="my-16 pt-6 pb-5 bg-cover bg-center bg-no-repeat" style="background-image: url('<?php echo esc_url($home_customer_club_image); ?>')">

        <div class="container flex flex-col justify-center items-center">

            <p class="text-2xl md:text-3xl font-bold text-cynBlack text-center mb-1">
                <?php echo esc_html($home_customer_club_title); ?>
            </p>

            <p class="text-base md:text-2xl font-medium text-cynBlack/80 leading-9 text-center mb-6">
                <?php echo esc_html($home_customer_club_under_title); ?>
            </p>

            <form
                id="customer_club_form"
                method="post"
                action=""
                hx-post="<?php echo esc_url(rest_url('cyn/v1/customer_club_form')); ?>"
                hx-target=".customer-club-form-result"
                hx-swap="innerHTML"
                hx-on::before-request="(function(){ var submitBtn = document.getElementById('customer-club-submit-btn'); if(!submitBtn){ return; } submitBtn.disabled = true; submitBtn.setAttribute('aria-busy', 'true'); submitBtn.classList.add('opacity-70', 'cursor-wait'); var idleText = submitBtn.querySelector('[data-submit-idle]'); var loadingIcon = submitBtn.querySelector('[data-submit-loading]'); if(idleText){ idleText.style.display = 'none'; } if(loadingIcon){ loadingIcon.style.display = 'inline-flex'; } })()"
                hx-on::after-request="(function(ev){ var submitBtn = document.getElementById('customer-club-submit-btn'); if(submitBtn){ submitBtn.disabled = false; submitBtn.removeAttribute('aria-busy'); submitBtn.classList.remove('opacity-70', 'cursor-wait'); var idleText = submitBtn.querySelector('[data-submit-idle]'); var loadingIcon = submitBtn.querySelector('[data-submit-loading]'); if(idleText){ idleText.style.display = 'inline'; } if(loadingIcon){ loadingIcon.style.display = 'none'; } } var resultEl = document.querySelector('.customer-club-form-result'); resultEl.style.display = 'block'; resultEl.style.opacity = '1'; if(ev.detail.successful){ try { var d = JSON.parse(ev.detail.xhr.responseText); resultEl.textContent = d.message || '<?php echo esc_js(__('با موفقیت ثبت شد', 'naghoos')); ?>'; } catch(e){ resultEl.textContent = '<?php echo esc_js(__('با موفقیت ثبت شد', 'naghoos')); ?>'; } resultEl.style.background = ''; var form = document.getElementById('customer_club_form'); if(form){ form.reset(); } } else { try { var err = JSON.parse(ev.detail.xhr.responseText); resultEl.textContent = err.error || '<?php echo esc_js(__('خطا در ارسال', 'naghoos')); ?>'; } catch(e){ resultEl.textContent = '<?php echo esc_js(__('خطا در ارسال', 'naghoos')); ?>'; } resultEl.style.background = '#ef4444'; } setTimeout(function(){ resultEl.style.transition = 'opacity 0.5s ease-out'; resultEl.style.opacity = '0'; setTimeout(function(){ resultEl.style.display = 'none'; }, 500); }, 5000); })(event)"
                class="w-full max-w-96 mb-6 md:mb-3">

                <div class="flex flex-1 items-center bg-white rounded-full border border-cynBlack/10 relative hover:border-cynBlack focus-within:border-cynBlack transition-all duration-300">
                    <span class="shrink-0 size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2" aria-hidden="true">
                        <?php Icon::print('User,-Profile'); ?>
                    </span>

                    <input type="tel" id="phone" name="phone" placeholder="<?php esc_attr_e('شماره تلفن', 'naghoos'); ?>" pattern="[0-9]{11}" required dir="rtl" class="w-full border-0 bg-transparent py-5 ps-10 pe-3 text-base font-medium text-cynBlack placeholder:text-cynBlack/50 focus:outline-none rounded-full" />

                    <button id="customer-club-submit-btn" type="submit" class="text-base font-medium bg-cynRed text-cynWhite rounded-4xl border border-cynRed !py-2.5 !px-5 end-3 top-1/2 -translate-y-1/2 absolute inline-flex items-center justify-center min-w-20">
                        <span data-submit-idle><?php esc_html_e('عضویت', 'naghoos'); ?></span>
                        <span data-submit-loading class="hidden items-center justify-center">
                            <span class="inline-block size-6 border-2 border-white/40 border-t-white rounded-full animate-spin" aria-hidden="true"></span>
                            <span class="sr-only"><?php esc_html_e('در حال ارسال...', 'naghoos'); ?></span>
                        </span>
                    </button>
                </div>

            </form>

            <div class="customer-club-form-result bg-green-500 text-white text-base font-semibold rounded-2xl p-3 shadow-item fixed top-4 right-4 z-50 pt-4" style="display:none; opacity: 0; transition: opacity 0.5s ease-out;"></div>

        </div>

        <div class="w-full">
            <img src="<?php echo esc_url($home_customer_club_taghcheh_image); ?>" alt="<?php esc_attr_e('باشگاه مشتریان', 'naghoos'); ?>" class="w-full h-full object-cover object-center">
        </div>

    </section>

<?php endif; ?>