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

/**
 *
 *
 *
 * @category  WordPress
 * @package   SilverWp
 * @subpackage
 * @author    Michal Kalkowski <michal at silversite.pl>
 * @copyright SilverSite.pl (c) 2016
 * @version
 */
class NBP {

	/**
	 * @var array
	 */
	protected $rates;

	/**
	 * @var string
	 */
	protected $publicationDate;

	/**
	 * @var string
	 */
	protected $tableNo;

	/**
	 * Table list form current year
	 * @var string
	 */
	private $tableListUri = 'http://www.nbp.pl/Kursy/xml/';

	/**
	 * @var array
	 */
	protected $tablesList = [];

	/**
	 * @return array
	 */
	public function getTablesList() {
		return $this->tablesList;
	}

	/**
	 * NBP constructor.
	 *
	 * @param null|int $year start from 2002
	 */
	public function __construct($year = null) {
		$content          = file_get_contents( $this->tableListUri . "dir$year.txt" );
		$this->tablesList = array_filter( explode( PHP_EOL, $content ) );
		$this->tablesList = array_map( 'trim', $this->tablesList );
	}

	/**
	 *
	 * @param string $tableName values: a, b or c
	 * @param null|string   $date format: YYMMDD
	 *
	 * @return bool
	 * @access public
	 */
	public function getTableName($tableName = 'a', $date = null) {
		//if is null get last table
		if ( is_null( $date ) ) {
			$this->tablesList = array_reverse( $this->tablesList );
		}
		foreach ( $this->tablesList as $table ) {
			if ( substr( $table, 0, - strlen( $table ) + 1 ) == $tableName ) {
				if ( is_null( $date ) ) {
					return $table;
				} else {
					if ( substr( $table, 5, strlen( $table ) ) === $date ) {
						return $table;
					}
				}
			}
		}
		return false;
	}

	/**
	 * @param string $fileName
	 *
	 * @return array|bool
	 * @access public
	 */
	public function getData( $fileName ) {
		$xml2Assoc = new Xml2Assoc();
		$data      = $xml2Assoc->parseFile( $this->tableListUri . $fileName . '.xml', true );
		if ( isset( $data['tabela_kursow'][0] ) ) {
			$data = $data['tabela_kursow'][0];
			$this->publicationDate = $data['data_publikacji'];
			$this->tableNo = $data['numer_tabeli'];
			$this->rates = $data['pozycja'];

			return $data;
		}

		return false;
	}

	/**
	 * @return string
	 * @access public
	 */
	public function getPublicationDate() {
		return $this->publicationDate;
	}

	/**
	 * @return string
	 * @access public
	 */
	public function getTableNo() {
		return $this->tableNo;
	}

	/**
	 * @return array
	 * @access public
	 */
	public function getRates() {
		return $this->rates;
	}

	/**
	 * @param float $lastRate
	 * @param float $currentRate
	 *
	 * @return float
	 * @access public
	 */
	public function calculateChangeRate($lastRate, $currentRate) {
		$change_rate = ( ( $currentRate - $lastRate ) / $lastRate ) * 100;
		return number_format( $change_rate, 4, '.', '' );
	}
}