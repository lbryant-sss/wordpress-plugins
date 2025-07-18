jQuery( function($) {
    WCML_Troubleshooting = {
        processed: {
          syncVariations: 0,
          syncGallery: 0,
          syncCategories: 0,
          syncStock: 0,
          fixRelationships: 0,
          duplicateTerms: 0,
          syncDeletedMeta: 0
        },
        init: function() {
            $( function() {
                jQuery('#wcml_trbl_run').on('click',function() {
                    var field = jQuery(this);
                    field.prop('disabled', true);
                    jQuery('.wcml_trbl_action .spinner').css('display','inline-block').css('visibility','visible');
                     WCML_Troubleshooting.run_next_troubleshooting_action();
                });
                jQuery('#attr_to_duplicate').on('change', function() {
                  WCML_Troubleshooting.processed.duplicateTerms = 0;
                  WCML_Troubleshooting.updateCounter('#wcml_duplicate_terms', WCML_Troubleshooting.processed.duplicateTerms);
                });
            });
        },
        setItemDoing: function( $checkboxInputId ) {
          var $item = jQuery( $checkboxInputId ).closest( 'li' ).removeClass( 'item-done' );
          $item.find( 'span.doing' ).show()
          $item.find( 'span.done' ).hide();
        },
        setItemDone: function( $checkboxInputId ) {
          jQuery( $checkboxInputId ).prop('checked', false);
          jQuery( $checkboxInputId ).prop('disabled', false);
          var $item = jQuery( $checkboxInputId ).closest( 'li' ).addClass( 'item-done' );
          $item.find( 'span.doing' ).hide();
          $item.find( 'span.done' ).show();
          WCML_Troubleshooting.run_next_troubleshooting_action();
        },
        updateCounter: function( $checkboxInputId, $count ) {
          jQuery( $checkboxInputId )
            .closest( 'li' ).removeClass( 'item-done' )
            .find( 'span.counter' ).show()
            .find( 'span.count' ).html( $count );
        },
        setCounterDone: function( $checkboxInputId ) {
          jQuery( $checkboxInputId ).prop('checked', false);
          jQuery( $checkboxInputId ).prop('disabled', false);
          jQuery( $checkboxInputId ).closest( 'li' ).addClass( 'item-done' );
          WCML_Troubleshooting.run_next_troubleshooting_action();
        },
        setError: function( $checkboxInputId ) {
          jQuery( $checkboxInputId )
            .closest( 'li' )
            .removeClass( 'item-done' )
            .addClass( 'item-error' );
          jQuery('.wcml_trbl_action').hide();
          jQuery('.wcml_trbl_warning.wcml_trbl_error').show();
        },

        sync_variations: function() {
            var $selector = '#wcml_sync_product_variations';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncVariations );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_sync_variations",
                    wcml_nonce: jQuery('#trbl_sync_variations_nonce').val()
                },
                dataType: 'json',
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  WCML_Troubleshooting.processed.syncVariations += response.data.processed;
                  WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncVariations );
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.processed.syncVariations = 0;
                    WCML_Troubleshooting.setCounterDone( $selector );
                  } else {
                    WCML_Troubleshooting.sync_variations();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },

        sync_product_gallery: function() {
            var $selector = '#wcml_sync_gallery_images';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncGallery );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_gallery_images",
                    wcml_nonce: jQuery('#trbl_gallery_images_nonce').val(),
                },
                dataType: 'json',
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  WCML_Troubleshooting.processed.syncGallery += response.data.processed;
                  WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncGallery );
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.processed.syncGallery = 0;
                    WCML_Troubleshooting.setCounterDone( $selector );
                  } else {
                    WCML_Troubleshooting.sync_product_gallery();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },

        sync_product_categories: function() {
            var $selector = '#wcml_sync_categories';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncCategories );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_sync_categories",
                    wcml_nonce: jQuery('#trbl_sync_categories_nonce').val(),
                },
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  WCML_Troubleshooting.processed.syncCategories += response.data.processed;
                  WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncCategories );
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.processed.syncCategories = 0;
                    WCML_Troubleshooting.setCounterDone( $selector );
                  } else {
                    WCML_Troubleshooting.sync_product_categories();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },

        sync_stock: function() {
            var $selector = '#wcml_sync_stock';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.updateCounter($selector , WCML_Troubleshooting.processed.syncStock );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_sync_stock",
                    wcml_nonce: jQuery('#trbl_sync_stock_nonce').val()
                },
                dataType: 'json',
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  WCML_Troubleshooting.processed.syncStock += response.data.processed;
                  WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncStock );
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.processed.syncStock = 0;
                    WCML_Troubleshooting.setCounterDone( $selector );
                  } else {
                    WCML_Troubleshooting.sync_stock();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },

        fix_translated_variations_relationships: function() {
            var $selector = '#wcml_fix_relationships';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.fixRelationships );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "fix_translated_variations_relationships",
                    wcml_nonce: jQuery('#fix_relationships_nonce').val()
                },
                dataType: 'json',
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  WCML_Troubleshooting.processed.fixRelationships += response.data.processed;
                  WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.fixRelationships );
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.processed.fixRelationships = 0;
                    WCML_Troubleshooting.setCounterDone( $selector );
                  } else {
                    WCML_Troubleshooting.fix_translated_variations_relationships();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },
        
        fix_product_type_terms: function() {
            var $selector = '#wcml_translate_product_type';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.setItemDoing( $selector );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_fix_product_type_terms",
                    wcml_nonce: jQuery('#trbl_product_type_terms_nonce').val()
                },
                dataType: 'json',
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.setItemDone( $selector );
                  } else {
                    WCML_Troubleshooting.fix_product_type_terms();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },
        
        duplicate_terms: function() {
            var $selector = '#wcml_duplicate_terms';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.duplicateTerms );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "trbl_duplicate_terms",
                    wcml_nonce: jQuery('#trbl_duplicate_terms_nonce').val(),
                    attr: jQuery('#attr_to_duplicate option:selected').val()
                },
                dataType: 'json',
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  WCML_Troubleshooting.processed.duplicateTerms += response.data.processed;
                  WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.duplicateTerms );
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.processed.duplicateTerms = 0;
                    WCML_Troubleshooting.setCounterDone( $selector );
                  } else {
                    WCML_Troubleshooting.duplicate_terms();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },
        
        register_reviews_in_st: function() {
            var $selector = '#register_reviews_in_st';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.setItemDoing( $selector );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "register_reviews_in_st",
                    wcml_nonce: jQuery('#register_reviews_in_st_nonce').val(),
                    page: jQuery('#register_reviews_in_st_page').val()
                },
                dataType: 'json',
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.setItemDone( $selector );
                  } else {
                    WCML_Troubleshooting.register_reviews_in_st();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },

        sync_deleted_meta: function() {
            var $selector = '#wcml_sync_deleted_meta';
            jQuery( $selector ).prop('disabled', true);
            WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncDeletedMeta );
            jQuery.ajax({
                type : "post",
                url : ajaxurl,
                data : {
                    action: "sync_deleted_meta",
                    wcml_nonce: jQuery('#sync_deleted_meta_nonce').val()
                },
                dataType: 'json',
                success: function(response) {
                  if ( ! response.success ) {
                    WCML_Troubleshooting.setError( $selector );
                    return;
                  }
                  WCML_Troubleshooting.processed.syncDeletedMeta += response.data.processed;
                  WCML_Troubleshooting.updateCounter( $selector, WCML_Troubleshooting.processed.syncDeletedMeta );
                  if ( response.data.complete ) {
                    WCML_Troubleshooting.processed.syncDeletedMeta = 0;
                    WCML_Troubleshooting.setCounterDone( $selector );
                  } else {
                    WCML_Troubleshooting.sync_deleted_meta();
                  }
                },
                error: function() {
                  WCML_Troubleshooting.setError( $selector );
                }
            });
        },

        run_next_troubleshooting_action: function() {
           if ( jQuery('#wcml_sync_product_variations').is(':checked') ) {
                WCML_Troubleshooting.sync_variations();
           } else if ( jQuery('#wcml_sync_gallery_images').is(':checked') ) {
               WCML_Troubleshooting.sync_product_gallery();
           } else if ( jQuery('#wcml_sync_categories').is(':checked') ) {
                WCML_Troubleshooting.sync_product_categories();
           } else if ( jQuery('#wcml_sync_stock').is(':checked') ) {
                WCML_Troubleshooting.sync_stock();
           } else if ( jQuery('#wcml_fix_relationships').is(':checked') ) {
                WCML_Troubleshooting.fix_translated_variations_relationships();
           } else if ( jQuery('#wcml_translate_product_type').is(':checked') ) {
               WCML_Troubleshooting.fix_product_type_terms();
           } else if ( jQuery('#wcml_duplicate_terms').is(':checked') ) {
                WCML_Troubleshooting.duplicate_terms();
           } else if ( jQuery('#register_reviews_in_st').is(':checked') ) {
               WCML_Troubleshooting.register_reviews_in_st();
           } else if ( jQuery('#wcml_sync_deleted_meta').is(':checked') ) {
                WCML_Troubleshooting.sync_deleted_meta();
           } else {
                jQuery('#wcml_trbl_run').prop('disabled', false);
                jQuery('.wcml_trbl_action .spinner').hide();
            }
        }

    }

    WCML_Troubleshooting.init();

});


