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
namespace SilverWp\Customizer\Panel;

use SilverWp\Customizer\Section\SectionAbstract;
use SilverWp\Customizer\Section\SectionInterface;
use SilverWp\Debug;
use SilverWp\ParamsAbstract;

if ( ! class_exists( 'SilverWp\Customizer\Panel\PanelAbstract' ) ) {

	/**
	 * Base customizer panel class
	 *
	 * @category   WordPress
	 * @package    SilverWp
	 * @subpackage Customizer\Panel
	 * @author     Michal Kalkowski <michal at silversite.pl>
	 * @copyright  SilverSite.pl (c) 2015
	 * @version    0.6
	 * @abstract
	 */
	abstract class PanelAbstract extends ParamsAbstract
		implements PanelInterface {

		/**
		 * Unique panel id
		 *
		 * @var string
		 * @access protected
		 */
		protected $panel_id;

		/**
		 *
		 * Section handler
		 *
		 * @var array
		 * @access private
		 */
		private $sections = array();

		/**
		 * Display dumps
		 *
		 * @var bool
		 */
		protected $debug = false;

		/**
		 *
		 * Class constructor register customizer
		 *
		 * @access public
		 */
		public function __construct() {
			$this->setUp();
			$this->addPanel();
		}

		/**
		 *
		 * Add panel to customizer
		 *
		 * @throws \SilverWp\Customizer\Panel\Exception
		 * @access public
		 */
		private function addPanel() {
			if ( ! isset( $this->panel_id ) ) {
				throw new Exception(
					Translate::translate( 'If You want add panel to your section first define panel_id class property.' )
				);
			}
			$params = $this->getParams();
			if ( $this->debug ) {
				Debug::dumpPrint( $this->sections );
			}

			\Kirki::add_panel( sanitize_title($this->panel_id), $params );
		}

		/**
		 *
		 * Create sections elements add to panel
		 *
		 * @access protected
		 * @abstract
		 */
		protected abstract function setUp();

		/**
		 *
		 * Add section to panel container
		 *
		 * @param \SilverWp\Customizer\Section\SectionAbstract $section
		 *
		 * @access public
		 */
		public function addSection( SectionAbstract $section ) {
			$section->setPanelId( $this->getPanelId() );
			$this->sections[] = $section;
			if ( $this->debug ) {
				Debug::dumpPrint( $this->sections );
			}
			$section->addSection();
		}

		/**
		 *
		 * Get unique panel id
		 *
		 * @return string
		 * @access public
		 */
		public function getPanelId() {
			return $this->panel_id;
		}

		/**
		 * Get all registered sections
		 *
		 * @return array
		 * @access public
		 */
		public function getSections() {
			return $this->sections;
		}

	}
}