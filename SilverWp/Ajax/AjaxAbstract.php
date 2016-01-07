<?php
/*
 * Copyright (C) 2014 Michal Kalkowski <michal at silversite.pl>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
namespace SilverWp\Ajax;

use SilverWp\Ajax\Exception;
use SilverWp\Debug;
use SilverWp\FileSystem;
use SilverWp\Helper\Filter;
use SilverWp\SilverWp;
use SilverWp\SingletonAbstract;
use SilverWp\Translate;
use SilverWp\View;

if ( ! class_exists( 'SilverWp\Ajax\AjaxAbstract' ) ) {
	/**
	 * Ajax server response
	 *
	 * @author        Michal Kalkowski <michal at silversite.pl>
	 * @version       0.5
	 * @category      WordPress
	 * @package       SilverWp
	 * @tutorial      http://wordpress.stackexchange.com/questions/106427/using-ajax-with-a-class-file
	 * @copyright     2009 - 2015 (c) SilverSite.pl
	 * @abstract
	 */
	abstract class AjaxAbstract extends SingletonAbstract
		implements AjaxInterface {

		/**
		 *
		 * Ajax JS file
		 *
		 * @var string
		 * @access protected
		 */
		protected $ajax_js_file = 'main.js';

		/**
		 *
		 * Ajax JS script handle name
		 *
		 * @var string
		 * @access protected
		 */
		protected $ajax_handler = 'sage_js';

		/**
		 * Ajax response function name
		 *
		 * @var string
		 * @access protected
		 */
		protected $name = null;

		/**
		 * nonce security field
		 *
		 * @var string
		 * @access protected
		 */
		protected $nonce = null;

		/**
		 *
		 * class constructor
		 *
		 * @throws Exception
		 * @access protected
		 */
		protected function __construct() {
			if ( \is_null( $this->name ) ) {
				$parent_class = get_called_class();
				throw new Exception(
					Translate::translate(
						'Variable %s::$name is requaied and can\'t be empty.', $parent_class
					)
				);
			}
			if ( ! is_null( $this->ajax_js_file ) ) {
				\add_action( 'wp_loaded', array( $this, 'scriptsRegister' ) );
			}
			# Could as well be: wp_enqueue_scripts or login_enqueue_scripts
			\add_action( 'wp_enqueue_scripts',
				array( $this, 'scriptsEnqueue' ) );
			\add_action( 'wp_enqueue_scripts',
				array( $this, 'scriptsLocalize' ) );

			# Guests:
			\add_action( "wp_ajax_nopriv_{$this->name}",
				array( $this, 'ajaxResponse' ) );
			# Logged in users:
			//TODO add iterface and method ajaxPrivResponse
			\add_action( "wp_ajax_{$this->name}",
				array( $this, 'ajaxResponse' ) );
		}

		/**
		 *
		 * get action hook name
		 *
		 * @return string
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 *
		 * register ajax js scripts
		 *
		 * @return void
		 * @access public
		 */
		public function scriptsRegister() {
			\wp_register_script(
				$this->ajax_handler
				, $this->getJsUri() . $this->ajax_js_file
				, array(
					'jquery',
				)
				, SILVERWP_VER
				, true
			);
		}

		/**
		 *
		 * Get assets URI
		 *
		 * @return string
		 * @access protected
		 */
		protected function getJsUri() {
			$file_system = FileSystem::getInstance();
			$js_uri      = $file_system->getDirectories( 'js_uri' );

			return $js_uri;
		}

		/**
		 *
		 * Enqueue ajax scripts
		 *
		 * @return void
		 * @access public
		 */
		public function scriptsEnqueue() {
			\wp_enqueue_script( $this->ajax_handler );
		}

		/**
		 *
		 * Localize and add params to AJAX JS
		 *
		 * @access public
		 * @return void
		 * @access public
		 */
		public function scriptsLocalize() {
			$this->nonce = wp_create_nonce( $this->getNonceName() );

			wp_localize_script(
				$this->ajax_handler,
				$this->name,
				array(
					'nonce'  => $this->nonce,
					'url'    => admin_url( 'admin-ajax.php' ),
					'action' => $this->name
				)
			);
		}

		/**
		 *
		 * Get name of nonce field
		 *
		 * @return string
		 * @access public
		 */
		public function getNonceName() {
			return $this->name . 'nonce';
		}

		/**
		 *
		 * Verifies the AJAX request to prevent
		 * processing requests external of the blog.
		 *
		 * @return void
		 * @access public
		 */
		public function checkAjaxReferer() {
			\check_ajax_referer( $this->getNonceName(), 'nonce', true );
		}

		/**
		 *
		 * Get params from JS request
		 *
		 * @access protected
		 *
		 * @param string $name
		 * @param int    $filter_options
		 * @param null   $default
		 *
		 * @return mixed filtered request data
		 */
		protected function getRequestData(
			$name, $filter_options = FILTER_DEFAULT, $default = null
		) {
			$request = null;
			if ( $this->isGet( $name ) ) {
				$request = Filter::get_var( $name, $filter_options, $default );
			} elseif ( $this->isPost( $name ) ) {
				$request = Filter::post_var( $name, $filter_options, $default );
			} else {
				$request = $default;
			}

			return $request;
		}

		/**
		 *
		 * Check if request is by $_GET data
		 *
		 * @access private
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		private function isGet( $name ) {
			if ( filter_has_var( INPUT_GET, $name ) ) {
				return true;
			}

			return false;
		}

		/**
		 *
		 * Check if request variable is from $_POST
		 *
		 * @access private
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		private function isPost( $name ) {
			if ( filter_has_var( INPUT_POST, $name ) ) {
				return true;
			}

			return false;
		}

		/**
		 *
		 * ajax response in json format
		 *
		 * @param array|object $data array or object with data should by returned
		 */
		protected function responseJson( $data ) {
			\header( 'Content-Type: application/json' );
			echo \json_encode( $data );
			exit;
		}

		/**
		 * Ajax response in HTML format
		 *
		 * @param array  $data      data
		 * @param string $view_file view file name
		 *
		 * @return string rendered view
		 * @throws Exception
		 */
		protected function responseHtml( array $data, $view_file = null ) {
			if ( \is_null( $view_file ) ) {
				$view_file = $this->name;
			}
			try {
				$view_path = FileSystem::getDirectory( 'views' );
				$view      = View::getInstance()->load( $view_path . 'ajax/'. $view_file, $data );

				//fix bug with display 0 or 1 in AJAX response
				die( $view );
			} catch ( Exception $ex ) {
				echo $ex->displayAdminNotice( $ex->getMessage() );
			}
		}

		/**
		 * check the header is from ajax request
		 *
		 * @return boolean
		 * @static
		 * @access public
		 */
		public static function isAjax() {
			if ( function_exists( 'apache_request_headers' ) ) {
				$headers = \apache_request_headers();
			} else {
				$headers = $_SERVER;
			}
			// fix some servers have lower other upper case in keys
			$headers = array_change_key_case( $headers, CASE_LOWER );

			if ( isset( $headers['x-requested-with'] )
			     && $headers['x-requested-with'] == 'XMLHttpRequest'
			) {
				return true;
			}

			return false;
		}

		/**
		 * Get view file from AJAX request
		 *
		 * @return string view file name
		 * @access protected
		 */
		protected function getViewFileFromRequest() {
			$view = $this->getRequestData( 'view' );
			if ( is_null( $view ) || $view == '0' ) {
				return null;
			}
			$view_file = $this->name . '-' . $view;

			return $view_file;
		}
	}
}
