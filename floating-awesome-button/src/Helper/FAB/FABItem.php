<?php

namespace Fab\Helper;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 * setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */

use Fab\Metabox\FABMetaboxLocation;
use Fab\Metabox\FABMetaboxSetting;
use Fab\Metabox\FABMetaboxDesign;
use Fab\Metabox\FABMetaboxTrigger;
use Fab\Module\FABModuleAuthLogin;
use Fab\Module\FABModuleAuthLogout;
use Fab\Module\FABModuleReadingBar;
use Fab\Module\FABModuleScrollToTop;
use Fab\Module\FABModuleAnchorLink;
use Fab\Module\FABModuleSearch;
use Fab\View;

class FABItem {

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
     * @access   protected
     * @var      int    $ID    ID
     */
    protected $ID;

    /**
     * @access   protected
     * @var      string    $title    Title
     */
    protected $title;

    /**
     * @access   protected
     * @var      string    $no_html_content Content without HTML tags
     */
    protected $no_html_content;

    /**
     * @access   protected
     * @var      string    $raw_content Content
     */
    protected $raw_content;

    /**
     * @access   protected
     * @var      array    $slug    slug
     */
    protected $slug;

    /**
     * @access   protected
     * @var      string    $status    status
     */
    protected $status;

    /**
     * @access   protected
     * @var      array    $icon_class    icon_class
     */
    protected $icon_class;

    /**
     * @access   protected
     * @var      array    $responsive    responsive
     */
    protected $responsive = array();

    /**
     * @access   protected
     * @var      array    $size    design
     */
    protected $size = array();

    /**
     * @access   protected
     * @var      bool    $standalone    standalone
     */
    protected $standalone = false;

    /**
     * @access   protected
     * @var      string    $type    type
     */
    protected $type;

    /**
     * @access   protected
     * @var      string    $link    link
     */
    protected $link = '';

    /**
     * @access   protected
     * @var      string    $linkBehavior    linkBehavior
     */
    protected $linkBehavior = false;

    /**
     * @access   protected
     * @var      string    $hotkey    hotkey
     */
    protected $hotkey;

    /**
     * @access   protected
     * @var      array    $animation    animation
     */
    protected $animation = array();

    /**
     * @access   protected
     * @var      FABModal    $modal    modal
     */
    protected $modal;

    /**
     * @access   protected
     * @var      FABModule    $module    module
     */
    protected $module;

    /**
     * @access   protected
     * @var      array    $trigger    trigger
     */
    protected $trigger = array();

    /**
     * @access   protected
     * @var      array    $template    template
     */
    protected $template = array();

    /**
     * @access   protected
     * @var      array    $tooltip    tooltip
     */
    protected $tooltip = array();

    /**
     * @access   protected
     * @var      array|string    $locations    locations setting
     */
    protected $locations;

    /**
     * @access   protected
     * @var      bool    $to_be_displayed    to be displayed or not
     */
    protected $to_be_displayed;

    /**
     * @access   protected
     * @var      string    $builder    builder (classic, guttenberg, elementor, beaver builder, etc)
     */
    protected $builder;

    /**
     * @access   protected
     * @var      array    $extraOptions    extra options
     */
    protected $extraOptions = array();

    /**
     * @access protected
     * @var array $toast
     */
    protected $toast = array();

    /**
     * @access public
     * @var array $obj
     */
    public $obj = array();

