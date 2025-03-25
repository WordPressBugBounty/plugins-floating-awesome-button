<?php

namespace Fab\Plugin\Helper;

!defined( 'WPINC ' ) or die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */

trait Option {

    /** Array Merge Recursive */
    public function ArrayMergeRecursive($array1, $array2) {
        $merged = (array) $array1; // Cast both to arrays to handle stdClass objects
        foreach ((array) $array2 as $key => $value) {
            // If both elements are arrays or objects, recursively merge them
            if (is_array($value) || is_object($value)) {
                if (isset($merged[$key]) && (is_array($merged[$key]) || is_object($merged[$key]))) {
                    $merged[$key] = $this->ArrayMergeRecursive($merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
            // For numeric keys, append value if not already in the array
            else if (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            }
            // For non-numeric keys, overwrite or set the value
            else {
                $merged[$key] = $value;
            }
        }

        // Return the merged array, casting back to object if necessary
        return is_object($array1) ? (object) $merged : $merged;
    }

    /**
     * Transform Boolean Value
     *
     * @param mixed $data The data to transform.
     * @return mixed The transformed data.
     */
    public function transformBooleanValue($data){
        if(is_array($data)){
            foreach($data as $key => &$value){
                if( is_array($value) ){ $value = $this->transformBooleanValue( $value ); }
                else { $value = ($value === 1 || $value === true || $value === 'true' || $value === "1") ? 1 : 0; }
            }
        } else { $data = ($data === 1 || $data === true || $data === 'true' || $data === "1") ? 1 : 0; }
        return $data;
    }

    /**
     * Transform Location Value.
     *
     * In version 1.10.0, a transformation was introduced to the location data structure
     * to adapt to database changes. Previously, location data did not include rule groups,
     * but now each rule can belong to a rule group.
     *
     * @since 1.10.0
     * @access public
     *
     * @param array $locations The location array to be transformed.
     * @return array The transformed location array, or the original array if no transformation is needed.
     */
    public function transformLocationValue($locations) {

        if (is_array($locations) && count($locations) > 0) {
            if (!isset($locations[0]['rules'])) {
                // Wrap the data into a grouped format
                $transformed = [
                    [
                        'logic' => 'OR', // Default logic for the group
                        'rules' => $locations
                    ]
                ];
                return $transformed;
            }
        }

        // Return the original data if no transformation is needed
        return $locations;
    }

    /**
     * Check if an array is in another array.
     *
     * @param array $needle The array to search for.
     * @param array $haystack The array to search within.
     * @return bool True if the array is found, false otherwise.
     */
    public function isArrayInArray(array $needle, array $haystack): bool {
        return count(array_intersect($needle, $haystack)) === count($needle);
    }

}
