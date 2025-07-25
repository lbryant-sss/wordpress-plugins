window.jQuery(function ($) {

    const APP = window.metaslider.app ? window.metaslider.app.MetaSlider : null

    /**
     * Event listening to media library edits
     */
    var media_library_events = {
        loaded: false,
        /**
         * Attaches listenTo event to the library collection
         *
         * @param modal object wp.media modal
         */
        attach_event: function (modal) {
            var library = modal.state().get('library')
            modal.listenTo(library, 'change', function (model) {
                media_library_events.update_slide_metadata({
                    id: model.get('id'),
                    caption: model.get('caption'),
                    description: model.get('description'),
                    title: model.get('title'),
                    alt: model.get('alt')
                })
            })
        },

        /**
         * Updates slide caption and other metadata when a media is edited in a modal
         *
         * @param object metadata
         */
        update_slide_metadata: function (metadata) {
            var $slides = $('.slide').filter(function (i) {
                return $(this).data('attachment-id') === metadata.id
            })

            var slideIds = $slides.map(function () {
                return this.id.replace('slide-', '')
            })

            // To be picked up by vue components
            $(document).trigger('metaslider/image-meta-updated', [slideIds.toArray(), metadata])

            metadata.title ? $('.title .default', $slides).html(metadata.title) : $('.title .default', $slides).html('&nbsp;')
            metadata.alt ? $('.alt .default', $slides).html(metadata.alt) : $('.alt .default', $slides).html('&nbsp;')
        }
    }

    /**
     * UI for adding a slide. Managed through the WP media upload UI
     * Event managed here.
     */
    var create_slides = window.create_slides = wp.media.frames.file_frame = wp.media({
        multiple: 'add',
        frame: 'post',
        library: {type: 'image'},
    });

    // Remove unwanted image views
    var whiteList = ['insert', 'iframe'];
    var unwanted_media_menu_items = create_slides.states.models.filter(function (view) {
        var title = view.id;

        // Filter through the list and determine which elements to remove
        return !whiteList.filter(function (term) {
            return title.includes(term)
        }).length;
    })
    create_slides.states.remove(unwanted_media_menu_items);

    create_slides.on('insert', function () {

        var slide_ids = [];
        create_slides.state().get('selection').map(function (media) {
            slide_ids.push(media.toJSON().id);
        });

        if (APP) {
            const message = slide_ids.length == 1 ? APP.__('Preparing 1 slide...', 'ml-slider') : APP.__('Preparing %s slides...')
            APP.notifyInfo(
                'metaslider/creating-slides',
                APP.sprintf(message, slide_ids.length),
                true
            )
        }

        // Remove the events for image APIs
        remove_image_apis();

        if(window.location.href.indexOf('metaslider-start') > -1) {
            var slideshow_id = "";
        } else {
            var slideshow_id = window.parent.metaslider_slider_id;
        }

        var data = {
            action: 'create_image_slide',
            slider_id: slideshow_id,
            selection: slide_ids,
            _wpnonce: metaslider.create_slide_nonce
        };

        // TODO: Create micro feedback to the user.
        // TODO: Adding lots of slides locks up the page due to 'resizeSlides' event
        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (xhr, status, error) {
                var err = JSON.parse(xhr.responseText);
                APP && APP.notifyError('metaslider/slide-create-failed', err.data.messages[1]['errors']['create_failed'][0], true)
            },
            success: function (response) {
                if(window.location.href.indexOf('metaslider-start') > -1) {
                    window.location.href = 'admin.php?page=metaslider&id=' + response.data;
                } else {
                    // Mount and render each new slide
                    response.data.forEach(function (slide) {
                        // TODO: Eventually move the creation to the slideshow or slide vue component
                        // TODO: Be careful about the handling of filters (ex. scheduling)
                        var res = window.metaslider.app.Vue.compile(slide['html'])


                        // Mount the slide to the beginning or end of the list
                        const cont_ = (new window.metaslider.app.Vue({
                            render: res.render,
                            staticRenderFns: res.staticRenderFns
                        }).$mount()).$el;

                        if (metaslider.newSlideOrder === 'last') {
                            $('#metaslider-slides-list > tbody').append(cont_);
                        } else {
                            $('#metaslider-slides-list > tbody').prepend(cont_);
                        }
                    })

                    /* Get the last added slide to avoid multiple scrollTo calls 
                     * when adding more than one slide in bulk */
                    var last_item = response.data[response.data.length - 1].slide_id;

                    $([document.documentElement, document.body]).animate({
                        scrollTop: metaslider.newSlideOrder === 'last' ? $("#slide-"+last_item).offset().top : 0
                    }, 2000);

                    // Add timeouts to give some breating room to the notice animations
                    setTimeout(function () {
                        if (APP) {
                            const message = slide_ids.length == 1 ? APP.__('1 slide added successfully', 'ml-slider') : APP.__('%s slides added successfully')
                            APP.notifySuccess(
                                'metaslider/slides-created',
                                APP.sprintf(message, slide_ids.length),
                                true
                            )
                        }
                        setTimeout(function () {
                            APP && APP.triggerEvent('metaslider/save')
                        }, 1000);
                    }, 1000);
                }
            }
        })
    })

    /**
     * Starts to watch the media library for changes
     */
    create_slides.on('attach', function () {
        if (!media_library_events.loaded) {
            media_library_events.attach_event(create_slides)
        }
    })

    create_slides.on('content:activate', function () {
        // Remove filters to don't allow to insert other media type different to images
        $('#media-attachment-filters').remove();
    })

    /**
     * Fire events when the modal is opened
     * Available events: create_slides.on('all', function (e) { console.log(e) })
     */
    // This is also a little "hack-ish" but necessary since we are accessing the UI indirectly
    create_slides.on('open activate uploader:ready', function () {
        // TODO: when converted to vue component make this work for other languages
        $('.media-menu a:contains("Media Library")').remove()
        add_image_apis()

        // Remove unwanted side menu items
        unwanted_media_menu_items.forEach(function (item) {
            $('#menu-item-' + item.id).remove();
        })

        // Remove filters to don't allow to insert other media type different to images
        $('#media-attachment-filters').remove();
    })
    create_slides.on('all', function (){ $('.media-button').text( APP.__('Add to slideshow', 'ml-slider') ); });

    APP && create_slides.on('open', function () {
        APP.notifyInfo('metaslider/add-slide-opening-ui', APP.__('Opening add slide UI...', 'ml-slider'))
    })
    APP && create_slides.on('deactivate close', function () {
        APP.notifyInfo('metaslider/add-slide-closing-ui', APP.__('Closing add slide UI...', 'ml-slider'))
        remove_image_apis()
    })

    /**
     * Handles changing alt and title on SEO tab
     * TODO: refactor to remove this
     */
    $('.metaslider').on('change', '.js-inherit-from-image', function (e) {
        var $this = $(this)
        var $parent = $this.parents('.can-inherit')
        var input = $parent.children('textarea,input[type=text]')
        var default_item = $parent.children('.default')
        if ($this.is(':checked')) {
            $parent.addClass('inherit-from-image')
        } else {
            $parent.removeClass('inherit-from-image')
            input.focus()
            if ('' === input.val()) {
                if (0 === default_item.find('.no-content').length) {
                    input.val(default_item.html())
                }
            }
        }
    })

    /**
     * For changing slide image. Managed through the WP media upload UI
     * Initialized dynamically due to multiple slides.
     */
    var update_slide_frame;

    /**
     * Handles changing an image when edited by the user.
     */
    $('.metaslider').on('click', '.update-image', function (event) {
        event.preventDefault();
        var $this = $(this);
        var current_id = $this.data('attachment-id');

        /**
         * Opens up a media window showing images
         */
        update_slide_frame = window.update_slide_frame = wp.media.frames.file_frame = wp.media({
            title: MetaSlider_Helpers.capitalize(metaslider.update_image),
            library: {type: 'image'},
            button: {
                text: MetaSlider_Helpers.capitalize($this.attr('data-button-text'))
            }
        });

        /**
         * Selects current image
         */
        update_slide_frame.on('open', function () {
            if (current_id) {
                var selection = update_slide_frame.state().get('selection');
                selection.reset([wp.media.attachment(current_id)]);

                // Add various image APIs
                add_image_apis($this.data('slideType'), $this.data('slideId'))
            }
        });

        /**
         * Starts to watch the media library for changes
         */
        update_slide_frame.on('attach', function () {
            if (!media_library_events.loaded) {
                media_library_events.attach_event(update_slide_frame);
            }
        });

        /**
         * Open media modal
         */
        update_slide_frame.open();

        /**
         * Handles changing an image in DB and UI
         */
        update_slide_frame.on('select', function () {
            var selection = update_slide_frame.state().get('selection');
            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                new_image_id = attachment.id;
                selected_item = attachment;
            });

            APP && APP.notifyInfo('metaslider/updating-slide', APP.__('Updating slide...', 'ml-slider'), true)

            // Remove the events for image APIs
            remove_image_apis()

            /**
             * Updates the meta information on the slide
             */
            var data = {
                action: 'update_slide_image',
                _wpnonce: metaslider.update_slide_image_nonce,
                slide_id: $this.data('slideId'),
                slider_id: window.parent.metaslider_slider_id,
                image_id: new_image_id
            };

            $.ajax({
                url: metaslider.ajaxurl,
                data: data,
                type: 'POST',
                error: function (error) {
                    var err = JSON.parse(error.responseText);
                    APP && APP.notifyError('metaslider/slide-update-failed', err.data.message, true)
                },
                success: function (response) {
                    /**
                     * Updates the image on success
                     */
                    var new_image = $('#slide-' + $this.data('slideId') + ' .thumb').find('img');
                    new_image.attr( 
                        'srcset',
                        `${response.data.thumbnail_url_large} 1024w, ${response.data.thumbnail_url_medium} 768w, ${response.data.thumbnail_url_small} 240w`
                    );
                    new_image.attr('src', response.data.thumbnail_url_small);
                    
                    // set new attachment ID
                    var $edited_slide_elms = $('#slide-' + $this.data('slideId') + ', #slide-' + $this.data('slideId') + ' .update-image');
                    $edited_slide_elms.data('attachment-id', selected_item.id);

                    if (response.data.thumbnail_url_small) {
                        $('#slide-' + $this.data('slideId')).trigger('metaslider/attachment/updated', response.data);
                    }

                    // Update metadata to new image
                    media_library_events.update_slide_metadata({
                        id: selected_item.id,
                        caption: selected_item.caption,
                        description: selected_item.description,
                        title: selected_item.title,
                        alt: selected_item.alt
                    })

                    APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Slide updated successfully', 'ml-slider'), true)

                    // TODO: run a function in SlideViewer.vue to replace this
                    $(".metaslider table#metaslider-slides-list").trigger('resizeSlides');
                }
            });
        });

        update_slide_frame.on('close', function () {
            remove_image_apis()
        })
        create_slides.on('close', function () {
            remove_image_apis()
        })

        /**
         * Add back "Update Slide Image" button text and button-primary class
         * after saving image changes or going back from image-edit
         * https://github.com/MetaSlider/metaslider/issues/448
         */
        update_slide_frame.on('all', function () { 
            // Only in library screen
            if(update_slide_frame.state().id === 'library') {
                // Hide left menu (Actions)
                update_slide_frame.$el.addClass('hide-menu');
                // Add back text and class to the button
                update_slide_frame.$el.find('.media-button-select')
                    .text(update_slide_frame.options.button.text)
                    .addClass('button-primary');
            }
        });
    });

    /**
     * Handles duplicating slides
     */
    $('.metaslider').on('click', '.duplicate-slide-image', function (event) {
        event.preventDefault();
        var $this = $(this);
        var data = {
            action: 'duplicate_slide',
            _wpnonce: metaslider.duplicate_slide_nonce,
            slide_id: $this.data('slide-id'),
            slider_id: window.parent.metaslider_slider_id
        };

        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (error) {
                APP && APP.notifyError('metaslider/slide-duplicate-failed', error, true)
            },
            success: function (response) {

                var res = window.metaslider.app.Vue.compile(response.data.html)

                // Mount the slide to the beginning or end of the list
                const cont_ = (new window.metaslider.app.Vue({
                    render: res.render,
                    staticRenderFns: res.staticRenderFns
                }).$mount()).$el;

                if (metaslider.newSlideOrder === 'last') {
                    $('#metaslider-slides-list > tbody').append(cont_);
                } else {
                    $('#metaslider-slides-list > tbody').prepend(cont_);
                }

                //Icon for mobile settings
                show_mobile_icon('slide-' + response.data.slide_id);

                //scroll to new slide
                $([document.documentElement, document.body]).animate({
                    scrollTop: metaslider.newSlideOrder === 'last' ? $("#slide-"+response.data.slide_id).offset().top : 0
                }, 2000);

                // Add timeouts to give some breating room to the notice animations
                setTimeout(function () {
                    setTimeout(function () {
                        APP && APP.triggerEvent('metaslider/save')
                    }, 1000);
                }, 1000);
            }
        });
        
    });

    /**
     * When Carousel mode or Loop continuously changes
     * 
     * @since 3.90
     */
    $('.metaslider').on('change', '.ms-settings-table input[name="settings[autoPlay]"], .ms-settings-table input[name="settings[carouselMode]"], .ms-settings-table input[name="settings[infiniteLoop]"]', function () {
        showHideAutoPlay();
    });

    /**
     * Show/hide Auto play and Play / pause if Carousel mode and Loop continuously are both enabled
     * 
     * @since 3.90
     */
    var showHideAutoPlay = function () {
        var carouselMode = $('.ms-settings-table input[name="settings[carouselMode]"]');
        var infiniteLoop = $('.ms-settings-table input[name="settings[infiniteLoop]"]');
        var autoPlay = $('.ms-settings-table input[name="settings[autoPlay]"]');
        var pausePlay = $('.ms-settings-table input[name="settings[pausePlay]"]');
        var progressBar = $('.ms-settings-table input[name="settings[progressBar]"]');

        if (carouselMode.is(':checked') && infiniteLoop.is(':checked')) {
            // Hide "Auto play" and "Play / pause" if "Carousel mode" AND "Loop carousel continuously" are enabled
            autoPlay.parents('tr').hide();
            pausePlay.parents('tr').hide();
        } else {
            // Show "Auto play" and "Play / pause" if "Carousel mode" OR "Loop carousel continuously" are disabled
            autoPlay.parents('tr').show();
            pausePlay.parents('tr').show();
        }

        var showProgressBar = autoPlay.is(':checked') && (!carouselMode.is(':checked') || !infiniteLoop.is(':checked')) ? true : false;

        progressBar.parents('tr').toggle(showProgressBar);
        $('tr.customizer-slideshow').eq(3).toggle(showProgressBar);
    }
    showHideAutoPlay();

    /**
     * Show/hide Pause/Play Button options
     * 
     * @since 3.96
     * 
     */
    $('.metaslider').on('change', '.ms-settings-table input[name="settings[pausePlay]"], .ms-settings-table input[name="settings[autoPlay]"], .ms-settings-table input[name="settings[showPlayText]"], .ms-settings-table input[name="settings[infiniteLoop]"]', function () {
        showHidePlayButtonOptions();
    });

    var showHidePlayButtonOptions = function () {
        var table = $('.ms-settings-table');
        var pausePlay = table.find('input[name="settings[pausePlay]"]');
        var showPlayText = table.find('input[name="settings[showPlayText]"]');
        var infiniteLoop = table.find('input[name="settings[infiniteLoop]"]');
        var hoverPauseRow = table.find('input[name="settings[hoverPause]"]').closest('tr');
        var playTextRow = table.find('input[name="settings[playText]"]').closest('tr');
        var pauseTextRow = table.find('input[name="settings[pauseText]"]').closest('tr');
        var pausePlayRow = pausePlay.closest('tr');
        var showPlayTextRow = showPlayText.closest('tr');
    
        if (infiniteLoop.is(':checked')) {
            pausePlayRow.add(showPlayTextRow).add(playTextRow).add(pauseTextRow).hide();
        } else {
            pausePlayRow.show();
            showPlayTextRow.toggle(pausePlay.is(':checked'));
            var showText = pausePlay.is(':checked') && showPlayText.is(':checked');
            playTextRow.add(pauseTextRow).toggle(showText);
            hoverPauseRow.toggle(!pausePlay.is(':checked'));
        }
    };
    
    showHidePlayButtonOptions();

    /**
     * When Pause/Play button changes
     * 
     * @since 3.92
     */
    $('.metaslider').on('change', '.ms-settings-table input[name="settings[pausePlay]"]', function () {
        showHideCustomPlayColor();
    });

    /**
     * Show/hide custom color settings for play button
     * 
     * @since 3.92
     */
    var showHideCustomPlayColor = function () {
        var $table = $('.ms-settings-table');
        var pausePlay = $table.find('input[name="settings[pausePlay]"]');
        var customizerRow = $('tr.customizer-play_pause');
    
        var isChecked = pausePlay.is(':checked');
        customizerRow.toggle(isChecked);
    };

    setTimeout(function () {
        showHideCustomPlayColor();
    }, 100);

    /**
     * When Arrows changes
     * 
     * @since 3.92
     */
    $('.metaslider').on('change', '.ms-settings-table select[name="settings[links]"]', function () {
        showHideCustomArrowColor();
    });

    /**
     * Show/hide custom color settings for arrows button
     * 
     * @since 3.92
     */
    var showHideCustomArrowColor = function () {
        var links = $('.ms-settings-table select[name="settings[links]"]').val();
        if (links === 'false') {
            $('tr.customizer-arrows').hide();
        } else {
            $('tr.customizer-arrows').show();
        }
    }

    setTimeout(function () {
        showHideCustomArrowColor();
    }, 100);

    /**
     * When Navigation changes
     * 
     * @since 3.92
     */
    $('.metaslider').on('change', '.ms-settings-table select[name="settings[navigation]"]', function () {
        showHideCustomNavigationColor();
    });

    /**
     * Show/hide custom color settings for navigation
     * 
     * @since 3.92
     */
    var showHideCustomNavigationColor = function () {
        var navigation = $('.ms-settings-table select[name="settings[navigation]"]').val();
        if (navigation === 'true') {
            $('tr.customizer-navigation').show();
        } else {
            $('tr.customizer-navigation').hide();
        }
    }

    setTimeout(function () {
        showHideCustomNavigationColor();
    }, 100);

    /**
     * When Auto play or Loop changes
     * 
     * @since 3.90
     */
    $('.metaslider').on('change', '.ms-settings-table input[name="settings[autoPlay]"], .ms-settings-table select[name="settings[loop]"]', function () {
        adjustLoop();
    });

    /**
     * When Image crop changes, enable/disable fields inside Crop tab
     * 
     * @since 3.93
     */
    $('.metaslider').on('change', '.ms-settings-table select[name="settings[smartCrop]"]', function () {
        var $this = $(this);
        var el_status = $this.val() == 'true' || $this.val() == 'false' ? true : false;

        $("#metaslider-slides-list tr.slide").each(function () {
            var row = $(this);
            if (row.hasClass('image')) {
                var crop_position = row.find('select.crop_position');
                var recrop_image = row.find('button.recrop_image');

                if (!el_status) {
                    crop_position.attr('disabled', 'disabled');
                    recrop_image.attr('disabled', 'disabled');
                } else {
                    crop_position.removeAttr('disabled');
                    recrop_image.removeAttr('disabled');
                }
            }
        });
    });

    /**
     * Add/remove 'Stop on first slide' option for Loop setting
     * 
     * @since 3.90
     */
    var adjustLoop = function () {
        var autoPlay = $('.ms-settings-table input[name="settings[autoPlay]"]');
        var loop = $('.ms-settings-table select[name="settings[loop]"]');

        if (autoPlay.is(':checked')) {
            // Add 'Stop on first slide' option if doesn't exists
            if (loop.find('option[value="stopOnFirst"]').length === 0) {
                loop.append(`<option value="stopOnFirst">${APP.__('Stop on first slide after looping', 'ml-slider')}</option>`);
            }
        } else {
            // Remove 'Stop on first slide' option
            loop.find('option[value="stopOnFirst"]').remove();
        }
    }
    adjustLoop();

    /**
     * When Progress Bar changes
     * 
     * @since 3.94
     */
    $('.metaslider').on('change', '.ms-settings-table input[name="settings[progressBar]"], .ms-settings-table input[name="settings[infiniteLoop]"]', function () {
        showHideCustomProgressBarColor();
    });

    /**
     * When Extra Effect changes
     * 
     * @since 3.99
     */
    $('.metaslider').on('change', '.ms-settings-table input[name="settings[carouselMode]"], .ms-settings-table select[name="settings[effect]"], .ms-settings-table select[name="settings[extra_effect]"]', function () {
        showHideExtraEffect();
    });

    // Make sure to be in sync when selecting another theme
    window.metaslider.app.EventManager.$on("metaslider/theme-updated", function () {
        setTimeout(() => {
            showHideCustomProgressBarColor();
        }, 1000);
    });

    /**
     * Show/hide custom color settings for progress bar
     * 
     * @since 3.92
     */
    var showHideCustomProgressBarColor = function () {
        var carouselMode = $('.ms-settings-table input[name="settings[carouselMode]"]');
        var progressBar = $('.ms-settings-table input[name="settings[progressBar]"]');
        var infiniteLoop = $('.ms-settings-table input[name="settings[infiniteLoop]"]');
        var autoPlay = $('.ms-settings-table input[name="settings[autoPlay]"]');

        var showProgressBar = autoPlay.is(':checked') && (!carouselMode.is(':checked') || !infiniteLoop.is(':checked')) ? true : false;

        progressBar.parents('tr').toggle(showProgressBar);
        $('tr.customizer-slideshow').eq(3).toggle(showProgressBar);
    }
    setTimeout(function () {
        showHideCustomProgressBarColor();
    }, 100);
    
