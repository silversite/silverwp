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

namespace SilverWp\PostType;

use SilverWp\Ajax\PostLike;
use SilverWp\Core;
use SilverWp\CoreInterface;
use SilverWp\Db\Query;
use SilverWp\Debug;
use SilverWp\Helper\Option;
use SilverWp\Helper\Paginator\Pager;
use SilverWp\Helper\Paginator\PaginatorInterface;
use SilverWp\Helper\RecursiveArray;
use SilverWp\Helper\UtlArray;
use SilverWp\MetaBox\Exception as MetaBoxException;
use SilverWp\MetaBox\MetaBoxInterface;
use SilverWp\PostInterface;
use SilverWp\PostRelationship\Relationship;
use SilverWp\PostType\Exception;
use SilverWp\PostType\PostTypeInterface;
use SilverWp\SingletonAbstract;
use SilverWp\Taxonomy\Exception as TaxonomyException;
use SilverWp\Taxonomy\TaxonomyInterface;
use SilverWp\Translate;

/**
 * Abstract Post Type
 *
 * @author Michal Kalkowski <michal at silversite.pl>
 * @version $Id: PostTypeAbstract.php 2319 2015-02-03 09:45:36Z padalec $
 * @category WordPress
 * @package SilverWp
 * @subpackage PostType
 * @copyright (c) 2009 - 2014, SilverSite.pl
 * @tutorial http://blog.teamtreehouse.com/create-your-first-wordpress-custom-post-type
 */
abstract class PostTypeAbstract extends SingletonAbstract implements PostTypeInterface, PostInterface, CoreInterface {
    /**
     *
     * name required
     *
     * @var string
     * @access protected
     */
    protected $name;
    /**
     *
     * @var array
     * @access protected
     */
    protected $supports = array( 'title', 'editor' );
    /**
     *
     *(optional) Whether a post type is intended to be used publicly either
     * via the admin interface or by front-end users.
     * Default: false
     * 'false' - Post type is not intended to be used publicly and should
     *           generally be unavailable in wp-admin and on the front end
     *           unless explicitly planned for elsewhere.
     * 'true' - Post type is intended for public use. This includes on the
     *          front end and in wp-admin.
     *
     * Note: While the default settings of exclude_from_search,
     *       publicly_queryable, show_ui, and show_in_nav_menus are inherited
     *       from public, each does not rely on this relationship and controls
     *       a very specific intention.
     *
     * @var boolean
     * @access protected
     */
    protected $public = true;
    /**
     *
     * (optional) The string to use to build the read, edit,
     * and delete capabilities. May be passed as an array to allow for
     * alternative plurals when using this argument as a base to construct
     * the capabilities, e.g. array('story', 'stories') the first array
     * element will be used for the singular capabilities and the second
     * array element for the plural capabilities, this is instead of the
     * auto generated version if no array is given which would be "storys".
     * By default the capability_type is used as a base to construct
     * capabilities. It seems that `map_meta_cap` needs to be set to true,
     * to make this work.
     * Default: "post"
     * Some of the capability types that can be used (probably not exhaustive list):
     * post (default)
     * page
     * These built-in types cannot be used:
     * attachment
     * mediapage
     *
     * @var mixed string or array
     * @access protected
     */
    protected $capability_type = 'post';
    /**
     *
     * (optional) The position in the menu order the post type should appear.
     * show_in_menu must be true.
     * Default: null - defaults to below Comments
     * 5 - below Posts
     * 10 - below Media
     * 15 - below Links
     * 20 - below Pages
     * 25 - below comments
     * 60 - below first separator
     * 65 - below Plugins
     * 70 - below Users
     * 75 - below Tools
     * 80 - below Settings
     * 100 - below second separator
     *
     * @var int
     * @access protected
     */
    protected $menu_position = null;

    /**
     *
     * (optional) Enables post type archives. Will use $post_type as archive
     * slug by default.
     * Default: false
     * Note: Will generate the proper rewrite rules if rewrite is enabled.
     * Also use rewrite to change the slug used.
     *
     * @var mixed boolean or string
     * @access protected
     */
    protected $has_archive = false;
    /**
     *
     * list of labels
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type section labels
     * @var array
     * @access protected
     */
    protected $labels = array(
        'menu_name'      => '',
        'name'           => '',
        'name_admin_bar' => '',
    );

