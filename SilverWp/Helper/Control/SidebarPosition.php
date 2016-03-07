<?php
/*
 * Copyright (C) 2014 Michal Kalkowski <michal at silversite.pl>
 *
 * SilverWp is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * SilverWp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
namespace SilverWp\Helper\Control;

use SilverWp\Debug;
use SilverWp\FileSystem;
use SilverWp\Helper\MetaBox;
use SilverWp\Helper\Option;
use SilverWp\SilverWp;
use SilverWp\Translate;

if ( ! class_exists( 'SilverWp\Helper\Control\SidebarPosition' ) ) {

	/**
	 *
	 * Control with sidebar position to choice
	 *
	 * @category   WordPress
	 * @package    SilverWp
	 * @subpackage Helper\Control
	 * @author     Michal Kalkowski <michal at silversite.pl>
	 * @copyright  SilverSite.pl 2015
	 * @version    $Revision:$
	 */
	class SidebarPosition extends RadioImage {

        /**
         *
         * Class constructor
         *
         * @param string $name
         * @access public
         */
        public function __construct( $name = 'sidebar' ) {
            parent::__construct( $name );

			$images_uri = FileSystem::getDirectory( 'images_uri' );

			$sidebar_positions = array(
				array(
					'value' => 0,
					'label' => Translate::translate( 'None' ),
					'img'   => $images_uri . 'admin/sidebar/icon_0_sidebar_off.png',
				),
				array(
					'value' => 1,
					'label' => Translate::translate( 'Left sidebar' ),
					'img'   => $images_uri . 'admin/sidebar/icon_1_sidebar_off.png',
				),
				array(
					'value' => 2,
					'label' => Translate::translate( 'Right sidebar' ),
					'img'   => $images_uri . 'admin/sidebar/icon_2_sidebar_off.png',
				),
			);

			$this->setOptions( $sidebar_positions );
		}

        /**
         *
         * Check the current post or page have a sidebar
         *
         * @static
         * @access public
         * @return boolean
         */
        public static function isDisplayed() {
			if ( is_front_page() && get_option('show_on_front') === 'page' ) { // Homepage with a static page
				$post_type = 'page';
				$post_id = get_option('page_on_front');
			} elseif ( is_page() ) { // Page single view
				$post_type = 'page';
				$post_id     = get_queried_object_id();
			} elseif ( is_single() ) { // Single view (Post or CPT)
				$page_object = get_queried_object();
				$post_type   = isset( $page_object->post_type ) ? $page_object->post_type : 'post';
				$post_id     = get_queried_object_id();
			} else { // special views (search, author, tag, date, archive)
				//$sidebar = \SilverWp\get_theme_option( 'blogposts_sidebar' ) === '0' ? false : true;
				$sidebar = false;
			}
			/*
			} elseif ( is_search() || is_author() || is_tag() || is_date() || is_archive() ) { // special views
				//$sidebar = \SilverWp\get_theme_option( 'blogposts_sidebar' ) === '0' ? false : true;
				$sidebar = false;
			} else { // single view
				$page_object = get_queried_object();
				$post_type   = isset( $page_object->post_type ) ? $page_object->post_type : 'post';
				$post_id     = get_queried_object_id();
			}
			*/
			if ( isset($post_type) ) { // not special view (single view)
				$sidebar = \SilverWp\get_meta_box( $post_type, 'sidebar', $post_id );

				if ( $sidebar === false ) { // $sidebar === false --> any value set (post added before theme was turn on)
					if ( $post_type === 'post' && \SilverWp\get_theme_option( 'blogposts_sidebar' ) !== '0' ) {
						$sidebar = true;
					} elseif ( $post_type === 'page' && \SilverWp\get_theme_option( 'pages_sidebar' ) !== '0' ) {
						$sidebar = true;
					}
				}
			}

			return apply_filters( 'sage/display_sidebar', $sidebar );
		}
	}
}