/**
     * Show/hide extra effect
     * 
     * @since 3.99
     */
    var showHideExtraEffect = function () {
        var carouselMode = $('.ms-settings-table input[name="settings[carouselMode]"]');
        var effect = $('.ms-settings-table select[name="settings[effect]"]');
        var extraEffect = $('.ms-settings-table select[name="settings[extra_effect]"]');

        var showExtraEffect = (!carouselMode.is(':checked') && ['fade', 'zooming', 'flip'].includes(effect.val()) ) ? true : false;
        extraEffect.parents('tr').toggle(showExtraEffect);
    }
    setTimeout(function () {
        showHideExtraEffect();
    }, 100);

    /**
     * Add all the image APIs. Add events everytime the modal is open
     * TODO: refactor out hard-coded unsplash (can wait until we add a second service)
     * TODO: right now this replaces the content pane. It might take some time but look for more native integration
     * TODO: It gets a little bit buggy when someone triggers a download and clicks around. Maybe not important.
     */
    var unsplash_api_events = function (event) {
        event.preventDefault()

        // Some things shouldn't happen when we're about to reload
        if (window.metaslider.about_to_reload) {
            return
        }

        // Set this tab as active
        $(this).addClass('active').siblings().removeClass('active')

        // If the image api container exists we don't want to create it again
        if ($('#image-api-container').length) {
            return
        }

        // Move the content and trigger vue to fetch the data
        // Add a container to house the content
        $(this).parents('.media-frame-router').siblings('.media-frame-content').append('<div id="image-api-container"></div>')

        // Add content to the container
        $('#image-api-container').append('<metaslider-external source="unsplash" :slideshow-id="' + window.parent.metaslider_slider_id + '" :slide-id="' + window.metaslider.slide_id + '" slide-type="' + (window.metaslider.slide_type || 'image') + '"></metaslider-external>')

        // Tell our app to render a new component
        $(window).trigger('metaslider/initialize_external_api', {
            'selector': '#image-api-container'
        })

        // Discard these
        delete window.metaslider.slide_id
        delete window.metaslider.slide_type
    }

    var add_image_apis = window.metaslider.add_image_apis = function (slide_type, slide_id) {

        // This is the pro layer screen (not currently used)
        if ($('.media-menu-item.active:contains("Layer")').length) {
            // If this is the layer slide screen and pro isnt installed, exit
            if (!window.metaslider.pro_supports_imports) {
                return
            }
        }
        window.metaslider.slide_type = 'layer'

        // If slide type is set then override the above because we're just updating an image
        if (slide_type) {
            window.metaslider.slide_type = slide_type
        }

        window.metaslider.slide_id = slide_id

        // Unsplash - First remove potentially leftover tabs in case the WP close event doesn't fire
        $('.unsplash-tab').remove()
        $('.media-frame-router .media-router').append('<a href="#" id="unsplash-tab" class="text-black hover:text-blue-dark unsplash-tab media-menu-item">Unsplash Library</a>')
        $('.toplevel_page_metaslider').on('click', '.unsplash-tab', unsplash_api_events)

        // Each API will fake the container, so if we click on a native WP container, we should delete the API container
        $('.media-frame-router .media-router .media-menu-item').on('click', function () {

            // Destroy the component (does clean up)
            $(window).trigger('metaslider/destroy_external_api')

            // Additionally set the active tab
            $(this).addClass('active').siblings().removeClass('active')
        })
    }

    /**
     * Remove tab and events for api type images. Add this when a modal closes to avoid duplicate events
     */
    var remove_image_apis = window.metaslider.remove_image_apis = function () {

        // Some things shouldn't happen when we're about to reload
        if (window.metaslider.about_to_reload) {
            return
        }

        // Tell tell components they are about to be removed
        $(window).trigger('metaslider/destroy_external_api')

        $('.toplevel_page_metaslider').off('click', '.unsplash-tab', unsplash_api_events)
        $('.unsplash-tab').remove()

        // Since we will destroy the container each time we should add the active class to whatever is first
        $('.media-frame-router .media-router > a').first().trigger('click')
    }

    /**
     * delete a slide using ajax (avoid losing changes)
     */
    $(".metaslider").on('click', '.delete-slide', function (event) {
        event.preventDefault();
        var $this = $(this);
        var data = {
            action: 'delete_slide',
            _wpnonce: metaslider.delete_slide_nonce,
            slide_id: $this.data('slideId'),
            slider_id: window.parent.metaslider_slider_id
        };

        // Set the slider state to deleting
        $this.parents('#slide-' + $this.data('slideId'))
            .removeClass('ms-restored')
            .addClass('ms-deleting')
            .append('<div class="ms-delete-overlay"><i style="height:24px;width:24px"><svg class="ms-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg></i></div>');
        $this.parents('#slide-' + $this.data('slideId'))
            .find('.ms-delete-status')
            .remove();

        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (response) {

                // Delete failed. Remove delete state UI
                alert(response.responseJSON.data.message);
                $slide = $this.parents('#slide-' + $this.data('slideId'));
                $slide.removeClass('ms-deleting');
                $slide.find('.ms-delete-overlay').remove();
            },
            success: function (response) {
                var count = 10;

                // Remove deleting state and add a deleted state with restore option
                setTimeout(function () {
                    $slide = $this.parents('#slide-' + $this.data('slideId'));
                    $slide.addClass('ms-deleted')
                        .removeClass('ms-deleting')
                        .find('.metaslider-ui-controls').append(
                        '<button class="undo-delete-slide" title="' + metaslider.restore_language + '" data-slide-id="' + $this.data('slideId') + '">' + metaslider.restore_language + '</button>'
                    );

                    // Grab the image from the slide
                    var img = $slide.find('.thumb').css('background-image')
                        .replace(/^url\(["']?/, '')
                        .replace(/["']?\)$/, '');

                    // If the image is the same as the URL then it's empty (external slide type)
                    img = (window.location.href === img) ? '' : img;

                    // @codingStandardsIgnoreStart
                    // Will be refactored in the the next branch
                    // Send a notice to the user
                    // var notice = new MS_Notification(metaslider.deleted_language, metaslider.click_to_undo_language, img);

                    // Fire the notice and set callback to undo
                    // notice.fire(10000, function() {
                    //     jQuery('#slide-' + $this.data('slideId'))
                    //         .addClass('hide-status')
                    //         .find('.undo-delete-slide').trigger('click');
                    // });
                    // @codingStandardsIgnoreEnd

                    // If the trash link isn't there, add it in (without counter)
                    if ('none' == $('.trashed-slides-cont').css('display')) {
                        $('.trashed-slides-cont').css('display', '');
                    }
                }, 1000);
            }
        });
    });

    /**
     * undelete a slide using ajax (avoid losing changes)
     */
    $(".metaslider").on('click', '.undo-delete-slide, .trash-view-restore', function (event) {
        event.preventDefault();
        var $this = $(this);
        var data = {
            action: 'undelete_slide',
            _wpnonce: metaslider.undelete_slide_nonce,
            slide_id: $this.data('slideId'),
            slider_id: window.parent.metaslider_slider_id
        };

        // Remove undo button
        $('#slide-' + $this.data('slideId')).find('.undo-delete-slide').html('');

        // Set the slider state to deleting
        $this.parents('#slide-' + $this.data('slideId'))
            .removeClass('ms-deleted')
            .addClass('ms-deleting')
            .css('padding-top', '31px')
            .append('<div class="ms-delete-overlay"><i style="height:24px;width:24px"><svg class="ms-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg></i></div>');
        $this.parents('#slide-' + $this.data('slideId'))
            .find('.ms-delete-status')
            .remove();
        $this.parents('#slide-' + $this.data('slideId'))
            .find('.delete-slide')
            .focus();

        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (response) {

                // Undelete failed. Remove delete state UI
                $slide = $this.parents('#slide-' + $this.data('slideId'));
                $slide.removeClass('ms-restoring').addClass('ms-deleted');
                $slide.find('.ms-delete-overlay').remove();

                // If there was a WP error, this should be populated:
                if (response.responseJSON) {
                    alert(response.responseJSON.data.message);
                } else {
                    alert('There was an error with the server and the action could not be completed.');
                }
            },
            success: function (response) {

                // Restore to original state
                $slide = $this.parents('#slide-' + $this.data('slideId'));
                $slide.addClass('ms-restored')
                $slide.removeClass('ms-deleting')
                    .find('.undo-delete-slide, .trash-view-restore').remove();
                $slide.find('.ms-delete-overlay').remove();
                $('#slide-' + $this.data('slideId') + ' h4').after('<span class="ms-delete-status is-success">' + metaslider.restored_language + '</span>');

                // We can try to remove the buton actions too (trashed view)
                $('#slide-' + $this.data('slideId')).find('.row-actions.trash-btns').html('');

                // Grab the image from the slide
                var img = $slide.find('.thumb').css('background-image')
                    .replace(/^url\(["']?/, '')
                    .replace(/["']?\)$/, '');

                // If the image is the same as the URL then it's empty (external slide type)
                img = (window.location.href === img) ? '' : img;

                // @codingStandardsIgnoreStart
                // Will be refactored in the the next branch
                // Send a success notification
                // TODO: fire notification
                // var notice = new MS_Notification(metaslider.restored_language, '', img, 'is-success');

                // Fire the notice
                // notice.fire(5000);
                // @codingStandardsIgnoreEnd
            }
        });
    });

    /**
     * delete a slide permanently using ajax (avoid losing changes)
     */
    $(".metaslider").on('click', '.trash-view-permanent', function (event) {
        event.preventDefault();
        var $this = $(this);
        var data = {
            action: 'permanent_delete_slide',
            _wpnonce: metaslider.permanent_delete_slide_nonce,
            slide_id: $this.data('slideId')
        };

        // Set the slider state to deleting
        $this.parents('#slide-' + $this.data('slideId'))
            .removeClass('ms-restored')
            .addClass('ms-deleting')
            .append('<div class="ms-delete-overlay"><i style="height:24px;width:24px"><svg class="ms-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg></i></div>');
        $this.parents('#slide-' + $this.data('slideId'))
            .find('.ms-delete-status')
            .remove();

        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (response) {
                // Delete failed. Remove delete state UI
                alert(response.responseJSON.data.message);
                $slide = $this.parents('#slide-' + $this.data('slideId'));
                $slide.removeClass('ms-deleting');
                $slide.find('.ms-delete-overlay').remove();
            },
            success: function (response) {
                var count = 10;

                // Remove deleting state and add a deleted state with restore option
                setTimeout(function () {
                    $slide = $this.parents('#slide-' + $this.data('slideId'));
                    $slide.addClass('ms-deleted')
                        .removeClass('ms-deleting')
                        .find('.metaslider-ui-controls').append(
                        '<button class="undo-delete-slide" title="' + metaslider.restore_language + '" data-slide-id="' + $this.data('slideId') + '">' + metaslider.restore_language + '</button>'
                    );

                    // Grab the image from the slide
                    var img = $slide.find('.thumb').css('background-image')
                        .replace(/^url\(["']?/, '')
                        .replace(/["']?\)$/, '');

                    // If the image is the same as the URL then it's empty (external slide type)
                    img = (window.location.href === img) ? '' : img;

                    // If the trash link isn't there, add it in (without counter)
                    if ('none' == $('.restore-slide-link').css('display')) {
                        $('.restore-slide-link').css('display', 'inline');
                    }
                }, 1000);
            }
        });
    });

    /**
     * Resize a single slide image 
     * 
     * @since 3.93
     * 
     * @param object el The input[name='resize_slide_id'] element
     */
    var resize_single_slide_image = function (el) {
        return new Promise(function (resolve, reject) {
            var slideshow_width = $("input.width").val();
            var slideshow_height = $("input.height").val();
            var thumb_width = el.attr("data-width");
            var thumb_height = el.attr("data-height");
            var slide_row = el.closest('tr');
            var crop_changed = slide_row.data('crop_changed');

            // Get the current Image crop size value and data-value attributes
            // The value for data-value is updated after all image resize through cropSlidesTheOldWay()
            var crop_multiply = parseInt($("select.cropMultiply").val());
            var prev_crop_multiply = parseInt($("select.cropMultiply").attr('data-value'));
    
            // Check if resizing is needed
            if (thumb_width != slideshow_width 
                    || thumb_height != slideshow_height 
                    || crop_changed 
                    || crop_multiply !== prev_crop_multiply
                ) {
                el.attr("data-width", slideshow_width);
                el.attr("data-height", slideshow_height);
    
                var data = {
                    action: "resize_image_slide",
                    slider_id: window.parent.metaslider_slider_id,
                    slide_id: el.attr("data-slide_id"),
                    _wpnonce: metaslider.resize_nonce
                };
    
                // AJAX call wrapped in a Promise
                $.ajax({
                    type: "POST",
                    data: data,
                    async: false,
                    cache: false,
                    url: metaslider.ajaxurl,
                    success: function (response) {
                        if (crop_changed) {
                            slide_row.data('crop_changed', false);
                        }
                        if (response.data && response.data.thumbnail_url_small) {
                            el.closest('tr.slide').trigger('metaslider/attachment/updated', response.data);
                        }
                        // Resolve the promise on success
                        resolve(response);
                    },
                    error: function (error) {
                        // Reject the promise on failure
                        reject(error);
                    }
                });
            } else {
                // Resolve immediately if no resizing is needed
                resolve("No resizing needed.");
            }
        });
    };
    
    /**
     * Save crop position for a single slide image 
     * 
     * @since 3.93
     * 
     * @param object el The input[name='resize_slide_id'] element
     */
    var crop_position_single_slide_image = function (el) {
        return new Promise(function (resolve, reject) {
            var slide_id = el.attr("data-slide_id");
            var slide_row = el.closest('tr');
            var crop_position = slide_row.find('.crop_position').val();
    
            if (crop_position.length > 0) {
                var data = {
                    action: "crop_position_image_slide",
                    slide_id: slide_id,
                    crop_position: crop_position,
                    _wpnonce: metaslider.resize_nonce
                };
    
                $.ajax({
                    type: "POST",
                    data: data,
                    async: false,
                    cache: false,
                    url: metaslider.ajaxurl,
                    success: function (response) {
                        slide_row.data('crop_changed', true);
                        // Resolve the promise on success
                        resolve(response);
                    },
                    error: function (error) {
                        // Reject the promise on failure
                        reject(error);
                    }
                });
            } else {
                // Resolve immediately if crop position can't be saved
                resolve("Can't save new crop position.");
            }
        });
    };

    // bind an event to the slides table to update the menu order of each slide
    // TODO: Remove this soon
    $(".metaslider").on('resizeSlides', 'table#metaslider-slides-list', function (event) {
        $("tr.slide input[name='resize_slide_id']", this).each(function () {
            resize_single_slide_image($(this));
        });
    });

    $(".metaslider").on('click', 'tr.slide button.recrop_image', function (event) {
        var recrop_image = $(this);
        var resize_slide_id = recrop_image.closest('tr').find("input[name='resize_slide_id']");
        
        recrop_image.attr('disabled',true);
        
        crop_position_single_slide_image(resize_slide_id)
            .then(function(response) {
                console.log('New crop position saved'); 
                return resize_single_slide_image(resize_slide_id);
            })
            .then(function(response) {
                setTimeout(function () {
                    recrop_image.attr('disabled', false);
                    APP && APP.notifySuccess(
                        'metaslider/slide-updated',
                        APP.__('Crop position saved and image cropped', 'ml-slider'),
                        true
                    );
                }, 1000);
            })
            .catch(function(error) {
                // Handle error from either crop_position_single_slide_image() or resize_single_slide_image()
                console.error('There was an error when saving crop position or cropping the image:', error);

                // Specific error message if the error originated in crop_position_single_slide_image()
                if (error instanceof Error && error.message.includes('crop_position_single_slide_image')) {
                    console.error('There was an error when trying to save new crop position:', error);
                }

                setTimeout(function () {
                    APP && APP.notifyError(
                        'metaslider/slide-update-failed',
                        APP.__('There was an error when saving crop position or cropping the image', 'ml-slider'),
                        true
                    );
                }, 1000);
            });
    });

    /**
     * Hide 'Click the "Add Slide" button to create your slideshow' notice
     * 
     * @since 3.80
     */
    var hideNoSlidesNotice = function () {
        $('#add-first-slide-notice').hide();
    }

    // helptext tooltips
    var addTooltips = function () {
        $('.tipsy-tooltip').tipsy({className: 'msTipsy', live: false, delayIn: 500, html: true, gravity: 'e'});
        $('.tipsy-tooltip-top').tipsy({live: false, delayIn: 500, html: true, gravity: 's'});
        $('.tipsy-tooltip-bottom').tipsy({live: false, delayIn: 500, html: true, gravity: 'n'});
        $('.tipsy-tooltip-bottom-toolbar').tipsy({live: false, delayIn: 500, html: true, gravity: 'n', offset: 2});
    }
    addTooltips();

    // Add tooltips when a new slide (<tr>) is added (to <table>)
    const slidesTable = $('#metaslider-slides-list');
    if (slidesTable.length) {
        const observer = new MutationObserver(
            function (mutationsList, observer) {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        addTooltips();
                        hideNoSlidesNotice();
                    }
                }
            }
        );
    
        const observerConfig = { childList: true, subtree: true };
        observer.observe(slidesTable[0], observerConfig);
    }

    // welcome screen dropdown
    $('#sampleslider-btn').on('click', function () {
        window.location.href = $('#sampleslider-options').val();
    });

    if (window.location.href.indexOf("withcaption") > -1) {
        $("input[value='override']").attr('checked', true).trigger('click');
    }

    $("#quickstart-browse-button").click(function(){
        window.create_slides.open();
    });

    //dashboard search query on pagination
    if($("#slideshows-list").length) {
        if($("#search_slideshow-search-input").length) {
            var search_string = $("#search_slideshow-search-input").val();
            if(search_string != "") {
                $("#slideshows-list .pagination-links a").each(function() {
                    this.href = this.href + "&s=" + search_string;
                });
            }
        }
    }

    /**
     * Hide smooth height setting when image crop is disabled
     *
    **/
    if ($('select[name="settings[smartCrop]"]').val() == 'disabled') {
        $('input[name="settings[smoothHeight]"]').closest('tr').show();
    } else {
        $('input[name="settings[smoothHeight]"]').closest('tr').hide();
    }
    $('select[name="settings[smartCrop]"]').change(function(){
        if ($(this).val() == 'disabled') {
            $('input[name="settings[smoothHeight]"]').closest('tr').show();
        } else {
            $('input[name="settings[smoothHeight]"]').closest('tr').hide();
            $('input[name="settings[smoothHeight]"]').prop( "checked", false );
        }
    });

    /* Dismiss legacy setting notices */
    $(document).on( 'click', '.ml-legacy-notice .notice-dismiss', function() {
        var data = {
            action: 'legacy_notification',
            notif_status: 'hide',
            _wpnonce: metaslider.legacy_notification_nonce
        };
        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (error) {
                console.log('Something went wrong:' +  error);
            },
            success: function (response) {
                console.log(response);
            }
        });
    
    });

    /* Copy to clipboard on Dashboard Page*/
    $('.copy-shortcode').click(function() {
        var textToCopy = $(this).text();
        if (window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy);
        } else {
            var $tempElement = $("<input>");
            $("body").append($tempElement);
            $tempElement.val(textToCopy).select();
            document.execCommand("Copy");
            $tempElement.remove();
        }
        $(this).next('.copy-message').fadeIn().delay(1000).fadeOut();
    });

    /**
     * Fallback after adding a new slide
     * 
     * @since 3.60
     * 
     * @param {object} data The added slide data 
     * 
     * @return void
     */
    var after_adding_slide_success = window.metaslider.after_adding_slide_success = function ( data ) {
        // Mount the slide to the beginning or end of the list
        var table = $(".metaslider table#metaslider-slides-list");

        if (window.metaslider.newSlideOrder === 'last') {
            table.append(data.html);
        } else {
            table.prepend(data.html);
        }

        $('html, body').animate({
            scrollTop: window.metaslider.newSlideOrder === 'last' 
                ? $($('#slide-'+data.slide_id)).offset().top
                : 0
        }, 2000);
        
        var APP = window.metaslider.app.MetaSlider;
        $(".media-modal-close").click();

        // Add timeouts to give some breating room to the notice animations
        setTimeout(function () {
            if (APP) {
                APP.notifySuccess(
                    'metaslider/slides-created',
                    APP.__('1 slide added successfully', 'ml-slider'),
                    true
                )
            }
            setTimeout(function () {
                APP && APP.triggerEvent('metaslider/save')
            }, 1000);
        }, 1000);
    }

    /**
     * Fallback after imporing slides
     * 
     * @since 3.98
     * 
     * @param {object} data The added slide data 
     * 
     * @return void
     */
    var after_importing_slides_success = window.metaslider.after_importing_slides_success = function ( data ) {
        if (!data) {
            console.error('No data found!');
            return;
        }

        var table = $(".metaslider table#metaslider-slides-list");
        
        data.forEach(function(slide) {
            // Mount the slide to the beginning or end of the list
            // Here we may follow an inverted approach due import 
            window.metaslider.newSlideOrder === 'last' 
                ? table.prepend(slide['html'])
                : table.append(slide['html']);
        });

        // Hide loading box
        $('#loading-add-sample-slides-notice').hide();

        var APP = window.metaslider.app.MetaSlider;

        // Add timeouts to give some breating room to the notice animations
        setTimeout(function () {
            if (APP) {
                const message = data.length == 1 ? APP.__('1 slide added successfully', 'ml-slider') : APP.__('%s slides added successfully')
                APP.notifySuccess(
                    'metaslider/slides-created',
                    APP.sprintf(message, data.length),
                    true
                )
            }
            setTimeout(function () {
                APP && APP.triggerEvent('metaslider/save')
            }, 1000);
        }, 1000);
    }

    /* Add mobile icon for slides with existing mobile setting */
    var show_mobile_icon = function (slide_id) {
        var mobile_label = APP && APP.__('Device options are enabled for this slide. Adjust using the Mobile tab.', 'ml-slider');
        var mobile_checkboxes = $('#metaslider-slides-list #'+ slide_id +' .mobile-checkbox:checked');
        var icon = '<span class="mobile_setting_enabled float-left tipsy-tooltip-top" title="'+ mobile_label +'"><span class="inline-block mr-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-smartphone"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg></span></span>';
        var mobile_enabled = $('#metaslider-slides-list #'+ slide_id +' .slide-details .mobile_setting_enabled');
        if (mobile_checkboxes.length > 0) {
            if(mobile_enabled.length == 0) {
                $('#metaslider-slides-list #'+ slide_id +' .slide-details').append(icon);
            }
        } else {
            mobile_enabled.remove();
        }
    };

    $('#metaslider-slides-list > tbody  > tr').each(function() {
        var tr_id = $(this).attr('id');
        show_mobile_icon(tr_id);
    });

    $('.metaslider').on('click', '.mobile-checkbox', function(){
        var slider_id = $(this).attr('name').replace(/[^0-9]/g,'');
        show_mobile_icon('slide-'+slider_id);
    });

    /* Hide the Device Options section when all options are hidden */
    function mobileSectionChecker(){
        if ($('[name="settings[links]"]').val() == 'false' && $('[name="settings[navigation]"]').val() == 'false') {
            $('.highlight.mobileOptions, .empty-row-spacing.mobileOptions').hide();
        } else {
            $('.highlight.mobileOptions, .empty-row-spacing.mobileOptions').show();
        }
    }
    $('[name="settings[navigation]"], [name="settings[links]"]').on('change', function(){
        mobileSectionChecker();
    });
    mobileSectionChecker();

    //thumbnail animation on dashboard page
    $(".slidethumb").each(function() {
        var count = 1; 
        var container = $(this); 
        setInterval(function() {
            count = container.find(":nth-child(" + count + ")").fadeOut().next().length ? count + 1 : 1;
            container.find(":nth-child(" + count + ")").fadeIn();
        }, 2000);
    });

    /* Dashboard modal */
    $(".open-modal").on("click", function () {
        event.preventDefault(); 
        let id = $(this).data("id");
        $("#modal-" + id).fadeIn();
        $("#overlay-" + id).fadeIn();
    });

    $(".close-modal, .modal-overlay").on("click", function () {
        let id = $(this).data("id") || $(this).attr("id").replace("overlay-", "");
        $("#modal-" + id).fadeOut();
        $("#overlay-" + id).fadeOut();
    }); 
    
    /**
     * Hide slide
     */
    // Stop propagation
    $(".metaslider").on('click', 'button.hide-slide input[type=checkbox]', function(e){
        e.stopPropagation();
    });
    // Button click handler
    $(".metaslider").on('click', 'button.hide-slide', function(e) {
        e.stopPropagation();
        $(this).find('input[type=checkbox]').trigger('click');
        $(this).closest('tr.slide').toggleClass('slide-is-hidden', $(this).find('input[type=checkbox]').is(':checked'));
    });

    /**
     * Set Hiden slide class on page load
     */
    $(".metaslider button.hide-slide input[type=checkbox]").each(function(i) {
        $(this).closest('tr.slide').toggleClass('slide-is-hidden', $(this).is(':checked'));
    });
});

/**
 * Various helper functions to use throughout
 */
var MetaSlider_Helpers = {

    /**
     * Various helper functions to use throughout
     *
     * @param  string string A string to capitalise
     * @return string Returns capitalised string
     */
    capitalize: function (string) {
        return string.replace(/\b\w/g, function (l) {
            return l.toUpperCase();
        });
    }
};