    /** format fab item to send to view
     *
     * @return array [ 'icon_class'=>'','type'=>'','id'=>'',]
     */
    public function __construct( int $ID ) {
        /** Get Plugin Instance */
        $plugin   = \Fab\Plugin::getInstance();
        $this->WP = $plugin->getWP();
        $this->Helper = $plugin->getHelper();
        $options  = $plugin->getConfig()->options;

        /** Construct Class */
        $this->ID              = $ID;
        $this->to_be_displayed = true;
        $this->title           = get_post_field( 'post_title', $this->ID );
        $this->raw_content     = wp_kses_post( get_post_field( 'post_content', $this->ID ) );
        $this->no_html_content = str_replace( array("\n", "\r"), '', strip_tags( wp_kses_post( get_post_field( 'post_content', $this->ID ) ) ) );
        $this->slug            = get_post_field( 'post_name', $this->ID );
        $this->status          = get_post_field( 'post_status', $this->ID );
        $this->modal           = new FABModal($this->ID);
        $this->icon_class      = $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['icon_class']['meta_key'], true );
        $this->icon_class      = $this->getIconClass();
        $this->type            = $this->WP->get_post_meta( $this->ID, FABMetaboxSetting::$post_metas['type']['meta_key'], true );
        $this->hotkey          = $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['hotkey']['meta_key'], true );
        $this->hotkey          = ($this->hotkey==='none') ? '' : $this->hotkey;

        /** Construct Function */
        $this->construct_grabModule();
        $this->construct_nestedAttributes();
        $this->construct_grabLink();
        $this->construct_extraOptions();

        /** Extra Function */
        $this->match(); // Auto Match Location.
        $this->detect_builder(); // Detect content builder
    }

    /** Grab Module */
    public function construct_grabModule(){
        if( 'anchor_link' === $this->type ){ $this->module = new FABModuleAnchorLink(); }
        elseif ( 'auth_login' === $this->type ) { $this->module = new FABModuleAuthLogin(); }
        elseif ( 'auth_logout' === $this->type ){ $this->module = new FABModuleAuthLogout(); }
        elseif( 'readingbar' === $this->type ){ $this->module = new FABModuleReadingBar(); }
        elseif( 'scrolltotop' === $this->type ){ $this->module = new FABModuleScrollToTop(); }
        elseif ( 'search' === $this->type ){ $this->module = new FABModuleSearch(); }
    }

    /** Grab Nested Attributes */
    public function construct_nestedAttributes(){
        /** Size */
        $this->size            = array(
            'type'   => ( $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['size_type']['meta_key'], true ) ) ?
                $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['size_type']['meta_key'], true ) : 'medium',
            'custom' => ( $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['size_custom']['meta_key'], true ) ) ?
                $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['size_custom']['meta_key'], true ) : '',
        );

        /** Animation */
        $default = FABMetaboxDesign::$input['fab_design_animation']['default'];
        $this->animation = $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['animation']['meta_key'], true );
        $this->animation = ( $this->animation ) ? $this->Helper->ArrayMergeRecursive( (array) $default, (array) $this->animation ) : $default;

        /** Responsive */
        $default = FABMetaboxDesign::$input['fab_design_responsive']['default'];
        $this->responsive = $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['responsive']['meta_key'], true );
        $this->responsive = ( $this->responsive ) ? $this->Helper->ArrayMergeRecursive( (array) $default, (array) $this->responsive ) : $default;

        /** Standalone */
        $standalone = ['readingbar', 'scrolltotop'];
        $this->standalone = (in_array($this->type, $standalone)) ? true : false;
        $this->standalone = ($this->standalone===false) ? $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['standalone']['meta_key'], true ) : $this->standalone;

        /** Trigger */
        $default = FABMetaboxTrigger::$input['fab_trigger']['default'];
        $this->trigger = $this->WP->get_post_meta( $this->ID, FABMetaboxTrigger::$post_metas['trigger']['meta_key'], true );
        $this->trigger = ( $this->trigger ) ? $this->Helper->ArrayMergeRecursive( (array) $default, (array) $this->trigger ) : $default;

        /** Template */
        $default = FABMetaboxDesign::$input['fab_design_template']['default'];
        $this->template = $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['template']['meta_key'], true );
        $this->template = ( $this->template ) ? $this->Helper->ArrayMergeRecursive( (array) $default, (array) $this->template ) : $default;

        /** Tooltip */
        $default = FABMetaboxDesign::$input['fab_design_tooltip']['default'];
        $this->tooltip = $this->WP->get_post_meta( $this->ID, FABMetaboxDesign::$post_metas['tooltip']['meta_key'], true );
        $this->tooltip = ( $this->tooltip ) ? $this->Helper->ArrayMergeRecursive( (array) $default, (array) $this->tooltip ) : $default;

        /** Location */
        $this->locations = $this->WP->get_post_meta( $this->ID, FABMetaboxLocation::$post_metas['locations']['meta_key'], true );
        $this->locations = ( $this->locations ) ? json_decode( $this->locations, true ) : array();
        $this->locations = $this->Helper->transformLocationValue( $this->locations );

        /** Toast */
        $default = FABMetaboxSetting::get_input()['fab_setting_toast']['default'];
        $this->toast = $this->WP->get_post_meta( $this->ID, FABMetaboxSetting::$post_metas['toast']['meta_key'], true );
        $this->toast = ( $this->toast ) ? $this->Helper->ArrayMergeRecursive( (array) $default, (array) $this->toast ) : $default;

        do_action('fab_item_data', $this);
    }

    /** Grab Link */
    public function construct_grabLink(){
        if ( $this->type === 'link' || $this->type === 'anchor_link' ) {
            $this->link         = $this->WP->get_post_meta( $this->ID, FABMetaboxSetting::$post_metas['link']['meta_key'], true );
            $this->link         = ( $this->link && is_string( $this->link ) ) ? $this->link : '';
            $this->linkBehavior = $this->WP->get_post_meta( $this->ID, FABMetaboxSetting::$post_metas['link_behavior']['meta_key'], true );
        } elseif ( $this->type === 'auth_logout' ) {
            $this->link = wp_logout_url( home_url() );
        } elseif ( $this->type === 'latest_post_link' ) {
            $post = wp_get_recent_posts(array(
                'numberposts' => '1',
                'post_status' => 'publish'
            ));
            if(isset($post[0])) { $this->link = get_permalink($post[0]['ID']); }
        }
    }

    /** Grab Extra Options */
    public function construct_extraOptions(){
        if($this->type==='print'){
            $this->extraOptions['print'] = array(
                'target' => $this->WP->get_post_meta( $this->ID, FABMetaboxSetting::$post_metas['print_target']['meta_key'], true ),
            );
        }
    }

    /** Match current displayed post by locations setting on cpt fab items
     *
     * @param array $fab_locations post_meta 'fab_locations'
     * @return void
     */
    public function match() {
        /** Grab Data */
        global $post;

        /** Validate */
        if ( ! $this->locations ) {
            return;
        }

        /** Loop location config */
        $validations = array();

        // Iterate over each location configuration.
        foreach ( $this->locations as $location ) {
            // Default logic to 'OR' if not set.
            $condition = array(
                'logic' => isset( $location['logic'] ) ? $location['logic'] : 'OR',
                'rules' => isset( $location['rules'] ) ? $location['rules'] : array(),
            );

            /** Rule Check */
            $condition['passed'] = false;

            $post_id = isset($post->ID) ? $post->ID : 0;

            if( is_plugin_active('woocommerce/woocommerce.php') ){
                if (is_shop()) {
                    $post_id = wc_get_page_id('shop');
                } elseif (is_cart()) {
                    $post_id = wc_get_page_id('cart');
                } elseif (is_checkout()) {
                    $post_id = wc_get_page_id('checkout');
                }
            }

            // Loop through each rule and apply the matching logic.
            foreach ($condition['rules'] as $index => $rule) {
                $current_passed = false; // Default value for the current rule evaluation.

                if (isset($post->post_type) && 'post_type' === $rule['type']) { // Matched by post type.
                    $current_passed = $this->Helper->match_operator_and_value(
                        $rule['operator'], // Operator ==, !=.
                        $post->post_type, // Source Value.
                        $rule['value'] // Compared Value.
                    );
                } elseif ($post_id && strpos($rule['type'], 'taxonomy_') !== false) { // Matched by post taxonomy.
                    $terms = wp_get_post_terms($post_id, str_replace('taxonomy_', '', $rule['type']), array('fields' => 'ids'));
                    $current_passed = $this->Helper->match_operator_and_value(
                        '==', // Operator always == to check taxonomy.
                        in_array($rule['value'], $terms), // Source Value.
                        ($rule['operator'] === '==' ? true : false) // Compared Value.
                    );
                } elseif ($post_id && strpos($rule['type'], 'single_') !== false) { // Matched by post ID, single.
                    $current_passed = $this->Helper->match_operator_and_value(
                        $rule['operator'], // Operator ==, !=.
                        $post_id, // Source Value, Current Post ID.
                        intval($rule['value']) // Compared Value.
                    );
                } elseif ('user_session' === $rule['type'] && $rule['value'] === 'user_session_logged_in') { // Matched by user session.
                    $current_passed = $this->Helper->match_operator_and_value(
                        '==', // Operator always == to check if user is logged in.
                        is_user_logged_in(), // Source Value, check if user is logged in.
                        ($rule['operator'] === '==' ? true : false) // Compared Value.
                    );
                } elseif ('user_role' === $rule['type']) { // Matched by user role.
                    $current_passed = $this->Helper->match_operator_and_value(
                        '==', // Operator always == to check user role.
                        in_array($rule['value'], wp_get_current_user()->roles), // Source Value, Current User Role.
                        ($rule['operator'] === '==' ? true : false) // Compared Value.
                    );
                }

                $current_passed = apply_filters( 'fab_match_rule', $current_passed, $rule);

                // Combine the current result with previous results based on logic.
                if ($index === 0) { // For the first rule, initialize 'passed' with the current result.
                    $condition['passed'] = $current_passed;
                } else { // For subsequent rules, combine using the specified logic.
                    if ($rule['logic'] === 'AND') {
                        $condition['passed'] = $condition['passed'] && $current_passed;
                    } elseif ($rule['logic'] === 'OR') {
                        $condition['passed'] = $condition['passed'] || $current_passed;
                    }
                }

                // If the logic is AND and a rule fails, we can exit early.
                if (!$condition['passed'] && $condition['logic'] === 'AND') {
                    break;
                }
            }

            /** Store Validations */
            $validations[] = $condition;
        }

        /** Validate Logic (OR, AND) */
        $to_be_displayed = $validations[0]['passed'];
        if (count($validations) > 1) {
            for ($i = 1; $i < count($validations); $i++) {
                if ($validations[$i - 1]['logic'] === 'OR') {
                    $to_be_displayed = $to_be_displayed || $validations[$i]['passed'];
                } elseif ($validations[$i - 1]['logic'] === 'AND') {
                    $to_be_displayed = $to_be_displayed && $validations[$i]['passed'];
                }
            }
        }

        $this->to_be_displayed = $to_be_displayed;
    }

    /**
     * Detect content builder
     */
    public function detect_builder() {
        if ( is_plugin_active( 'elementor/elementor.php' ) && \Elementor\Plugin::instance()->documents->get( $this->getID() )->is_built_with_elementor()) {
            // Elementor builder
            $this->builder = 'elementor';
        } else { // Default builder
            $this->builder = 'default';
        }
    }

    /**
     * Render FAB by type
     *
     * @return void
     */
    public function render() {
        if ( 'modal' === $this->type ) {
            $this->render_content();
        } elseif ( 'modal_widget' === $this->type ) {
            $this->render_content();
            $this->render_widget();
        } elseif ( 'widget' === $this->type ) {
            $this->render_widget();
        } elseif( $this->module && method_exists( $this->module, 'render' ) ){
            $this->module->render();
        }
    }

    /**
     * Render FAB Content
     *
     * @return void
     */
    public function render_content( $content = '' ) {
        global $wp_embed;

        /** Render Elementor */
        if ( $this->builder !== 'elementor' ) {
            $content = get_post_field( 'post_content', $this->getID() ); // Get post content.
            $content = $wp_embed->autoembed( do_blocks( $content ) );
            $content = wp_kses_post( $content ); // Esc content.
        }

        /** Output the content */
        View::RenderStatic(
            sprintf('Template/modal/layout/%s',
                isset($this->getModal()->getLayout()['id']) ?
                    $this->getModal()->getLayout()['id'] : 'stacked'
            ),
            array( 'fab_item' => $this, 'content' => $content )
        );
    }

    /**
     * Render FAB Widget
     */
    public function render_widget() {
        dynamic_sidebar( sprintf( 'fab-widget-%s', $this->getSlug() ) );
    }

    /**
     * @return int
     */
    public function getID(): int {
        return $this->ID;
    }

    /**
     * @param int $ID
     */
    public function setID( int $ID ): void {
        $this->ID = $ID;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle( $title ): void {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->no_html_content;
    }

    /**
     * @param string $content
     */
    public function setContent( $content ): void {
        $this->no_html_content = $content;
    }

    /**
     * @return string
     */
    public function getRawContent() {
        return $this->raw_content;
    }

    /**
     * @param string $raw_content
     */
    public function setRawContent( $raw_content ): void {
        $this->raw_content = $raw_content;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus( $status ): void {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @param array $slug
     */
    public function setSlug( $slug ): void {
        $this->slug = $slug;
    }

    /**
     * @return array
     */
    public function getIconClass() {
        /** TODO: OLDCODE must be removed next major version */
        $oldData = $this->WP->get_post_meta( $this->ID, 'fab_setting_icon_class', true );
        if ( $oldData ) {
            $this->WP->update_post_meta( $this->ID, FABMetaboxDesign::$post_metas['icon_class']['meta_key'], $oldData );
        }
        /** TODO: OLDCODE must be removed next major version */

        return ( $this->icon_class ) ? $this->icon_class : 'fas fa-circle';
    }

    /**
     * @param array $icon_class
     */
    public function setIconClass( $icon_class ): void {
        $this->icon_class = $icon_class;
    }

    /**
     * @return array
     */
    public function getResponsive(): array {
        return $this->responsive;
    }

    /**
     * @param array $responsive
     */
    public function setResponsive( array $responsive ): void {
        $this->responsive = $responsive;
    }

    /**
     * @return array
     */
    public function getSize(): array {
        return $this->size;
    }

    /**
     * @param array $size
     */
    public function setSize( array $size ): void {
        $this->size = $size;
    }

    /**
     * @return bool
     */
    public function isStandalone(): bool
    {
        return $this->standalone;
    }

    /**
     * @param bool $standalone
     */
    public function setStandalone(bool $standalone): void
    {
        $this->standalone = $standalone;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType( $type ): void {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink( $link ): void {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getLinkBehavior() {
        return $this->linkBehavior;
    }

    /**
     * @param string $linkBehavior
     */
    public function setLinkBehavior( $linkBehavior ): void {
        $this->linkBehavior = $linkBehavior;
    }

    /**
     * @return string
     */
    public function getHotkey() {
        return $this->hotkey;
    }

    /**
     * @param string $hotkey
     */
    public function setHotkey( $hotkey ): void {
        $this->hotkey = $hotkey;
    }

    /**
     * @return array
     */
    public function getAnimation()
    {
        return $this->animation;
    }

    /**
     * @param array $animation
     */
    public function setAnimation($animation): void
    {
        $this->animation = $animation;
    }

    /**
     * @return FABModal
     */
    public function getModal(): FABModal
    {
        return $this->modal;
    }

    /**
     * @param FABModal $modal
     */
    public function setModal(FABModal $modal): void
    {
        $this->modal = $modal;
    }

    /**
     * @return FABModule
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param FABModule $module
     */
    public function setModule($module): void
    {
        $this->module = $module;
    }

    /**
     * @return array
     */
    public function getTrigger(): array {
        return $this->trigger;
    }

    /**
     * @param array $trigger
     */
    public function setTrigger( array $trigger ): void {
        $this->trigger = $trigger;
    }

    /**
     * @return array
     */
    public function getTemplate(): array
    {
        return $this->template;
    }

    /**
     * @param array $template
     */
    public function setTemplate(array $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * @param array $tooltip
     */
    public function setTooltip($tooltip): void
    {
        $this->tooltip = $tooltip;
    }

    /**
     * @return array
     */
    public function getLocations(): array {
        return $this->locations;
    }

    /**
     * @param array $locations
     */
    public function setLocations( array $locations ): void {
        $this->locations = $locations;
    }

    /**
     * @return bool
     */
    public function isToBeDisplayed(): bool {
        return $this->to_be_displayed;
    }

    /**
     * @param bool $to_be_displayed
     */
    public function setToBeDisplayed( bool $to_be_displayed ): void {
        $this->to_be_displayed = $to_be_displayed;
    }

    /**
     * @return string
     */
    public function getBuilder(): string {
        return $this->builder;
    }

    /**
     * @param string $builder
     */
    public function setBuilder( string $builder ): void {
        $this->builder = $builder;
    }

    /**
     * @return array
     */
    public function getExtraOptions(): array
    {
        return $this->extraOptions;
    }

    /**
     * @param array $extraOptions
     */
    public function setExtraOptions(array $extraOptions): void
    {
        $this->extraOptions = $extraOptions;
    }

    /** Grab All Assigned Variables */
    public function getVars() {
        $data = get_object_vars( $this );
        $data['modal'] = $this->modal->getVars();
        if($this->module) { $data['module'] = $this->module->getVars(); }
        return $data;
    }

    /**
     * Grab All Toasts
     *
     * @return array
     */
    public function getToast(): array
    {
        return $this->toast;
    }

    /**
     * Set Toast Configuration (Partial Update)
     *
     * @param array $toast
     * @return void
     */
    public function setToast(array $toast): void
    {
        $this->toast = array_merge($this->toast, $toast);
    }

}
