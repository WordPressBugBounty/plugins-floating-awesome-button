<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 * setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class BackendCustomColumns extends Base implements Model_Interface {

    /**
     * Add new column to the posts list.
     *
     * @access public
     *
     * @param array $columns Columns to add new column.
     * @return array $columns New columns.
     */
    public function add_column($columns){
        $new_columns = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $new_columns[$key] = $value;
                $new_columns['clicked'] = __('Clicked', 'floating-awesome-button');
            } else {
                $new_columns[$key] = $value;
            }
        }
        return $new_columns;
    }

    /**
     * Fill the column with data.
     *
     * @access public
     *
     * @param string $column Column name.
     * @param int $post_id Post ID.
     * @return void Echo the data.
     */
    public function fill_column($column, $post_id) {
        if ($column === 'clicked') {
            $clicked = $this->WP->get_post_meta($post_id, 'fab_total_clicked', true) ?: 0;

            echo esc_html($clicked);
        }
    }

    /**
     * Make the column sortable.
     *
     * @access public
     *
     * @param array $columns Columns.
     * @return array The modified columns.
     */
    public function make_column_sortable($columns) {
        $columns['clicked'] = 'clicked';
        return $columns;
    }

    /**
     * Sort the column.
     *
     * @access public
     *
     * @param WP_Query $query Query.
     * @return void Sort the column.
     */
    public function sort_column($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('orderby') === 'clicked') {
            $query->set('meta_key', 'fab_total_clicked');
            $query->set('orderby', 'meta_value_num');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Class.
     *
     * @return void
     */
    public function run() {
        /** @backend - Handle fill value column */
        add_action( sprintf( 'manage_%s_posts_custom_column', FAB_POST_TYPE_NAME ), array($this, 'fill_column'), 10, 2 );

        /** @backend - Handle sort column */
        add_action( 'pre_get_posts', array($this, 'sort_column'), 10, 1 );

        /** @backend - Add column */
        add_filter( sprintf( 'manage_%s_posts_columns', FAB_POST_TYPE_NAME ), array($this, 'add_column'), 10, 1 );

        /** @backend - Handle sortable column */
        add_filter( sprintf( 'manage_edit-%s_sortable_columns', FAB_POST_TYPE_NAME ), array($this, 'make_column_sortable'), 10, 1 );
    }
}
