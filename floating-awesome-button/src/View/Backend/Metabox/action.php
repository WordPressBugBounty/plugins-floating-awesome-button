<div class="fab-container">
    <div class="flex flex-col gap-2 p-2">
        <a 
            href="<?php echo esc_url( $preview_url ); ?>" 
            target="_blank" 
            style="background-color: #007cba; width: 100%; display: block; text-align: center;"
            class="py-2 text-white"
        >
            <i class="fa fa-eye pr-2"></i>
            <?php esc_html_e( 'Preview', 'floating-awesome-button' ); ?>
        </a>
        <a 
            href="<?php echo esc_url( $clone_url ); ?>" 
            target="_blank" 
            style="background-color: #007cba; width: 100%; display: block; text-align: center;"
            class="py-2 text-white"
        >
            <i class="fa fa-clone pr-2"></i>
            <?php esc_html_e( 'Clone', 'floating-awesome-button' ); ?>
        </a>
    </div>
</div>