    /**
     *
     * taxonomy object hendle
     *
     * @var object
     * @access protected
     */
    protected $taxonomy_handler = null;
    /**
     *
     * meta box object handler
     *
     * @var object
     * @access protected
     */
    protected $meta_box_handler = null;

    /**
     *
     * display or not media upload button
     *
     * @var boolean
     * @access protected
     */
    protected $media_button = true;

    /**
     * list of post type templates
     *
     * @var array
     * @access protected
     */
    protected static $page_templates = array();

    /**
     * paginator handler
     *
     * @var object
     * @access private
     */
    private $paginator = null;

    /**
     * image thumbnail size
     *
     * @var mixed string or array
     * @access protected
     */
    protected $thumbnail_size = 'thumbnail';

    /**
     * post id
     *
     * @var integer
     * @access protected
     */
    protected $post_id = null;

    /**
     *
     * Class constructor
     *
     * @access protected
     */
    protected function __construct() {
        //set labels
        $this->setLabels();
        // Thumbnail support for portfolio posts
        add_theme_support( 'post-thumbnails', array( $this->name ) );
        // Adds new post type
        add_action( 'init', array( $this, 'init' ) );
    }

    /**
     *
     * default labels
     *
     * @return array
     */
    private function getDefaultLabels() {

        $labels = array(
            'singular_name'      => Translate::translate( 'Item' ),
            'add_new'            => Translate::translate( 'Add New Item' ),
            'add_new_item'       => Translate::translate( 'Add New Item' ),
            'edit_item'          => Translate::translate( 'Edit Item' ),
            'all_items'          => Translate::translate( 'All Items' ),
            'new_item'           => Translate::translate( 'Add New Item' ),
            'view_item'          => Translate::translate( 'View Item' ),
            'search_items'       => Translate::translate( 'Search' ),
            'not_found'          => Translate::translate( 'No items found' ),
            'not_found_in_trash' => Translate::translate( 'No items found in trash' )
        );

        return $labels;
    }

    /**
     *
     * set labels
     *
     * @abstract
     * @access protected
     */
    abstract protected function setLabels();

    /**
     *
     * add post type template
     *
     * @param mixed $template_name array or string
     *
     * @access public
     */
    public function addTemplates( $template_name ) {
        if ( \is_array( $template_name ) ) {
            self::$page_templates[ $this->name ] = \array_merge( self::$page_templates, $template_name );
        } else {
            self::$page_templates[ $this->name ] = $template_name;
        }
    }

	/**
	 * Get all added templates if post_type isn't null
	 * return all templates directed for post type
	 *
	 * @param null $post_type
	 *
	 * @return array
	 * @static
	 * @access public
	 */
    public static function getTemplates( $post_type = null ) {
        $template = \is_null( $post_type ) ? self::$page_templates : self::$page_templates[ $post_type ];

        return $template;
    }

    /**
     *
     * get post type name
     *
     * @return string
     * @access public
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * set taxonomy class handler
     *
     * @param TaxonomyInterface $taxonomy
     *
     * @return PostTypeAbstract
     * @access public
     */
    public function setTaxonomy( TaxonomyInterface $taxonomy ) {
        $this->taxonomy_handler = $taxonomy;

        return $this;
    }

    /**
     *
     * get taxonomy object hendle
     *
     * @return object
     * @access public
     */
    public function getTaxonomy() {
        return $this->taxonomy_handler;
    }

    /**
     *
     * set meta box hendle class
     *
     * @param MetaBoxInterface $meta_box
     *
     * @return PostTypeAbstract
     * @access public
     */
    public function setMetaBox( MetaBoxInterface $meta_box ) {
        $this->meta_box_handler = $meta_box;

        return $this;
    }

    /**
     * get meta box object handle
     *
     * @return object
     */
    public function getMetaBox() {
        return $this->meta_box_handler;
    }

