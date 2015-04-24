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
 Repository path: $HeadURL: https://svn.nq.pl/wordpress/branches/dynamite/igniter/wp-content/themes/igniter/lib/SilverWp/ShortCode/View/View.php $
 Last committed: $Revision: 2211 $
 Last changed by: $Author: padalec $
 Last changed date: $Date: 2015-01-22 17:51:49 +0100 (Cz, 22 sty 2015) $
 ID: $Id: View.php 2211 2015-01-22 16:51:49Z padalec $
*/
namespace SilverWp\ShortCode\Vc\View;

if ( ! class_exists( '\SilverWp\ShortCode\Vc\ViewAbstract' ) ) {

    /**
     * Short Code view renderer
     *
     * @category WordPress
     * @package SilverWp
     * @subpackage ShortCode
     * @author Michal Kalkowski <michal at dynamite-studio.pl>
     * @copyright Dynamite-Studio.pl 2014
     * @version $Id: View.php 2211 2015-01-22 16:51:49Z padalec $
     */

    class ViewAbstract extends \WPBakeryShortCode {
        const SC_PRFX = 'ds_';

        /**
         * Overrides method from WPBakeryShortCode::getFileName()
         * change templates short code file name (remove prefix)
         *
         * @return string
         * @access protected
         */
        protected function getFileName() {
            $file_name = str_replace( self::SC_PRFX, '', $this->shortcode );

            return $file_name;
        }

        /**
         *
         * Overrides method from WPBakeryShortCode::prepareAtts()
         * replace single quot to double recursive
         *
         * @return array
         * @access protected
         */
        protected function prepareAtts( $attributes ) {
            $return = array();
            if ( is_array( $attributes ) ) {
                foreach ( $attributes as $key => $val ) {
                    if ( is_array( $val ) ) {
                        $return[ $key ] = $this->prepareAtts( $val );
                    } else {
                        $return[ $key ] = preg_replace( '/\`\`/', '"', $val );
                    }
                }
            }

            return $return;
        }
    }
}