<?php
/**
 * Metabox Location
 *
 * @package FAB
 */
?>

<div id="fab-metabox-locations"></div>
<script type="text/javascript">
    jQuery(function($) {
        window.FAB_METABOX_LOCATION.init();
        jQuery( ".fab-location-rule-group-item" ).sortable({ 
            cancel: "input, .disable-sort" 
        });
    });
</script>