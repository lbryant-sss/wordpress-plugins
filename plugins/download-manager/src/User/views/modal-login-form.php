<?php use WPDM\__\__;
use WPDM\__\Session;

if(!defined('ABSPATH')) die(); ?>
<div class="w3eden">

<!-- Modal Login Form -->
<div class="modal fade" id="wpdmloginmodal" tabindex="-1" role="dialog" aria-labelledby="wpdmloginmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
<button type="button" class="close" id="clmb" data-dismiss="modal" aria-label="Close">
    ❌
</button>
            <div class="modal-body">

                        <div class="text-center wpdmlogin-logo">
                            <a href="<?php echo home_url('/'); ?>" id="wpdm_modal_login_logo"></a>
                        </div>

                    <?php do_action("wpdm_before_login_form"); ?>


                    <form name="loginform" id="modalloginform" action="" method="post" class="login-form" >

                        <input type="hidden" name="permalink" value="<?php the_permalink(); ?>" />

                        <?php echo WPDM()->user->login->formFields(); ?>

                        <?php  if(isset($params['note_after'])) {  ?>
                            <div class="alert alert-info alter-note-after mb-3" >
                                <?php echo $params['note_after']; ?>
                            </div>
                        <?php } ?>

                        <?php do_action("wpdm_login_form"); ?>
                        <?php do_action("login_form"); ?>

                        <div class="row login-form-meta-text text-muted mb-3" style="font-size: 10px">
                            <div class="col-5"><label><input class="wpdm-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /><?php _e( "Remember Me" , "download-manager" ); ?></label></div>
                            <div class="col-7 text-right"><label><a class="color-blue" href="<?php echo wpdm_lostpassword_url(); ?>"><?php _e( "Forgot Password?" , "download-manager" ); ?></a>&nbsp;</label></div>
                        </div>



                        <input type="hidden" name="redirect_to" id="wpdm_modal_login_redirect_to" value="<?php echo __::valueof($_SERVER, 'REQUEST_URI', ['validate' => 'escs']); ?>" />

                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" name="wp-submit" id="wpdmloginmodal-submit" class="btn btn-block btn-primary btn-lg"><i class="fas fa-user-shield"></i> &nbsp;<?php _e( "Login" , "download-manager" ); ?></button>
                            </div>
                        </div>

                    </form>



                    <?php do_action("wpdm_after_login_form"); ?>

            </div>
            <?php
            $__wpdm_social_login = get_option('__wpdm_social_login');
            $__wpdm_social_login = is_array($__wpdm_social_login)?$__wpdm_social_login:array();
            if(is_array($__wpdm_social_login) && count($__wpdm_social_login) > 1) { ?>

            <div class="modal-footer text-center bg-light p-4">
                <div style="display: block;width: 100%;">
                    <div class="pb-2 text-center d-block" style="width: 100%"><?php _e("Or connect using your social account", "download-manager") ?></div>
                    <div class="text-center d-block" style="width: 100%;padding-top: 10px">
		                <?php if(isset($__wpdm_social_login['facebook'])){ ?><button type="button" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=facebook'); ?>', 'Facebook', 400,400);" title="Facebook" class="btn btn-secondary wpdm-facebook wpdm-facebook-connect">F</button><?php } ?>
		                <?php if(isset($__wpdm_social_login['twitter'])){ ?><button type="button" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=twitter'); ?>', 'Twitter', 400,400);" title="X" class="btn btn-secondary wpdm-twitter wpdm-linkedin-connect">X</button><?php } ?>
		                <?php if(isset($__wpdm_social_login['linkedin'])){ ?><button type="button" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=linkedin'); ?>', 'LinkedIn', 400,400);" title="LinkedIn" class="btn btn-secondary wpdm-linkedin wpdm-twitter-connect">In</button><?php } ?>
		                <?php if(isset($__wpdm_social_login['google'])){ ?><button type="button" onclick="return _PopupCenter('<?php echo home_url('/?sociallogin=google'); ?>', 'Google', 400,400);" title="Google" class="btn btn-secondary wpdm-google-plus wpdm-google-connect">G</button><?php } ?>
                    </div>
                </div>
            </div>

            <?php } ?>
            <?php if(isset($regurl) && $regurl != ''){ ?>
                <div class="modal-footer text-center"><a href="<?php echo $regurl; ?>" class="btn btn-block btn-link btn-xs wpdm-reg-link  color-primary"><?php _e( "Don't have an account yet?" , "download-manager" ); ?> <i class="fas fa-user-plus"></i> <?php _e( "Register Now" , "download-manager" ); ?></a></div>
            <?php } ?>
        </div>
    </div>
