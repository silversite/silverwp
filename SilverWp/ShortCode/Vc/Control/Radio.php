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
namespace SilverWp\ShortCode\Vc\Control;

/**
 * Radio control for VC
 *
 * @category WordPress
 * @package SilverWp
 * @subpackage
 * @author Michal Kalkowski <michal at dynamite-studio.pl>
 * @copyright SilverSite.pl (c) 2015
 * @version 0.1
 * @TODO Not Implemented
 */
if ( ! class_exists( '\SilverWp\ShortCode\Vc\Control' ) ) {

    class Radio extends ElementMultiAbstract implements NewElementInterface {
        protected $type = 'radio';
        protected $holder = 'div';

        public function createElement( $settings, $value ) {
            silverwp_debug_var($value);

            $this->setValue($value);
            $html = '<div class="silver-vc-field-radio">';

            $options = $this->getOptions();
            foreach ($options as $option) {
                $html .= $this->html($option['value'], $option['label']);
            }
            $html .= '</div>';
            return $html;
        }

        private function html($value, $label) {

            $name  = esc_attr( $this->getName() );
            $type  = esc_attr( $this->type );
            $html  = '<div class="radio">';
                $html .= '<label>';
                    $html .= '<input type="radio" ';
                        $html .= 'name="'. $name .'" ';
                        $html .= 'class="wpb_vc_param_value wpb-textinput ' . $name . ' ' . $type . '_field" ';
                        $html .= 'value="' . esc_attr( $value ) . '" ';
                        if ( $this->getValue() == $value ) {
                            $html .= 'checked="checked" ';
                        }
                    $html .= '/>';
                    $html .= $label;
                $html .= '</label>';
            $html .= '</div>';
            return $html;
        }
    }
} 