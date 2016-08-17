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
namespace SilverWp;

if ( ! class_exists( 'SilverWp\Debug' ) ) {

    /**
     *
     * Debug
     *
     * @category  WordPress
     * @package   SilverWp
     * @author    Michal Kalkowski <michal at silversite.pl>
     * @copyright SilverSite.pl 2015
     * @version   $Revision:$
     */
    class Debug {
        /**
         * Display debug information for this allowed IPs
         *
         * @var array
         * @static
         */
	    public static $allowed_ips = array(
		    '31.183.61.125', '127.0.0.1', '31.182.69.228',
		    '192.168.10.1', '192.168.50.1', '31.183.53.127',
		    '192.168.11.5', '192.168.11.4'
	    );
        /**
         * Prate dump variable used var_dump function.
         *
         * @param mixed       $variable variable to dump
         * @param null|string $label    label displayed before dumping
         *
         * @access public
         * @static
         */
        public static function dump( $variable, $label = null ) {
	        if ( in_array( $_SERVER['REMOTE_ADDR'], self::$allowed_ips ) ) {
		        if ( ! is_null( $label ) ) {
	                echo '<p><strong>' . $label . '</strong></p>';
	            }
	            echo '<pre style="width:950px; padding:6px 18px; background:#fff; color:red; text-align:left; position:relative; z-index:9999999; ">';
	            var_dump( $variable );
	            echo '</pre>';
	        }
        }

        /**
         *
         * Prate dump variable used print_r function.
         *
         * @param mixed       $variable variable to dump
         * @param null|string $label    label displayed before dumping
         *
         * @static
         * @access public
         */
        public static function dumpPrint( $variable, $label = null ) {
	        if ( in_array( $_SERVER['REMOTE_ADDR'], self::$allowed_ips ) ) {
		        if ( ! is_null( $label ) ) {
			        echo '<p><strong>' . $label . '</strong></p>';
		        }
		        echo '<pre style="width:950px; padding:6px 18px; background:#fff; color:red; text-align:left; position:relative; z-index:9999999; ">';
		        print_r( $variable );
		        echo '</pre>';
	        }
        }

    }
}