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

		//Taxonomies

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
		$acfGroup->layoutFields->addTab('home_faq', 'سوالات متداول (تنظیمات کلی سوالات متداول)');
		$acfGroup->basicFields->addText('home_faq_title', 'عنوان سوالات متداول', ['placeholder' => 'سوالات متداول', 'width' => '50%']);
		$acfGroup->basicFields->addText('home_faq_under_title', 'متن زیر عنوان سوالات متداول', ['placeholder' => 'متن زیر عنوان سوالات متداول', 'width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_faq_button', 'لینک و متن دکمه سوالات متداول (اگر این قسمت پر نشود شماره تلفن  پشتیبانی نمایش داده خواهد شد)', ['width' => '100%']);

		$acfGroup->layoutFields->addTab('home_about_tab', 'درباره ما');
		$acfGroup->basicFields->addText('home_about_title', 'عنوان', ['width' => '50%']);
		$acfGroup->contentFields->addTextEditor('home_about_description', 'توضیحات', ['width' => '50%']);
		$acfGroup->contentFields->addFile('home_about_file', 'تصویر/ویدیو', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_about_cover', 'تصویر کاور ویدیو (اگر ویدیو انتخاب کرده اید لطفا این قسمت را تکمیل کنید)', ['width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_about_button', 'لینک و متن دکمه', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_category_tab', 'دسته بندی ها');
		$acfGroup->contentFields->addTaxonomy('home_product_cats', 'انتخاب دسته‌بندی‌های محصولات جهت نمایش', ['taxonomy' => 'product_cat', 'field_type' => 'multi_select', 'return_format' => 'id', 'width' => '50%']);

		$acfGroup->layoutFields->addTab('home_amazing_tab', 'شگفت چیان');
		$acfGroup->choiceFields->addRadio('home_amazing_title_type', 'نمایش عنوان سکشن', [
			'choices' => [
				'text' => 'متن',
				'image' => 'تایپوگرافی (تصویر)',
			],
			'default_value' => 'text',
			'layout' => 'horizontal',
			'width' => '100%',
		]);
		$acfGroup->basicFields->addText('home_amazing_title', 'عنوان', [
			'width' => '50%',
			'conditional_logic' => [
				[
					[
						'field' => 'acf_radio_home_amazing_title_type_',
						'operator' => '==',
						'value' => 'text',
					],
				],
			],
		]);
		$acfGroup->contentFields->addFile('home_amazing_typography', 'تصویر تایپوگرافی', [
			'width' => '50%',
			'conditional_logic' => [
				[
					[
						'field' => 'acf_radio_home_amazing_title_type_',
						'operator' => '==',
						'value' => 'image',
					],
				],
			],
		]);
		$acfGroup->basicFields->addText('home_amazing_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->advanceFields->addDateTimePicker('home_amazing_date_time', 'تاریخ و زمان نمایش محصولات شگفت چیان', ['display_format' => 'd/m/Y H:i', 'return_format' => 'YmdHis', 'width' => '50%']);
		$acfGroup->choiceFields->addRadio('home_amazing_status', 'در صورت تمام شدن تایم مجددا تمدید شود؟ (در صورت خیر بعد از تمام شدن تایم سکشن نمایش داده نخواهد شد)', [
			'choices' => ['yes' => 'بله', 'no' => 'خیر'],
			'default_value' => 'no',
			'layout' => 'horizontal',
			'width' => '50%'
		]);
		$acfGroup->relationshipFields->addPostObject('home_amazing_post', 'انتخاب محصولات شگفت چیان (اگر محصولی انتخاب کنید دیگر از دسته بندی شگفت چیان محصولی نمایش داده نمیشود)', ['post_type' => 'product', 'multiple' => 1, 'width' => '50%']);

		$acfGroup->layoutFields->addTab('home_show_product_one_tab', 'نمایش محصول');
		$acfGroup->basicFields->addText('home_show_product_one_title', 'عنوان', ['width' => '50%']);
		$acfGroup->basicFields->addText('home_show_product_one_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_show_product_one_image', 'تصویر', ['width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_show_product_one_button', 'لینک و متن دکمه', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_double_banner_tab', 'بنر دوتایی');
		$acfGroup->contentFields->addImage('home_double_banner_left_image', 'تصویر بنر چپ', ['width' => '50%']);
		$acfGroup->basicFields->addUrl('home_double_banner_left_link', 'لینک', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_double_banner_right_image', 'تصویر بنر راست', ['width' => '50%']);
		$acfGroup->basicFields->addUrl('home_double_banner_right_link', 'لینک', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_banner_tab', 'بنر');
		$acfGroup->contentFields->addImage('home_banner_image_desktop', 'تصویر بنر برای دسکتاپ', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_banner_image_mobile', 'تصویر بنر برای موبایل', ['width' => '50%']);

		$acfGroup->choiceFields->addRadio('home_banner_type', 'نمایش بنر', [
			'choices' => [
				'without_text' => 'بنر بدون متن',
				'with_text' => 'بنر با متن',
			],
			'default_value' => 'without_text',
			'layout' => 'horizontal',
			'width' => '100%',
		]);

		$acfGroup->basicFields->addText('home_banner_title', 'عنوان', [
			'width' => '50%',
			'conditional_logic' => [
				[
					[
						'field' => 'acf_radio_home_banner_type_',
						'operator' => '==',
						'value' => 'with_text',
					],
				],
			],
		]);
		$acfGroup->basicFields->addText('home_banner_under_title', 'متن زیر عنوان', [
			'width' => '50%',
			'conditional_logic' => [
				[
					[
						'field' => 'acf_radio_home_banner_type_',
						'operator' => '==',
						'value' => 'with_text',
					],
				],
			],
		]);

		$acfGroup->relationshipFields->addLink('home_banner_button', 'لینک و متن دکمه', [
			'width' => '50%',
			'conditional_logic' => [
				[
					[
						'field' => 'acf_radio_home_banner_type_',
						'operator' => '==',
						'value' => 'with_text',
					],
				],
			],
		]);

		$acfGroup->basicFields->addUrl('home_banner_link', 'لینک بنر', [
			'width' => '50%',
			'conditional_logic' => [
				[
					[
						'field' => 'acf_radio_home_banner_type_',
						'operator' => '==',
						'value' => 'without_text',
					],
				],
			],
		]);

		$acfGroup->layoutFields->addTab('home_show_product_two_tab', 'نمایش محصول');
		$acfGroup->basicFields->addText('home_show_product_two_title', 'عنوان', ['width' => '50%']);
		$acfGroup->basicFields->addText('home_show_product_two_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_show_product_two_image', 'تصویر', ['width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_show_product_two_button', 'لینک و متن دکمه', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_banner_two_tab', 'بنر دوم');
		$acfGroup->contentFields->addImage('home_banner_two_image_desktop', 'تصویر بنر برای دسکتاپ', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_banner_two_image_mobile', 'تصویر بنر برای موبایل', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_popular_products_tab', 'محصولات پرطرفدار');
		$acfGroup->basicFields->addText('home_popular_products_title', 'عنوان', ['width' => '50%']);
		$acfGroup->basicFields->addText('home_popular_products_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->relationshipFields->addPostObject('home_popular_products_selected', 'انتخاب محصولات پرطرفدار', ['post_type' => 'product', 'multiple' => 1, 'width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_popular_products_button', 'لینک و متن دکمه', ['width' => '50%']);
		$acfGroup->basicFields->addText('home_popular_products_persian_title', 'متن فارسی عمودی در وسط اسلایدر', ['width' => '50%']);
		$acfGroup->basicFields->addText('home_popular_products_english_title', 'متن انگلیسی عمودی در وسط اسلایدر', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_show_product_three_tab', 'نمایش محصول');
		$acfGroup->basicFields->addText('home_show_product_three_title', 'عنوان', ['width' => '50%']);
		$acfGroup->basicFields->addText('home_show_product_three_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_show_product_three_image', 'تصویر', ['width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_show_product_three_button', 'لینک و متن دکمه', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_events_tab', 'رویدادها');
		$acfGroup->basicFields->addText('home_events_title', 'عنوان', ['width' => '33%']);
		$acfGroup->basicFields->addText('home_events_under_title', 'متن زیر عنوان', ['width' => '33%']);
		$acfGroup->relationshipFields->addLink('home_events_button', 'لینک و متن دکمه', ['width' => '33%']);
		$acfGroup->contentFields->addImage('home_events_image_desktop', 'تصویر بنر برای دسکتاپ', ['width' => '50%']);
		$acfGroup->contentFields->addImage('home_events_image_mobile', 'تصویر بنر برای موبایل', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('home_blogs_tab', 'بلاگ ها');
		$acfGroup->relationshipFields->addPostObject('home_blogs_selected', 'انتخاب بلاگ ها (در صورتی که انتخاب نشوند جدیدترین ها نمایش داده میشود)', ['post_type' => 'post', 'multiple' => 1, 'width' => '100%']);


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
}