    /**
     * init post type
     *
     * @access public
     * @throws \SilverWp\PostType\Exception
     * @return void
     */
    public function init() {
        if ( \is_null( $this->name ) ) {
            throw new Exception( Translate::translate( 'Post Type $name is required and can\'t be empty.' ) );
        }
        $this->register();
    }

    /**
     * add new suport too post type
     *
     * @param string $name support name
     *
     * @return void
     * @access public
     */
    public function addSupport( $name ) {
        if ( \in_array( $name, $this->supports ) ) {
            return;
        }
        $this->supports[ ] = $name;
    }

    /**
     * register Post Type
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     * @access protected
     * @return void
     */
    protected function register() {
        $args = array(
            'labels'              => \wp_parse_args( $this->labels, $this->getDefaultLabels() ),
            'public'              => $this->public,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'query_var'           => true,
            'exclude_from_search' => false,
            //'menu_icon'       => '',
            'supports'            => $this->supports,
            'capability_type'     => $this->capability_type,
            'rewrite'             => array(
                'slug'       => $this->name,
                'with_front' => false
            ), // Permalinks format
            'menu_position'       => $this->menu_position,
            'hierarchical'        => false,
            'has_archive'         => $this->has_archive,
        );
        \register_post_type( $this->name, $args );
        \flush_rewrite_rules();
    }

    /**
     * register meta box too Post Type
     *
     * @param MetaBoxInterface $meta_box
     *
     * @access public
     * @return void
     */
    public function registerMetaBox( MetaBoxInterface $meta_box ) {
        try {
            $this->setMetaBox( $meta_box );
            $meta_box->setId( $this->name );
            $meta_box->setPostType( array( $this->name ) );
            $class_name = \get_called_class();
            $meta_box->setPostTypeClass( $class_name::getInstance() );
        } catch ( MetaBoxException $ex ) {
            echo $ex->displayAdminNotice();
        }
    }

    /**
     *
     * Set a taxonomies to Post Type
     *
     * @param TaxonomyInterface $taxonomy
     *
     * @access public
     */
    public function registerTaxonomy( TaxonomyInterface $taxonomy ) {
        try {
            $this->setTaxonomy( $taxonomy );
            $taxonomy->setPostTypeHandler( $this );
        } catch ( TaxonomyException $ex ) {
            echo $ex->displayAdminNotice();
        }
    }


    /**
     *
     * remove media button to upload file from post type
     *
     * @global object $current_screen
     * @link https://trac.nq.pl/wordpress/ticket/60
     * @todo this hook doesn't work
     * @access public
     */
    public function removeMediaButton() {
        global $current_screen;
        // use 'post', 'page' or 'custom-post-type-name'
        if ( $this->name == $current_screen->post_type ) {
            \add_action( 'media_buttons_context', \create_function( '', 'return;' ) );
        }
    }

    /**
     *
     * Add count to "Right Now" Dashboard Widget
     * hook right_now_content_table_end it's seams
     * to by deprecated in WP 3.9 > so this doesn't work
     *
     * @deprecated since 1.8
     * @link http://codex.wordpress.org/Plugin_API/Action_Reference/right_now_content_table_end
     * @access public
     * @return void
     */
    public function addCounts() {
        if ( ! \post_type_exists( $this->name ) ) {
            return;
        }

        $num_posts = \wp_count_posts( $this->name );
        $num       = \number_format_i18n( $num_posts->publish );
        $text      = Translate::n( 'Item', 'Items', \intval( $num_posts->publish ) );
        if ( \current_user_can( 'edit_posts' ) ) {
            $num  = "<a href=\"edit.php?post_type={$this->name}\">$num</a>";
            $text = "<a href=\"edit.php?post_type={$this->name}\">$text</a>";
        }
        echo '<td class="first b b-portfolio">' . $num . '</td>';
        echo '<td class="t portfolio">' . $text . '</td>';
        echo '</tr>';

        if ( $num_posts->pending > 0 ) {
            $num  = \number_format_i18n( $num_posts->pending );
            $text = Translate::n( 'Item Pending', 'Items Pending', \intval( $num_posts->pending ) );
            if ( \current_user_can( 'edit_posts' ) ) {
                $num  = "<a href=\"edit.php?post_status=pending&post_type={$this->name}\">$num</a>";
                $text = "<a href=\"edit.php?post_status=pending&post_type={$this->name}\">$text</a>";
            }
            echo '<td class="first b b-portfolio">' . $num . '</td>';
            echo '<td class="t portfolio">' . $text . '</td>';
            echo '</tr>';
        }
    }

