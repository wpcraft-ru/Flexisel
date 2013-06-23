<?php
/*
Plugin Name: Flexisel
Plugin URI: http://localhost/
Description: Jquery плагин Flexisel переписанный Rasko под Wordpress. Представляет из себя «карусель».
Version: 0.1
Author: Rasko
Author URI: http://localhost/
*/

// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
 
if (!class_exists('Flexisel')) {
	class Flexisel {
	// Хранение внутренних данных
	public $data = array();
		// Реализуем синглтон?
		private static $_this;

		var $HAS_SHORTCODE_KEY = '_has_flexisel_shortcode';
		// 'yes'/'no' vs. true/false as get_post_meta() returns '' for false and not found.
		 var $shortcode_used = 'no';
		// Конструктор объекта
		// Инициализация основных переменных
		function Flexisel()
		{
			// Синглтон
			self::$_this = $this;
			// Регистрируем наш шорткод
			add_shortcode('flexisel', array (&$this, 'do_shortcode'));
			// Поиск шорткода
			add_filter('the_content', array(&$this, 'the_content'), 12); // выполнится после do_shortcode
			add_action('save_post', array(&$this, 'save_post'));
			add_action('wp_print_styles', array(&$this, 'wp_print_styles'), 20);
			add_action('wp_print_scripts', array(&$this, 'wp_print_scripts'));
			//$post_attachments = get_posts( array (
			//	'post_type' => 'attachment',
			//	'post_parent' => $post->ID
			//));
			//print_r($post_attachments);

		}
		
		function do_shortcode($atts)
		{
			$this->shortcode_used = 'yes';
			// Извлекаем значение page... (если значение '-1', то оно не указано вообще)
			extract(shortcode_atts(array(
				'page' => -1
			), $atts));
			
			// Если не указан параметр page - делать ничего не нужно.
			if ($page < 0) 
				return false;
				
			//если посты были вгружены прямо в этот пост то возьмем их
			$gpargsL = array(
				'post_status'    => null,
				'post_type'      => 'attachment', // Тип: аттач.
				'post_parent'    => $page, // Родительский постовой.
				'post_mime_type' => 'image', // Картинка.
				'order'          => 'ASC' // Сортировка ASC или DESC?
			);
			$post_imagesL = get_posts($gpargsL);
			//print_r($post_imagesL);

			//Включаем буферизацию вывода
			ob_start ();
			include_once('flexisel-template.php');
			// Получаем данные
			$output = ob_get_contents ();
			// Отключаем буферизацию
			ob_end_clean ();
			
			return $output;
		}
		
		function wp_print_styles($args) 
		{
			global $post;
			if ('no' != get_post_meta($post->ID, $this->HAS_SHORTCODE_KEY, true)){
				wp_enqueue_style('flexisel', plugins_url('css/style.css', __FILE__));
			}
		}
		
		function wp_print_scripts($args) 
		{
			global $post;
			if ('no' != get_post_meta($post->ID, $this->HAS_SHORTCODE_KEY, true)){
				wp_enqueue_script('flexisel', plugins_url('js/jquery.flexisel.js', __FILE__), array('jquery'));
			}
		}
		
		function save_post($post_id) 
		{
			delete_post_meta($post_id, $this->HAS_SHORTCODE_KEY);
			wp_remote_request(get_permalink($post_id), array('blocking' => false));
		}
  
		function the_content($content) 
		{
			global $post;
			if ('' === get_post_meta($post->ID, $this->HAS_SHORTCODE_KEY, true)){
				update_post_meta($post->ID, $this->HAS_SHORTCODE_KEY, $this->shortcode_used);
			}
			remove_filter('the_content', array(&$this, 'the_content'), 12);
			return $content;
		}
	}
}
global $flexisel;
$flexisel = new Flexisel();
?>