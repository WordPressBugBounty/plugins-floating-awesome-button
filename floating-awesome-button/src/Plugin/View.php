<?php

namespace Fab;

! defined( 'WPINC ' ) or die;

/**
 * Helper library for Triangle plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */

class View {

    /**
     * Plugin configuration object
     * @var object
     */
    protected $Plugin;

    /**
     * Helper object
     * @var object
     */
    protected $Helper;

    /**
     * WP object
     * @var object
     */
    protected $WP;

    /**
     * Form object
     * @var object
     */
    protected $Form;

    /**
     * Provide page information page_title, menu_title, etc
     *
     * @var     object  $Page   Page object where the view is located
     */
    protected $Page;

    /**
     * @access   protected
     * @var      array    $sections     Lists of view path callback to load
     */
    protected $sections;

    /**
     * @access   protected
     * @var      string    $template        View template callback to load
     */
    protected $template;

    /**
     * View data send from the controller
     *
     * @var     array   $data    View data
     */
    protected $data;

    /**
     * Enable/Disable (Shortcode, etc)
     *
     * @var     array   $options    View options
     */
    protected $options;

    /**
     * View constructor
     *
     * @return void
     */
    public function __construct( $plugin ) {
        $this->Plugin  = $plugin;
        $this->Helper  = $plugin->getHelper();
        $this->Form    = $plugin->getForm();
        $this->WP      = $plugin->getWP();
        $this->data    = array();
        $this->options = array();
    }

    /**
     * View constructor
     *
     * @return void
     */
    public function addData( $data ) {
        foreach ( $data as $key => $value ) {
            $this->data[ $key ] = $value;
        }
    }

    /**
     * Helper to load content
     *
     * @backend
     * @return  content
     */
    public function loadContent( $content, $args = array() ) {
        extract( $this->data );
        $path = json_decode( FAB_PATH );
        require sprintf(
            '%s%s.php',
            $path->view_path,
            str_replace( '.', '/', $content )
        );
    }

    /**
     * Helper to handle data logic within section loop
     * - Slugify
     * - Determine active tab
     * - Determine which content to load
     * - Convert url for url type sections
     */
    public function sectionLoopLogic( $path, $section ) {
        $data            = array();
        $data['slug']    = str_replace( ' ', '', strtolower( $section['name'] ) );
        $data['active']  = isset( $section['active'] ) ? true : false;
        $data['content'] = ( isset( $section['link'] ) && ! $data['active'] ) ? '' : $path; /** Handle url sections type */
        if ( isset( $section['link'] ) && ! strpos( $section['link'], '//' ) ) {
            $data['tab'] = $this->WP->Page->add_query_arg( null, null ) . '&section=' . $section['link'];
            $data['tab'] = json_decode( FAB_PATH )['home_url'] . $data['tab'];
            $data['tab'] = sprintf('<a id="%s" href="%s" target="_blank">%s</a>',
                'tab-'. $data['slug'],
                $section['link'],
                $section['name']
               );
        } elseif ( isset( $section['link'] ) && strpos( $section['link'], '//' ) ) {
            $data['tab'] = sprintf('<a id="%s" href="%s" target="_blank">%s</a>',
                'tab-'. $data['slug'],
                $section['link'],
                $section['name']
            );
        } else {
            $data['tab'] = $section['name'];}
        return $data;
    }

    /**
     * Build view, echo html content
     *
     * @return  void
     */
    public function build() {
        $this->loadContent(
            sprintf(
                'Template/%s',
                esc_attr( $this->template )
            )
        );
    }

    /**
     * @return object
     */
    public function getPage() {
        return $this->Page;
    }

    /**
     * @param object $Page
     */
    public function setPage( $Page ) {
        $this->Page = $Page;
    }

    /**
     * @return array
     */
    public function getSections() {
        return $this->sections;
    }

    /**
     * @param array $sections
     */
    public function setSections( $sections ) {
        $this->sections = $sections;
    }

    /**
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate( $template ) {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData( $data ) {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions( $options ) {
        $this->options = $options;
    }

    /** Static View Render - Used For (Options, Metafields, etc) */
    public static function RenderStatic($path, $data = array()){
        if(!empty($data)) { extract( $data ); }
        require sprintf(
            '%s%s.php',
            json_decode( FAB_PATH )->view_path,
            str_replace( '.', '/', $path )
        );
    }

}
