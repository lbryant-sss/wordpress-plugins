( function( $ ) {
    $( '#contact-form-editor' ).append(
        `<div id="cf7apps-root" class="cf7apps-internal-settings"></div>`
    );

    if ( '1' === cf7appsWrapperObjects.cf7appsRedirectionEnabled ) {
        $( '#form-panel' ).css( 'position', 'relative' ).append(
            `<a  id="cf7apps-target-btn" style="position:absolute; top: 20px;right: 20px;padding: 0 20px;" href="#cf7apps-root" class="components-button cf7apps-btn tertiary-primary">CF7 Apps Settings</a>`
        );

        $( document ).on( 'click', '#cf7apps-target-btn', function( e ) {
            e.preventDefault();

            // target element
            const target = $( $( this ).attr( 'href' ) );

            if ( target.length ) {
                // scroll to the target element
                $( 'html, body' ).animate(
                    {
                        scrollTop: target.offset().top - 100,
                    },
                    'fast'
                );
            }
        } );
    }
} )( jQuery );