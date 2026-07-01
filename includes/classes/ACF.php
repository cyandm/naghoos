<?php

/**
 * ACF Class
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

use Cyan\Theme\Helpers\Validators;
use Cyan\Theme\Helpers\ACF\AcfGroup;


class ACF
{

	public static function init()
	{
		$isDev = ENVIRONMENT === 'development';
		$isDev ? null : add_filter('acf/settings/show_admin', '__return_false', 100);

		if (! function_exists('acf_add_local_field_group')) {
			return;
		}


		add_action('acf/include_fields', [__CLASS__, 'registerAllACF']);
	}

	/**
	 * Register all ACF fields for the individual post types, taxonomies, page templates, and menu items
	 * @return void
	 */
	public static function registerAllACF()
	{
		//PostTypes
		self::forHome();
		self::forSlider();
		self::forPersonnel();
		self::forBlogs();
		self::forProduct();
		self::forTestimonial();

		//Taxonomies
		self::forProductCategory();

		//Page Templates
		self::forContactUs();
		self::forAboutUs();

		//Menu Items

	}

	private static function forHome()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('home_best_selling_products_tab', 'محصولات پر فروش');
		$acfGroup->basicFields->addText('home_best_selling_products_title', 'عنوان', ['width' => '50%', 'default_value' => 'پر‌فروش ها']);
		$acfGroup->basicFields->addText('home_best_selling_products_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->relationshipFields->addPostObject('home_best_selling_products_selected', 'انتخاب محصولات پر فروش (در صورت انتخاب نکردن به صورت دیفالت 4 تا از محصولات پر فروش نمایش داده خواهد شد)', ['post_type' => 'product', 'multiple' => 1, 'width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_best_selling_products_button', 'لینک و متن دکمه', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_discounted_products_tab', 'محصولات تخفیف دار');
		$acfGroup->basicFields->addText('home_discounted_products_title', 'عنوان', ['width' => '50%', 'default_value' => 'تخفیف های داغ']);
		$acfGroup->basicFields->addText('home_discounted_products_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->relationshipFields->addPostObject('home_discounted_products_selected', 'انتخاب محصولات تخفیف دار (در صورت انتخاب نکردن به صورت دیفالت 4 تا از محصولات تخفیف دار نمایش داده خواهد شد)', ['post_type' => 'product', 'multiple' => 1, 'width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_discounted_products_button', 'لینک و متن دکمه', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_discounted_products_background', 'تصویر بکگراند برای محصولات تخفیف دار', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_products_categories_tab', 'دسته بندی های محصولات');
		$acfGroup->contentFields->addTaxonomy('home_products_categories_row_one', 'انتخاب دسته‌بندی‌های محصولات جهت نمایش در ردیف اول', ['taxonomy' => 'product_cat', 'field_type' => 'multi_select', 'return_format' => 'id', 'width' => '50%']);
		$acfGroup->contentFields->addTaxonomy('home_products_categories_row_two', 'انتخاب دسته‌بندی‌های محصولات جهت نمایش در ردیف دوم', ['taxonomy' => 'product_cat', 'field_type' => 'multi_select', 'return_format' => 'id', 'width' => '50%']);

		$acfGroup->layoutFields->addTab('home_show_products_with_cat_tab', 'نمایش محصولات بر اساس دسته بندی');
		$acfGroup->basicFields->addText('home_show_products_with_cat_title', 'عنوان بر اساس دسته بندی، آموزشی ها، تخصصی ها و یا...', ['width' => '50%', 'default_value' => 'آموزشی ها']);
		$acfGroup->basicFields->addText('home_show_products_with_cat_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->relationshipFields->addTaxonomy('home_show_products_with_cat_selected', 'انتخاب یک دسته از محصولات (در صورت انتخاب نکردن این قسمت نمایش داده نخواهد شد)', ['taxonomy' => 'product_cat', 'return_format' => 'id', 'width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_show_products_with_cat_button', 'لینک و متن دکمه', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_personnel_tab', 'مولف ها (پرسنل)');
		$acfGroup->basicFields->addText('home_personnel_title', 'عنوان مولف ها', ['default_value' => 'مولف ها']);
		$acfGroup->relationshipFields->addPostObject('home_personnel_select', 'انتخاب افراد جهت نمایش (در صورت انتخاب نکردن به صورت دیفالت 12 تا از مولف ها نمایش داده خواهد شد)', ['post_type' => 'personnel', 'multiple' => 1]);

		$acfGroup->layoutFields->addTab('home_testimonial_tab', 'نظرات همراهان');
		$acfGroup->basicFields->addText('home_testimonial_title', 'عنوان نظرات همراهان', ['default_value' => 'نظرات همراهان']);
		$acfGroup->relationshipFields->addPostObject('home_testimonial_select', 'انتخاب نظرات همراهان (در صورت انتخاب نکردن به صورت دیفالت 6 تا از نظرات همراهان نمایش داده خواهد شد)', ['post_type' => 'testimonial', 'multiple' => 1]);

		$acfGroup->layoutFields->addTab('home_history_tab', 'تاریخچه');
		$acfGroup->basicFields->addText('home_history_title', 'عنوان تاریخچه', ['default_value' => 'تاریخچه‌ی ما', 'width' => '50%']);
		$acfGroup->basicFields->addText('home_history_title_content', 'عنوان محتوای تاریخچه', ['width' => '50%']);
		$acfGroup->contentFields->addTextEditor('home_history_content', 'متن تاریخچه', ['toolbar' => 'advanced', 'width' => '50%']);
		$acfGroup->contentFields->addImage('home_history_image', 'تصویر تاریخچه', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_events_tab', 'رویدادها');
		$acfGroup->basicFields->addText('home_events_title', 'عنوان', ['width' => '33%']);
		$acfGroup->basicFields->addText('home_events_under_title', 'متن زیر عنوان', ['width' => '33%']);
		$acfGroup->relationshipFields->addLink('home_events_button', 'لینک و متن دکمه', ['width' => '33%']);
		$acfGroup->contentFields->addImage('home_events_image_desktop', 'تصویر بنر برای دسکتاپ', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_events_image_mobile', 'تصویر بنر برای موبایل', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_attributes_tab', 'ویژگی های ما');
		for ($i = 1; $i <= 3; $i++) {
			$acfGroup->basicFields->addText('home_attributes_title_' . $i, 'عنوان ویژگی ' . $i, ['width' => '50%']);
			$acfGroup->contentFields->addImage('home_attributes_image_' . $i, 'تصویر ویژگی ' . $i, ['width' => '50%']);
		}

		$acfGroup->layoutFields->addTab('home_blogs_tab', 'بلاگ ها (غیرفعال میباشد)');
		$acfGroup->relationshipFields->addPostObject('home_blogs_selected', 'انتخاب بلاگ ها (در صورتی که انتخاب نشوند جدیدترین ها نمایش داده میشود)', ['post_type' => 'post', 'multiple' => 1, 'width' => '100%']);

		$acfGroup->layoutFields->addTab('home_faq_tab', 'سوالات متداول (تنظیمات کلی سوالات متداول)');
		$acfGroup->basicFields->addText('home_faq_title', 'عنوان سوالات متداول', ['width' => '50%', 'default_value' => 'سوالات متداول', 'placeholder' => 'سوالات متداول']);
		$acfGroup->basicFields->addText('home_faq_under_title', 'متن زیر عنوان سوالات متداول', ['placeholder' => 'متن زیر عنوان سوالات متداول', 'width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_faq_button', 'لینک و متن دکمه سوالات متداول (اگر این قسمت پر نشود شماره تلفن  پشتیبانی نمایش داده خواهد شد)', ['width' => '100%']);

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/home.php');

		// register group
		$acfGroup->register('Home-Page');
	}

	private static function forSlider()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->contentFields->addImage('desktop_slider', 'عکس اسلایدر برای دسکتاپ', ['width' => '50%']);
		$acfGroup->contentFields->addImage('mobile_slider', 'عکس اسلایدر برای موبایل', ['width' => '50%']);
		$acfGroup->basicFields->addText('url', 'لینک اسلایدر', [
			'default_value' => '#',
			'width' => '50%',
		]);
		$acfGroup->choiceFields->addBoolean('slider_wide', 'نمایش عریض', [
			'default_value' => 1,
			'ui_on_text' => 'بله',
			'ui_off_text' => 'خیر',
			'width' => '50%',
		]);

		//location
		$acfGroup->setLocation('post_type', '==', 'slider');

		// register group
		$acfGroup->register('Slider');
	}

	private static function forPersonnel()
	{

		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->basicFields->addText('personnel_position', 'سمت');

		//location
		$acfGroup->setLocation('post_type', '==', 'personnel');

		// register group
		$acfGroup->register('Personnel');
	}

	private static function forBlogs()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('blogs_bests_weekly', 'بهترین های هفته');
		$acfGroup->basicFields->addText('blogs_bests_weekly_title', 'عنوان بهترین های هفته');
		$acfGroup->relationshipFields->addPostObject('blogs_bests_weekly_select', 'انتخاب بهترین های هفته (در صورت انتخاب نکردن به صورت دیفالت پر بیننده ترین ها نمایش داده خواهد شد)', [
			'post_type' => 'blog',
			'multiple' => 1,
			'return_format' => 'id',
		]);

		$acfGroup->layoutFields->addTab('blogs_newest', 'آخرین پست ها');
		$acfGroup->contentFields->addImage('blogs_newest_img', 'عکس بکگراند آخرین پست ها');
		$acfGroup->relationshipFields->addPostObject('blogs_newest_select', 'انتخاب آخرین پست ها (در صورت انتخاب نکردن به صورت دیفالت 12 تا از آخرین پست ها نمایش داده خواهد شد)', [
			'post_type' => 'blog',
			'multiple' => 1,
			'return_format' => 'id',
		]);

		$acfGroup->layoutFields->addTab('blogs_categories', 'دسته بندی ها');
		$acfGroup->basicFields->addText('blogs_categories_title', 'عنوان دسته بندی ها');

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/blog.php');

		//register group
		$acfGroup->register('Blogs');
	}

	private static function forProduct()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('product_excert_tab', 'خلاصه کتاب');
		$acfGroup->contentFields->addTextEditor('product_excert', 'متن یا توضیحات خلاصه کتاب', ['width' => '100%', 'toolbar' => 'advanced']);

		$acfGroup->layoutFields->addTab('product_excert_voice_tab', 'خلاصه صوتی کتاب');
		$acfGroup->contentFields->addFile('product_excert_voice', 'فایل صوتی');

		$acfGroup->layoutFields->addTab('product_related_tab', 'محصولات مرتبط');
		$acfGroup->relationshipFields->addPostObject(
			'product_related_products',
			'انتخاب محصولات مرتبط (حداکثر 12 — در صورت کمتر، بقیه خودکار پر می‌شود)',
			['post_type' => 'product', 'multiple' => 1, 'return_format' => 'id', 'width' => '100%']
		);

		//location
		$acfGroup->setLocation('post_type', '==', 'product');

		//register group
		$acfGroup->register('Product');
	}

	private static function forTestimonial()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->basicFields->addText('testimonial_name', 'نام نظر دهنده', ['width' => '50%']);
		$acfGroup->basicFields->addNumber('testimonial_rate', 'امتیاز نظر دهنده (از 1 تا 5)', ['width' => '50%', 'min' => 1, 'max' => 5, 'default_value' => 5]);

		//location
		$acfGroup->setLocation('post_type', '==', 'testimonial');

		//register group
		$acfGroup->register('Testimonial');
	}

	private static function forProductCategory()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->basicFields->addText('product_category_description', 'توضیحات دسته بندی (نمایش در صفحه اصلی)');
		$acfGroup->advanceFields->addColorPicker('product_category_color', 'رنگ بکگراند عکس دسته بندی', ['width' => '50%']);

		//location
		$acfGroup->setLocation('taxonomy', '==', 'product_cat');

		//register group
		$acfGroup->register('Product Category');
	}

	private static function forContactUs()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->basicFields->addText('contact_title', 'عنوان');

		$acfGroup->basicFields->addText('under_title', 'متن زیر عنوان');

		$acfGroup->basicFields->addText('contact_box_title', 'عنوان باکس تماس با ما');

		$acfGroup->basicFields->addText('visit_title', 'عنوان آدرس حضوری');
		$acfGroup->basicFields->addText('opening_hours_title', 'عنوان ساعت کاری');
		$acfGroup->basicFields->addText('map_address_title', 'عنوان آدرس روی نرم افزار ها');
		$acfGroup->basicFields->addText('contact_info_title', 'عنوان اطلاعات تماس');

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/contact-us.php');

		//register group
		$acfGroup->register('Contact Us');
	}

	private static function forAboutUs()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('about_hero', 'هیرو (بالای صفحه)');
		$acfGroup->contentFields->addImage('about_img_hero_desktop', 'عکس هیرو دسکتاپ (بالای صفحه)');
		$acfGroup->contentFields->addImage('about_img_hero_mobile', 'عکس هیرو موبایل (بالای صفحه)');

		$acfGroup->layoutFields->addTab('about_txt_one', 'متن اول');
		$acfGroup->contentFields->addTextEditor('about_text_one', 'متن زیر هیرو', ['toolbar' => 'advanced']);

		$acfGroup->layoutFields->addTab('about_video', 'ویدیو');
		$acfGroup->basicFields->addText('about_video_title', 'عنوان ویدیو');
		$acfGroup->contentFields->addFile('about_video_file', 'فایل ویدیو');
		$acfGroup->contentFields->addImage('about_video_cover', 'عکس پوشش ویدیو');

		$acfGroup->layoutFields->addTab('about_personnel', 'مولف ها (پرسنل)');
		$acfGroup->basicFields->addText('about_personnel_title', 'عنوان مولف ها', ['default_value' => 'مولف ها']);
		$acfGroup->relationshipFields->addPostObject('about_personnel_select', 'انتخاب افراد جهت نمایش', [
			'post_type' => 'personnel',
			'multiple' => 1,
		]);

		$acfGroup->layoutFields->addTab('about_txt_two', 'متن دوم');
		$acfGroup->contentFields->addTextEditor('about_text_two', 'متن زیر پرسنل', ['toolbar' => 'advanced']);

		//location

		$acfGroup->setLocation('page_template', '==', 'templates/about-us.php');

		//register group
		$acfGroup->register('About Us');
	}
}