    /**
     * get post type data from data base
     *
     * @param integer $limit if not limit set this variable to 0
     * @param boolean $paginator the post should be paginated or not
     * @param mixed   $thumbnail image thubnail size array or string
     * @param array   $query_args
     *
     * @return array
     * @todo move this method to SilverWp\Db\Query
     * @depraced
     */
    public function getQueryData( $limit = 5, $paginator = false, array $query_args = array() ) {
        $return    = array();
        $like_bool = Option::get_theme_option( $this->getName() . '_list_like' );
        if ( $like_bool === '1' ) {
            $PostLike = PostLike::getInstance();
        }

        $defualt_query_args = array(
            'post_type'        => $this->getName(),
            'orderby'          => 'post_date',
            'order'            => 'DESC',
            'suppress_filters' => false //wpml
        );
        if ( $limit > 0 ) {
            $defualt_query_args[ 'posts_per_page' ] = $limit;
        } else {
            $defualt_query_args[ 'posts_per_page' ] = - 1;
        }
        if ( $paginator ) {
            $paged                         = \get_query_var( 'paged' ) ? \get_query_var( 'paged' ) : 1;
            $defualt_query_args[ 'paged' ] = $paged;
        }
        $query_args = \wp_parse_args( $query_args, $defualt_query_args );
        //change to custome Select class
        $loop = new Query( $query_args );

        if ( $loop->have_posts() ) {

            if ( $paginator ) {
                $Paginator = $this->setPaginator( new Pager() );
                $Paginator->setMaxNumPages( $paged );
                $Paginator->setTotalPosts( $loop->max_num_pages );
            }

            while ( $loop->have_posts() ) {
                $loop->the_post();
                $post_id    = $loop->post->ID;
                $like_count = $like_bool === '1' ? $PostLike->getPostLikeCount( $post_id ) : '';
                //add meta box
                if ( $this->isMetaBoxRegistered() ) {
                    $meta_box_all = $this->getMetaBox()->setPostId( $post_id )->getAll();
                    //Fix Ticket #319
                    if ( ! empty( $meta_box_all ) ) {
                        $mata_box = RecursiveArray::removeEmpty( $meta_box_all );
                    } else {
                        $mata_box = array();
                    }
                } else {
                    $mata_box = array();
                }

                $post_data = array(
                    'ID'   => $post_id,
                    'link' => \get_permalink( $post_id ),
                    'date' => $this->dateFormat( 'date' ),
                    'like' => $like_count,
                    'slug' => $loop->post->post_name,

                );

                if ( $this->isTaxonomyRegistered() && $this->getTaxonomy()->isRegistered( 'category' ) ) {
                    $post_data[ 'category' ] = $this->getTaxonomy()
                                                    ->setPostId( $post_id )
                                                    ->getPostTerms( 'category' );
                }

                if ( $this->isTaxonomyRegistered() && $this->getTaxonomy()->isRegistered( 'tag' ) ) {
                    $post_data[ 'tags' ] = $this->getTaxonomy()
                                                ->setPostId( $post_id )
                                                ->getPostTerms( 'tag' );
                }

                if ( $this->isTitle() ) {
                    $post_data[ 'title' ] = \get_the_title( $post_id );
                }

                if ( $this->isThumbnail( $post_id ) ) {
                    $post_data[ 'image_html' ] = \get_the_post_thumbnail( $post_id, $this->thumbnail_size );// Thumbnail

                    $image_attributes              = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ),
                                                                                  'full' );
                    $post_data[ 'image_full_src' ] = isset( $image_attributes[ 0 ] ) ? $image_attributes[ 0 ] : null;

                } else {
                    $post_data[ 'image_html' ] = null;
                }

                if ( $this->isDescription() ) {
                    $post_data[ 'description' ]       = apply_filters( 'the_content', $loop->post->post_content );
                    $post_data[ 'short_description' ] = get_the_excerpt();
                }
                $return[ ] = \array_merge( $post_data, $mata_box );
            }
        }
        \wp_reset_postdata();

        return $return;
    }

    /**
     * ceck if paginator is set
     *
     * @return boolean true or false
     * @access public
     */
    public function isPaginator() {
        if ( \is_null( $this->paginator ) ) {
            return false;
        }

        return true;
    }

    /**
     *
     * get single item
     *
     * @return array array with all data
     * @access public
     */
    public function getSingleItem() {
        $post_id   = $this->post_id;
        $post_data = UtlArray::object_to_array( \get_post( $post_id ) );

        if ( $this->isMetaBoxRegistered() ) {
            $meta_box_all = $this->getMetaBox()->setPostId( $post_id )->getAll();
            $mata_box     = RecursiveArray::removeEmpty( $meta_box_all );
        } else {
            $mata_box = array();
        }

        if ( $this->isTaxonomyRegistered() && $this->getTaxonomy()->isRegistered( 'category' ) ) {
            $post_data[ 'category' ] = $this->getTaxonomy()->setPostId( $post_id )->getPostTerms( 'category' );
        }

        if ( $this->isTaxonomyRegistered() && $this->getTaxonomy()->isRegistered( 'tag' ) ) {
            $post_data[ 'tags' ] = $this->getTaxonomy()->setPostId( $post_id )->getPostTerms( 'tag' );
        }

        if ( $this->isThumbnail( $post_id ) ) {
            $post_data[ 'image_html' ] = \get_the_post_thumbnail( $post_id, $this->thumbnail_size );// Thumbnail
        }

        $data = \array_merge( $post_data, $mata_box );

        return $data;
    }

    /**
     * check if the taxonowy was registered
     *
     * @return boolean
     * @access public
     */
    public function isTaxonomyRegistered() {
        if ( \is_null( $this->taxonomy_handler ) ) {
            return false;
        }

        return true;
    }

    /**
     * check if the meta box class was registered
     *
     * @return boolean
     * @access public
     */
    public function isMetaBoxRegistered() {
        if ( \is_null( $this->meta_box_handler ) ) {
            return false;
        }

        return true;
    }

    /**
     * check the post type have thumbnail
     *
     * @return boolean
     * @access public
     */
    public function isThumbnail() {
        $post_id   = $this->post_id;
        $thumbnail = \in_array( 'thumbnail', $this->supports ) && \has_post_thumbnail( $post_id );

        return $thumbnail;
    }

    /**
     * check the post type have description
     *
     * @return boolean
     * @access public
     */
    public function isDescription() {
        $editor = \in_array( 'editor', $this->supports );

        return $editor;
    }

    /**
     * chaeck the post type supports title
     *
     * @return boolean
     * @access public
     */
    public function isTitle() {
        $is_title = \in_array( 'title', $this->supports );

        return $is_title;
    }

    /**
     * set post id
     *
     * @param integer $post_id post id
     *
     * @return PostTypeAbstract
     * @access public
     */
    public function setPostId( $post_id ) {
        $this->post_id = $post_id;
        if ( $this->isMetaBoxRegistered() ) {
            $this->getMetaBox()->setPostId( $post_id );
        }
        if ( $this->isTaxonomyRegistered() ) {
            $this->getTaxonomy()->setPostId( $post_id );
        }

        return $this;
    }

    /**
     * get post id
     *
     * @return integer
     * @access public
     */
    public function getPostId() {
        return $this->post_id;
    }

    /**
     *
     * set thumbnail size returned in getQueryMethod
     *
     * @param mixed $thumbnail_size string or array
     *
     * @return \SilverWp\PostType\PostTypeAbstract
     * @access public
     */
    public function setThumbnailSize( $thumbnail_size ) {
        $this->thumbnail_size = $thumbnail_size;

        return $this;
    }

    public function addRelationship( $name, $to = null ) {
        try {
            $connection = new Relationship( $name );
            $connection->setFrom( $this->getName() );
            if ( ! is_null( $to ) ) {
                $connection->setTo( $to );
            }

            return $connection;
        } catch (\SilverWp\Exception $ex ) {
            echo $ex->displayAdminNotice();
        }
    }
}
