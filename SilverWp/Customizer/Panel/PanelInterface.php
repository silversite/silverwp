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


/**
 * Interface for panels params
 *
 * @category   WordPress
 * @package    SilverWp
 * @subpackage SilverWp\Customizer\Section
 * @author     Michal Kalkowski <michal at silversite.pl>
 * @copyright  SilverSite.pl 2015
 * @version    $Revision:$
 */
interface PanelInterface {

    /**
     *
     * Get unique panel id
     *
     * @return string
     * @access public
     */
    public function getPanelId();

    /**
     *
     * Add section to panel container
     *
     * @param \SilverWp\Customizer\Section\SectionAbstract $section
     *
     * @access public
     */
    public function addSection( SectionAbstract $section );

    /**
     * Get all registered sections
     *
     * @return array
     * @access public
     */
    public function getSections();

}