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
		self::forPersonnel();
		self::forBlogs();

		//Taxonomies

		//Page Templates
		self::forContactUs();
		self::forAboutUs();

		//Menu Items

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
}
