/*global console,ajaxurl,$,jQuery*/

/**
 *
 */
jQuery(function ($) {
    "use strict";

    if ($('input.mega-setting-responsive_breakpoint').val() == '0px') {
        $('.mega-tab-content-mobile_menu').addClass('mega-mobile-disabled');
    }

    $('input.mega-setting-responsive_breakpoint').on("keyup", function() {
        if ( $(this).val() == '0px' || $(this).val() == '0') {
            $('.mega-tab-content-mobile_menu').addClass('mega-mobile-disabled');
        } else {
            $('.mega-tab-content-mobile_menu').removeClass('mega-mobile-disabled');
        }
    });

    if ($('input[name="settings[disable_mobile_toggle]"]').is(":checked")) {
        $('.mega-tab-content-mobile_menu').addClass('mega-toggle-disabled');
    }

    $('input[name="settings[disable_mobile_toggle]"]').on("change", function() {
        if ( $(this).is(":checked")) {
            $('.mega-tab-content-mobile_menu').addClass('mega-toggle-disabled');
        } else {
            $('.mega-tab-content-mobile_menu').removeClass('mega-toggle-disabled');
        }
    });

    $('select[name$="[effect_mobile]').each(function(index) {
        if ($(this).val() == ( 'slide_left' ) || $(this).val() == ( 'slide_right' ) ) {
            $(this).closest('tr.mega-effect_mobile').addClass('mega-is-offcanvas');
        }
    });

    $('select[name$="[effect_mobile]"]').on("change", function() {
        if ( this.value == 'slide_left' || this.value == 'slide_right') {
            $(this).closest('tr.mega-effect_mobile').addClass('mega-is-offcanvas');
        } else {
            $(this).closest('tr.mega-effect_mobile').removeClass('mega-is-offcanvas');
        }
    });

    $('.menu_settings_menu_locations .mega-enabled input[type="checkbox"]').on("change", function() {
        if ( $(this).is(":checked")) {
            $(this).closest('.mega-location').removeClass('mega-location-disabled').addClass('mega-location-enabled');
        } else {
            $(this).closest('.mega-location').removeClass('mega-location-enabled').addClass('mega-location-disabled');
        }
    });

    if (typeof wp.codeEditor !== 'undefined' && typeof cm_settings !== 'undefined') {
        if ($('#codemirror').length) {
            wp.codeEditor.initialize($('#codemirror'), cm_settings);
        }

        $('[data-tab="mega-tab-content-custom_styling"]').on('click', function() {
            setTimeout( function() {
                $('.mega-tab-content-custom_styling').find('.CodeMirror').each(function(key, value) {
                    value.CodeMirror.refresh();
                });
            }, 160);
        });
    }
    
    $('.mega-color-picker-input').customColorPicker({ 
        defaultColor: '#DDDDDD', 
        showCssVarPalette: false
    });

    $(".mega-copy_color").on('click', function() {
        var from = $(this).prev().find('.mega-color-picker-input').customColorPicker('get');
        var to = $(this).next().find(".mega-color-picker-input");
        to.customColorPicker('set', from);
    })


    $(".confirm").on("click", function() {
        return confirm(megamenu_settings.confirm);
    });

    $('#theme_selector').on('change', function () {
        var url = $(this).val();
        if (url) {
            window.location = url;
        }
        return false;
    });

    $('.mega-location-header').on("click", function(e) {
        if (e.target.nodeName.toLowerCase() != 'a') {
            $(this).parent().toggleClass('mega-closed').toggleClass('mega-open');
            $(this).siblings('.mega-inner').slideToggle();
        }
    });

    $('.icon_dropdown').select2({
      containerCssClass: 'tpx-select2-container select2-container-sm',
      dropdownCssClass: 'tpx-select2-drop',
      minimumResultsForSearch: -1,
      formatResult: function(icon) {
        return '<i class="' + $(icon.element).attr('data-class') + '"></i>';
      },
      formatSelection: function (icon) {
        return '<i class="' + $(icon.element).attr('data-class') + '"></i>';
        }
    });

    $('.mega-tab-content').each(function() {
        if (!$(this).hasClass('mega-tab-content-general')) {
            $(this).hide();
        }
    });

    $('.mega-tab').on("click", function() {
        var selected_tab = $(this);
        selected_tab.siblings().removeClass('nav-tab-active');
        selected_tab.addClass('nav-tab-active');
        var content_to_show = $(this).attr('data-tab');
        $(this).parent().parent().find('.mega-tab-content').hide();
        $(this).parent().parent().find('.' + content_to_show).show();
    });

    $(".theme_editor").on("submit", function(e) {
        e.preventDefault();
        $(".theme_result_message").remove();
        var original_value = $("input#submit").attr('value');
        $("input#submit").addClass('is-busy').attr('value', megamenu_settings.saving + "…");
        var memory_limit_link = $("<a>").attr('href', megamenu_settings.increase_memory_limit_url).html(megamenu_settings.increase_memory_limit_anchor_text);

        $.ajax({
            url:ajaxurl,
            async: true,
            data: $(this).serialize(),
            type: 'POST',
            success: function(message) {
                if (message.success == true) { //Theme saved successfully
                    var success = $("<p>").addClass('saved theme_result_message');
                    var icon = $("<span>").addClass('dashicons dashicons-yes');
                    $('.megamenu_submit .mega_left').append(success.html(icon).append(message.data));
                } else if (message.success == false) { // Errors in scss
                    var error = $("<p>").addClass('fail theme_result_message').html(megamenu_settings.theme_save_error + " ").append(megamenu_settings.theme_save_error_refresh).append("<br /><br />").append(message.data);
                    $('.megamenu_submit').after(error);
                } else {
                    if (message.indexOf("exhausted") >= 0) {
                        var error = $("<p>").addClass('fail theme_result_message').html(megamenu_settings.theme_save_error + " ").append(megamenu_settings.theme_save_error_exhausted + " ").append(megamenu_settings.theme_save_error_memory_limit + " ").append(memory_limit_link).append("<br />").append(message);
                    } else {
                        var error = $("<p>").addClass('fail theme_result_message').html(megamenu_settings.theme_save_error + "<br />").append(message);
                    }
                    $('.megamenu_submit').after(error);
                }
            },
            error: function(message) {
                if(message.status == 500) { // 500 error with no response from server
                    var error = $("<p>").addClass('fail theme_result_message').html(megamenu_settings.theme_save_error_500 + " ").append(megamenu_settings.theme_save_error_memory_limit + " ").append(memory_limit_link);
                } else {
                    if (message.responseText == "-1") { // nonce check failed
                        var error = $("<p>").addClass('fail theme_result_message').html(megamenu_settings.theme_save_error + " " + megamenu_settings.theme_save_error_nonce_failed );
                    }
                }
                $('.megamenu_submit').after(error);

            },
            complete: function() {
                $("input#submit").removeClass('is-busy').attr('value', original_value);
            }
        });

    });


    $(".theme_editor").on("change", function(e) {
        $(".theme_result_message").css('visibility', 'hidden');
    });;

    $('select#mega_css').on("change", function() {
        var select = $(this);
        var selected = $(this).val();
        select.next().children().hide();
        select.next().children('.' + selected).show();
    });

    // validate inputs once the user moves to the next setting
    $( window ).scroll(function() {
        $('.theme_editor input:focus').blur();
    });

    $('form.theme_editor label[data-validation]').each(function() {
        var label = $(this);
        var validation = label.attr('data-validation');
        var error_message = label.siblings( '.mega-validation-message-' + label.attr('class') );
        var input = $('input', label);

        input.on('blur', function() {

            var value = $(this).val();

            if (label.hasClass('mega-flyout_width') && value == 'auto') {
                label.removeClass('mega-error');
                label.siblings( '.mega-validation-message-' + label.attr('class') ).hide();
                return;
            }

            if ( ( validation == 'int' && Math.floor(value) != value )
              || ( validation == 'px' && ! ( value.substr(value.length - 2) == 'px' || value.substr(value.length - 2) == 'em' || value.substr(value.length - 2) == 'vh' || value.substr(value.length - 2) == 'vw' || value.substr(value.length - 2) == 'pt' || value == 'max-content' || value.substr(value.length - 3) == 'rem' || value.substr(value.length - 1) == '%' ) && value != 0 && value != 'normal' && value != 'inherit' )
              || ( validation == 'float' && ! $.isNumeric(value) ) ) {
                label.addClass('mega-error');
                error_message.show();
            } else {
                label.removeClass('mega-error');
                label.siblings( '.mega-validation-message-' + label.attr('class') ).hide();
            }

        });

    });

    $(".mega-accordion-title").on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var title = $(this);
        var content = title.next('.mega-accordion-content');

        if ( content.is(':hidden') ) {
            content.slideDown('fast', function() {
                title.addClass('mega-accordion-open');
            });
        } else {
            content.slideUp('fast', function() {
                title.removeClass('mega-accordion-open');
            });
        }
    });

    $(".mega-ellipsis").on('click', function(e) {
        e.stopPropagation();

        var ellipsis = $(this);

        $(".mega-ellipsis").not(ellipsis).removeClass('mega-ellipsis-open');

        if ( ellipsis.hasClass('mega-ellipsis-open') ) {
            ellipsis.removeClass('mega-ellipsis-open');
        } else {
            ellipsis.addClass('mega-ellipsis-open');
        }
    });

    $(document).on("click", function(e) { // hide menu when clicked away from
        if ( ! $(e.target).closest(".mega-ellipsis").length ) {
            $(".mega-ellipsis").removeClass('mega-ellipsis-open');
        }
    });

    $('.megamenu-edit-theme').on("click", function() {
        var url = $(this).siblings("select").find(":selected").attr('data-url');
        window.location.href = url;
    });
    
});



