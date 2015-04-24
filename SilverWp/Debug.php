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
namespace SilverWp;

if ( ! class_exists( 'SilverWp\Debug' ) ) {

    /**
     *
     * Debug
     *
     * @category WordPress
     * @package SilverWp
     * @author Michal Kalkowski <michal at silversite.pl>
     * @copyright Dynamite-Studio.pl & silversite.pl 2015
     * @version $Revision:$
     */
    class Debug {

        /**
         * Prate dump variable used var_dump function.
         *
         * @param mixed $variable variable to dump
         * @param null|string $label label displayed before dumping
         *
         * @access public
         * @static
         */
        public static function dump( $variable, $label = null ) {
            if ( ! is_null( $label ) ) {
                echo '<p><strong>' . $label . '</strong></p>';
            }
            echo '<pre style="color:red;text-align:left;width:950px;">';
            var_dump( $variable );
            echo '</pre>';
        }

        /**
         *
         * Prate dump variable used print_r function.
         *
         * @param mixed $variable variable to dump
         * @param null|string $label label displayed before dumping
         *
         * @static
         * @access public
         */
        public static function dumpPrint( $variable, $label = null ) {
            if (!is_null($label)) {
                echo '<p><strong>' . $label .'</strong></p>';
            }
            echo '<pre style="color:red;text-align:left;width:950px;">';
            print_r( $variable );
            echo '</pre>';
        }

    }
}