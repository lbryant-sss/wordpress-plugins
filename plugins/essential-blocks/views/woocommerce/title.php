<<?php echo esc_attr( $titleTag ); ?> class="eb-woo-product-title">
    <a href="<?php echo esc_attr( esc_url( get_permalink() ) ); ?>">
        <?php echo wp_kses_post( get_the_title() ); ?>
    </a>
</<?php echo esc_attr( $titleTag ); ?>>