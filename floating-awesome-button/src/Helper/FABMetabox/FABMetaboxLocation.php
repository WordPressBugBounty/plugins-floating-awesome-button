<?php

namespace Fab\Metabox;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */

use Fab\Wordpress\Model\Metabox;

class FABMetaboxLocation extends Metabox {

    /**
     * WP object
     * @var object
     */
    protected $WP;

    /**
     * Params object
     * @var object
     */
    protected $params;

    /** FAB Metabox Operator */
    public static $operator = array(
        array(
            'id'   => '==',
            'text' => 'is equal to',
        ),
        array(
            'id'   => '!=',
            'text' => 'is not equal to',
        ),
    );

    /** FAB Metabox Operator Logic */
    public static $logic = array(
        array(
            'id'   => 'OR',
            'text' => 'OR',
        ),
        array(
            'id'   => 'AND',
            'text' => 'AND',
        ),
    );

    /** $_POST input */
    public static $input = array(
        'fab_location_type'     => array(
            'default'      => '',
            'sub_meta_key' => 'type',
        ),
        'fab_location_operator' => array(
            'default'      => '',
            'sub_meta_key' => 'operator',
        ),
        'fab_location_value'    => array(
            'default'      => '',
            'sub_meta_key' => 'value',
        ),
        'fab_location_logic'    => array(
            'default'      => '',
            'sub_meta_key' => 'logic',
        ),
        'fab_location_cgroup_logic' => array(
            'default'      => '',
            'sub_meta_key' => 'logic',
        ),
    );

    /** FAB Metabox Post Metas */
    public static $post_metas = array(
        'locations' => array( 'meta_key' => 'fab_location' ),
    );

    /** Constructor */
    public function __construct() {
        $plugin   = \Fab\Plugin::getInstance();
        $this->WP = $plugin->getWP();
    }

    /** Sanitize */
    public function sanitize() {
        /** $_POST Data for metabox location */
        $input = self::$input;

        /** Validate Data Type */
        if ( ! is_array( $_POST ) ) {
            return;
        } else {
            $params = $_POST;
        }

        $params['fab_location_cgroup_logic'] = isset($params['fab_location_cgroup_logic']) ? $params['fab_location_cgroup_logic'] : array();
        /** Validate sub Data Type */
        foreach ( $input as $key => $meta ) {
            if ( ! isset( $params[ $key ] ) || ! is_array( $params[ $key ] ) ) {
                return;
            }
        }

        // Initialize the result
        $result = [];
        // Iterate over each group using the keys in $params['fab_location_type']
        $groupKeys = array_keys($params['fab_location_type']);
        foreach ($groupKeys as $groupKey) {
            $group = [
                'logic' => $params['fab_location_cgroup_logic'][$groupKey][0] ?? null,
                'rules' => []
            ];

            // Check if this group has valid rules
            if (isset($params['fab_location_type'][$groupKey]) && is_array($params['fab_location_type'][$groupKey])) {
                $types = $params['fab_location_type'][$groupKey];
                $operators = $params['fab_location_operator'][$groupKey];
                $values = $params['fab_location_value'][$groupKey];
                $logic = $params['fab_location_logic'][$groupKey];

                // Loop through the rules in this group
                for ($j = 0; $j < count($types); $j++) {
                    $group['rules'][] = [
                        'type' => $types[$j] ?? null,
                        'operator' => $operators[$j] ?? null,
                        'value' => $values[$j] ?? null,
                        'logic' => $logic[$j] ?? 'OR',  // Default to 'OR' if logic is not set
                    ];
                }
            }

            // Add the group to the result
            $result[] = $group;
        }

        // Save the final result
        $this->params = $result;

    }

    /** transformData */
    public function setDefaultInput() {
        /** Transform Locations */
        $locations = array();

        if( empty($this->params) ){
            $this->params[self::$post_metas['locations']['meta_key']] = json_encode(array());
            return;
        }

        // Directly iterate through $this->params
        foreach ($this->params as $index => $group) {
            // Ensure the group has a 'rules' key that is an array
            if (isset($group['rules']) && is_array($group['rules'])) {
                // Group rules by logic
                $group_rules = [];
                foreach ($group['rules'] as $rule) {
                    // Check if the necessary fields exist within each rule
                    if (
                        isset($rule['type']) && isset($rule['operator']) &&
                        isset($rule['value']) && isset($rule['logic'])
                    ) {
                        // Add rule to group based on 'logic'
                        $group_rules[] = $rule;
                    }
                }

                // Push the grouped rules and logic into the $locations array
                $locations[] = [
                    'logic' => $group['logic'],  // Keep the logic value ('or' or 'and')
                    'rules' => $group_rules
                ];
            }
        }

        // Remove duplicate entries based on the entire group (rules + logic)
        $duplicate = [];
        foreach ($locations as $location) {
            // Use a JSON-encoded string to check for duplicates (to compare rules and logic together)
            $serializedLocation = json_encode($location);
            $duplicate[$serializedLocation] = $location;  // Store the unique location
        }

        // Convert the unique locations back into a numerically indexed array
        $locations = array_values($duplicate);

        /** Prepare parameters */
        $this->params = array();
        $this->params[self::$post_metas['locations']['meta_key']] = json_encode($locations);
    }

    /** Save data to database */
    public function save() {
        global $post;
        foreach ( $this->params as $key => $value ) {
            if ( $value ) {
                $this->WP->update_post_meta( $post->ID, $key, $value );
            }
        }
    }

}