</div>

</div>

<script>
    jQuery(function ($) {
        var llbl = $('#wpdmloginmodal-submit').html();
        var __lm_redirect_to = location.href;
        var __lm_logo = "<?php echo get_site_icon_url(); ?>";
        var $body = $('body');
        $('#modalloginform').submit(function () {
            $('#wpdmloginmodal-submit').html(WPDM.html("i", "", "fa fa-spin fa-sync") + " <?php _e( "Logging In..." , "download-manager" ); ?>");
            $(this).ajaxSubmit({
                error: function(error) {
                    $('#modalloginform').prepend(WPDM.html("div", error.responseJSON.messages, "alert alert-danger"));
                    $('#wpdmloginmodal-submit').html(llbl);
                    <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                    try {
                        grecaptcha.reset();
                    } catch (e) {

                    }
                    <?php } ?>
                },
                success: function (res) {
                    if (!res.success) {
                        $('form .alert-danger').hide();
                        $('#modalloginform').prepend(WPDM.html("div", res.message, "alert alert-danger"));
                        $('#wpdmloginmodal-submit').html(llbl);
                        <?php if((int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_site_key') != ''){ ?>
                        try {
                            grecaptcha.reset();
                        } catch (e) {

                        }
                        <?php } ?>
                    } else {
                        $('#wpdmloginmodal-submit').html(wpdm_js.spinner+" "+res.message);
                        location.href = __lm_redirect_to;
                    }
                }
            });
            return false;
        });

        $body.on('click', 'form .alert-danger', function(){
            $(this).slideUp();
        });

        $body.on('click', 'a[data-target="#wpdmloginmodal"], .wpdmloginmodal-trigger', function (e) {
            e.preventDefault();
            if($(this).data('redirect') !== undefined) {
                __lm_redirect_to = $(this).data('redirect');
                console.log(__lm_redirect_to);
            }
            if($(this).data('logo') !== undefined) {
                __lm_logo = $(this).data('logo');
            }

            $('#wpdm_modal_login_logo').html(WPDM.el('img', {src: __lm_logo, alt: "Logo"}));
            $('#wpdmloginmodal').modal('show');
        });
        $('#wpdmloginmodal').on('shown.bs.modal', function (event) {
            var trigger = $(event.relatedTarget);
            console.log(trigger.data('redirect'));
            if(trigger.data('redirect') !== undefined) {
                __lm_redirect_to = trigger.data('redirect');
                console.log(__lm_redirect_to);
            }
            if($(this).data('logo') !== undefined) {
                __lm_logo = $(this).data('logo');
            }
            if(__lm_logo !== "")
                $('#wpdm_modal_login_logo').html(WPDM.el('img', {src: __lm_logo, alt: "Logo"}));

            $('#user_login').trigger('focus')
        });
        $(window).keydown(function(event) {
            if(event.ctrlKey && event.keyCode === 76) {

                $('#wpdmloginmodal').modal('show');
                /*console.log("Hey! Ctrl + "+event.keyCode);*/
                event.preventDefault();
            }
        });

    });
</script>
<style>
    #clmb{
        z-index: 999999;position: absolute;right: 10px;top:10px;font-size: 10px;border-radius: 500px;background: #eee;width: 28px;height: 28px;line-height: 28px;text-align: center;opacity:0.4;
        transition: all 0.3s ease-in-out;
        filter: grayscale(100%);
    }
    #clmb:hover{
        opacity: 1;
        filter: grayscale(0%);
    }

    #wpdmloginmodal .modal-content{
        border: 0;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }
    #wpdmloginmodal .modal-dialog{
        width: 380px;
    }
    #wpdmloginmodal .modal-dialog .modal-body{
        padding: 40px;
    }
    .w3eden .card.card-social-login .card-header{
        font-size: 11px !important;
    }
    #wpdmloginmodal-submit{
        font-size: 12px;
    }
    @media (max-width: 500px) {
        #wpdmloginmodal{
            z-index: 999999999;
        }
        #wpdmloginmodal .modal-dialog {
            width: 90%;
            margin: 5% auto;
        }
    }
</style>
