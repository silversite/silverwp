<?php

/*
 * Copyright (C) 2014 Michal Kalkowski <michal at silversite.pl>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace SilverWp;

use SilverWp\Interfaces\Params;

/**
 *
 * Setup params array
 *
 * @category  WordPress
 * @package   SilverWp
 * @author    Michal Kalkowski <michal at silversite.pl>
 * @copyright SilverSite.pl (c) 2015
 * @version   0.6
 */
abstract class ParamsAbstract implements Params {

	/**
	 * @var array
	 */
	private $params = array();

	/**
	 * Add single param
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return mixed
	 * @access public
	 */
	public function addParam( $name, $value ) {
		$this->params[ $name ] = $value;

		return $this;
	}

	/**
	 * Set params
	 *
	 * @param array $params
	 *
	 * @return $this
	 * @access public
	 */
	public function setParams( array $params ) {
		$this->params = $params;

		return $this;
	}

	/**
	 * Get all params
	 *
	 * @return array
	 * @access public
	 */
	public function getParams() {
		return $this->params;
	}
}