document.addEventListener('DOMContentLoaded', function() {
    // Select relevant elements from the DOM
    const navWrapper = document.querySelector('.nav-tab-wrapper');
    const tabs = document.querySelectorAll('.mega-tab');
    const slider = document.querySelector('.nav-tab-slider');
    const activeTabContentDisplay = document.getElementById('active-tab-content');

    // Find the initially active tab
    let currentActiveTab = document.querySelector('.mega-tab.nav-tab-active');

    // Function to position the slider under the active tab
    function positionSlider(activeTab) {
        if (!activeTab || !slider || !navWrapper) {
            // If elements are missing, the new CSS :has rule will handle hiding the slider
            // if no tab is active. If an activeTab is expected but missing,
            // slider might not position correctly, but it won't be explicitly hidden by JS here.
            return;
        }

        // Get the dimensions and position of the navigation wrapper and the active tab
        const navWrapperRect = navWrapper.getBoundingClientRect();
        const activeTabRect = activeTab.getBoundingClientRect();

        // Calculate the slider's width and left position
        // The left position is relative to the navWrapper
        slider.style.width = activeTabRect.width + 'px';
        slider.style.left = (activeTabRect.left - navWrapperRect.left) + 'px';

        // Display the data-tab attribute of the active tab (for demonstration)
        if (activeTabContentDisplay) {
            activeTabContentDisplay.textContent = `Current active tab's data-tab: ${activeTab.getAttribute('data-tab')}`;
        }
    }

    // Set the initial position of the slider if a tab is active
    if (currentActiveTab) {
        positionSlider(currentActiveTab);
    }
    // REMOVED: The 'else if' block that explicitly hid the slider using JS.
    // CSS :has() now handles hiding the slider if no tab is initially active.


    // Add click event listeners to each tab
    tabs.forEach(tab => {
        tab.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default anchor link behavior

            // Remove 'nav-tab-active' class from the previously active tab
            if (currentActiveTab) {
                currentActiveTab.classList.remove('nav-tab-active');
            }

            // Add 'nav-tab-active' class to the clicked tab
            this.classList.add('nav-tab-active');
            currentActiveTab = this; // Update the reference to the current active tab

            // Move the slider to the newly active tab
            positionSlider(this);
        });
    });

    // Reposition the slider on window resize to maintain correct alignment
    window.addEventListener('resize', function() {
        if (currentActiveTab) {
            positionSlider(currentActiveTab);
        }
        // If no tab is active during resize, the CSS :has rule keeps the slider hidden.
    });
});