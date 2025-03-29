<div class="fab-container" id="<?php echo esc_attr( $notice['id'] ); ?>">
    <div class="<?php echo isset( $notice['class'] ) ? esc_attr( $notice['class'] ) : 'w-full fixed bottom-0 left-0 bg-yellow-500 text-center text-white py-2'; ?>">
        <p><?php echo esc_html( $notice['message'] ); ?></p>
    </div>
</div>
