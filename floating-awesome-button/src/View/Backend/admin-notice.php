<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

	$dismiss_text = fs_text_x_inline( 'Dismiss', 'as close a window', 'dismiss' );

	$slug      = '';
	$data_type = '';

if ( ! empty( $manager_id ) ) {
	$slug      = $manager_id;
	$data_type = WP_FS__MODULE_TYPE_PLUGIN;

	if ( false !== strpos( $slug, ':' ) ) {
		$parts = explode( ':', $slug );

		$slug        = $parts[0];
		$parts_count = count( $parts );

		if ( 1 < $parts_count && WP_FS__MODULE_TYPE_THEME === $parts[1] ) {
			$data_type = $parts[1];
		}
	}
}

	$attributes = array();
if ( ! empty( $id ) ) {
	$attributes['data-id'] = $id;
}
if ( ! empty( $manager_id ) ) {
	$attributes['data-manager-id'] = $manager_id;
}
if ( ! empty( $slug ) ) {
	$attributes['data-slug'] = $slug;
}
if ( ! empty( $data_type ) ) {
	$attributes['data-type'] = $data_type;
}

	$classes = array( 'fs-notice' );
switch ( $type ) {
	case 'error':
		$classes[] = 'error';
		$classes[] = 'form-invalid';
		break;
	case 'promotion':
		$classes[] = 'updated';
		$classes[] = 'promotion';
		break;
	case 'warn':
		$classes[] = 'notice';
		$classes[] = 'notice-warning';
		break;
	case 'update':
	case 'success':
	default:
		$classes[] = 'updated';
		$classes[] = 'success';
		break;
}

if ( ! empty( $sticky ) ) {
	$classes[] = 'fs-sticky';
}
if ( ! empty( $plugin ) ) {
	$classes[] = 'fs-has-title';
}
if ( ! empty( $slug ) ) {
	$classes[] = "fs-slug-{$slug}";
}
if ( ! empty( $type ) ) {
	$classes[] = "fs-type-{$type}";
}
?>
    <div class="fab-container <?php  echo fs_html_get_classname($classes); // phpcs:ignore ?>" <?php echo fs_html_get_attributes($attributes); ?>> 
		<?php if ( ! empty( $plugin ) ) : ?>
			<div class="fab-admin-notice-header-container">
                <img class="fab-admin-notice-logo" src="<?php echo json_decode( FAB_PATH )->plugin_url . '/assets/img/icon.png' // phpcs:ignore?>" alt="">
				<label class="fab-admin-notice-title"><?php echo esc_html( strtoupper( $plugin ) ); ?></label>
				<?php if ( isset( $suffix_title ) ) : ?>
					<label class="fab-admin-notice-suffix-title"> <?php echo esc_html( strtoupper( $suffix_title ) ); ?> </label>
				<?php endif ?>
			</div>
		<?php endif ?>
	
		<?php if ( ! empty( $sticky ) && ( ! isset( $dismissible ) || false !== $dismissible ) ) : ?>
			<div class="fs-close">
				<i class="dashicons dashicons-no" title="<?php echo esc_attr( $dismiss_text ); ?>"></i>
				<span><?php echo esc_html( $dismiss_text ); ?></span>
			</div>
		<?php endif ?>
	
		<div class="fs-notice-body">
			<?php if ( ! empty( $title ) ) : ?>
                <strong><?php echo fs_html_get_sanitized_html($title); // phpcs:ignore?></strong>
			<?php endif ?>
	
            <?php echo fs_html_get_sanitized_html($message); // phpcs:ignore?>
		</div>

		<?php if ( ! empty( $buttons ) ) : ?>
			<div class="fs-notice-button">
				<?php foreach ( $buttons as $button ) : ?>
					<?php if ( isset( $button['url'] ) ) : ?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" class="<?php echo esc_attr( $button['classes'] ); ?>">
						<?php echo esc_html( $button['message'] ); ?>
					</a>
					<?php elseif ( isset( $button['id'] ) ) : ?>
					<button id="<?php echo esc_html( $button['id'] ); ?>" data-value="<?php echo esc_html( $button['value'] ); ?>" class="fab-fs-button <?php echo esc_attr( $button['classes'] ); ?>">
						<?php echo esc_html( $button['message'] ); ?>
					</button>
					<?php endif ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>


<script type="text/javascript" >
jQuery( document ).ready(function( $ ) {
	$( '.fs-notice.fs-sticky .fab-fs-button' ).click(function() {
		var
			notice           = $( this ).parents( '.fs-notice' ),
			id               = notice.attr( 'data-id' ),
			value            = $( this ).attr( 'data-value' );
			ajaxActionSuffix = notice.attr( 'data-manager-id' ).replace( ':', '-' );

		notice.fadeOut( 'fast', function() {
			var data = {
				action   : 'fs_dismiss_notice_action_' + ajaxActionSuffix,
				// As such we don't need to use `wp_json_encode` method but using it to follow wp.org guideline.
				_wpnonce : <?php echo wp_json_encode( wp_create_nonce( 'fs_dismiss_notice_action' ) ); ?>,
				message_id: id,
				value: value,
			};

			$.post( <?php echo Freemius::ajax_url(); ?>, data, function( response ) {

			});

			notice.remove();
		});
	});
});
</script>