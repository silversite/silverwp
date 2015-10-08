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
 Repository path: $HeadURL: https://svn.nq.pl/wordpress/branches/dynamite/igniter/wp-content/themes/igniter/lib/SilverWp/ShortCode/Form/Element/ElementMultiAbstract.php $
 Last committed: $Revision: 2184 $
 Last changed by: $Author: padalec $
 Last changed date: $Date: 2015-01-21 13:20:08 +0100 (Śr, 21 sty 2015) $
 ID: $Id: ElementMultiAbstract.php 2184 2015-01-21 12:20:08Z padalec $
*/
namespace SilverWp\ShortCode\Vc\Control;

if ( ! class_exists( '\SilverWp\ShortCode\Vc\ControlMultiAbstract' ) ) {

    /**
     * Extra method for multi elements
     *
     * @category WordPress
     * @package SilverWp
     * @subpackage ShortCode\Vc\Control
     * @author Michal Kalkowski <michal at dynamite-studio.pl>
     * @copyright Dynamite-Studio.pl & silversite.pl 2015
     * @version $Id: ElementMultiAbstract.php 2184 2015-01-21 12:20:08Z padalec $
     */
    abstract class ControlMultiAbstract extends ControlAbstract implements ControlMultiInterface {

        /**
         * Multi elements options
         *
         * @param array $options
         *
         * @param bool  $flip_data
         *
         * @return $this
         * @access public
         */
        public function setOptions( array $options, $flip_data = true ) {
            if ( $flip_data ) {
                $options = $this->flipSourceData( $options );
            }
            $this->setValue( $options );

            return $this;
        }

        /**
         *
         * Add mew option
         *
         * @param string $value
         * @param string $label
         *
         * @return $this
         * @access public
         */
        public function addOption( $value, $label ) {
            $this->setting[ 'value' ][ $label ] = $value;

            return $this;
        }

        /**
         * Default value for multi element
         *
         * @param string $value
         *
         * @return $this
         * @access public
         */
        public function setDefault( $value ) {
            $this->setting[ 'std' ] = $value;

            return $this;
        }

        /**
         * Flip array from array( value => label) to array (label => value)
         *
         * @param array $data
         *
         * @param bool  $empty add empty element to beginning of array
         *
         * @return array
         * @access protected
         */
        protected function flipSourceData( array $data, $empty = false ) {
            $return = array();

            if ( $empty ) {
                $return[ '' ] = '';
            }

            foreach ( $data as $value ) {
                $return[ $value[ 'label' ] ] = $value[ 'value' ];
            }

            return $return;
        }

        /**
         * Get default value of element
         *
         * @return mixed|null
         * @access public
         */
        public function getDefault() {
            $default = $this->getSetting( 'std' );

            return $default;
        }

    }
}
