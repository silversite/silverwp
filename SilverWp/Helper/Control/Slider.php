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

if ( ! class_exists( 'SilverWp\Helper\Control\Slider' ) ) {

	/**
	 * Control slider
	 *
	 * @category   WordPress
	 * @package    SilverWp
	 * @subpackage Helper\Control
	 * @author     Michal Kalkowski <michal at silversite.pl>
	 * @copyright  SilverSite.pl (c) 2015
	 * @version    0.5
	 */
	class Slider extends MultiControlAbstract {
        protected $type = 'slider';

        /**
         *
         * Set min (start) value
         *
         * @param int|float $value
         *
         * @return $this
         * @access public
         */
        public function setMin( $value ) {
            $this->setting[ 'min' ] = $value;

            return $this;
        }

        /**
         *
         * Set max (end) value
         *
         * @param int|float $value
         *
         * @return $this
         * @access public
         */
        public function setMax( $value ) {
            $this->setting[ 'max' ] = $value;

            return $this;
        }

        /**
         *
         * Set steps value
         *
         * @param int|float $value
         *
         * @return $this
         * @access public
         */
        public function setStep( $value ) {
            $this->setting[ 'step' ] = $value;

            return $this;
        }
    }
}