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
/*
 Repository path: $HeadURL: $
 Last committed: $Revision: $
 Last changed by: $Author: $
 Last changed date: $Date: $
 ID: $Id: $
*/
namespace SilverWp\Customizer\Control;

if ( ! class_exists( '\SilverWp\Customizer\Control\Background' ) ) {

    /**
     * Background field
     *
     * @category WordPress
     * @package SilverWp
     * @subpackage Wp\Customizer\Control
     * @author Michal Kalkowski <michal at silversite.pl>
     * @copyright Dynamite-Studio.pl & silversite.pl 2015
     * @version $Revision:$
     * @link http://kirki.org/#Background
     */
    class Background extends ControlAbstract {
        protected $type = 'checkbox';

        /**
         * Set default color
         *
         * @param string $color
         *
         * @return $this
         * @access public
         */
        public function setDefaultColor( $color ) {
            $this->setting[ 'default' ][ 'color' ] = $color;

            return $this;
        }

        /**
         * Set default value for image
         *
         * @param string $image url to image
         *
         * @return $this
         * @access public
         */
        public function setDefaultImage( $image ) {
            $this->setting[ 'default' ][ 'image' ] = $image;

            return $this;
        }

        /**
         *
         * Set default repeat value
         *
         * @param string $repeat
         *
         * @return $this
         * @access public
         */
        public function setDefaultRepeat( $repeat ) {
            $this->setting[ 'default' ][ 'repeat' ] = $repeat;

            return $this;
        }

        /**
         *
         * Set default size value
         *
         * @param string $size
         *
         * @return $this
         * @access public
         */
        public function setDefaultSize( $size ) {
            $this->setting[ 'default' ][ 'size' ] = $size;

            return $this;
        }

        /**
         *
         * Set default attach value
         *
         * @param string $attach
         *
         * @return $this
         * @access public
         */
        public function setDefaultAttach( $attach ) {
            $this->setting[ 'default' ][ 'attach' ] = $attach;

            return $this;
        }

        /**
         *
         * Set default position value
         *
         * @param string $position
         *
         * @return $this
         * @access public
         */
        public function setDefaultPosition( $position ) {
            $this->setting[ 'default' ][ 'position' ] = $position;

            return $this;
        }

        /**
         * Set default value for opacity. Rang between 0-100.
         * If you want to disable opacity for your control,
         * then you can simply set false
         *
         * @param int|boolean $opacity
         *
         * @return $this
         * @access public
         *
         */
        public function setDefaultOpacity( $opacity ) {
            $this->setting[ 'default' ][ 'opacity' ] = $opacity;

            return $this;
        }

        public function setDefault( array $default ) {
            $this->setting[ 'default' ] = $default;

            return $this;
        }
    }
}