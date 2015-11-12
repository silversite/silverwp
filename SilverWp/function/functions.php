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

use SilverWp\Customizer\CustomizerAbstract;
use SilverWp\File\FileException;
use SilverWp\File\File;
use SilverWp\Helper\Option;
use SilverWp\Helper\Thumbnail;


if ( ! function_exists( '\SilverWp\get_attachment_image_from_url' ) ) {

    /**
     * Returns an HTML image element representing an attachment file.
     *
     * @param string $image_file_url - URL of media
     * @param string $size - size name of image
     *
     * @return string
     * @access public
     * @author Marcin Dobroszek <marcin at silversite.pl>
     */
    function get_attachment_image_from_url( $image_file_url, $size = 'thumbnail' ) {
        $attachmentId = Thumbnail::getAttachmentIdFromUrl( $image_file_url );

        return wp_get_attachment_image( $attachmentId, $size );
    }
}

