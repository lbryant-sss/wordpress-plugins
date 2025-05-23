<?php
/* @var $this NewsletterEmailsAdmin */
/* @var $controls NewsletterControls */
/* @var $logger NewsletterLogger */

defined('ABSPATH') || exit;

$email_id = (int) $_GET['id'];

if ($controls->is_action('save') || $controls->is_action('next') || $controls->is_action('test')) {
    $email['id'] = $email_id;

    if ($this->is_html_allowed()) {
        $email['message'] = $controls->data['message'];
    } else {
        $email['message'] = wp_kses_post($controls->data['message']);
    }

    $email['subject'] = wp_strip_all_tags($controls->data['subject']);
    $this->save_email($email);
    if ($controls->is_action('next')) {
        $controls->js_redirect($this->get_admin_page_url('edit') . '&id=' . $email_id);
        return;
    }
}

if ($controls->is_action('test')) {
    $this->send_test_email($this->get_email($email_id), $controls);
}

$controls->data = Newsletter::instance()->get_email($email_id, ARRAY_A);

if (!$this->is_html_allowed()) {
    $controls->warnings[] = 'Your user cannot manage full HTML content, when saving the content will be filtered and get broken.';
}
?>

<?php include NEWSLETTER_INCLUDES_DIR . '/codemirror.php'; ?>

<style>
    .CodeMirror {
        height: 600px;
        margin-top: 15px;
        margin-bottom: 15px;
    }
</style>
<script>
    var templateEditor;
    jQuery(function () {
        templateEditor = CodeMirror.fromTextArea(document.getElementById("options-message"), {
            lineNumbers: true,
            mode: 'htmlmixed',
            lineWrapping: true,
            extraKeys: {"Ctrl-Space": "autocomplete"}
        });
    });
    function tnp_media(name) {
        var tnp_uploader = wp.media({
            title: "Select an image",
            button: {
                text: "Select"
            },
            frame: 'post',
            multiple: false,
            displaySetting: true,
            displayUserSettings: true
        }).on("insert", function () {
            wp.media;
            var media = tnp_uploader.state().get("selection").first();
            if (media.attributes.url.indexOf("http") !== 0)
                media.attributes.url = "http:" + media.attributes.url;

            if (!media.attributes.mime.startsWith("image")) {

                templateEditor.getDoc().replaceRange(url, templateEditor.getDoc().getCursor());

            } else {
                var display = tnp_uploader.state().display(media);
                var url = media.attributes.sizes[display.attributes.size].url;

                templateEditor.getDoc().replaceRange('<img src="' + url + '">', templateEditor.getDoc().getCursor());

            }
        }).open();
    }

</script>

<div class="wrap tnp-emails-editor-html" id="tnp-wrap">

    <div id="tnp-body">

        <?php $controls->show(); ?>

        <form action="" method="post" style="margin-top: 2rem">
            <?php $controls->init() ?>

            <?php $controls->text('subject', 60, 'Newsletter subject') ?>
            <a href="#" class="button-primary" onclick="tnp_suggest_subject(); return false;"><?php _e('Get ideas', 'newsletter') ?></a>
            <a href="#" class="button-primary" onclick="newsletter_textarea_preview('options-message'); return false;"><i class="fa fa-eye"></i></a>

            <input type="button" class="button-primary" value="Add media" onclick="tnp_media()">
            <?php $controls->textarea_preview('message', '100%', 700, '', '', false); ?>



            <div style="text-align: right ">
                <?php $controls->button_confirm('reset', __('Back to last save', 'newsletter'), 'Are you sure?'); ?>
                <?php $controls->button('test', __('Test', 'newsletter')); ?>
                <?php $controls->button('save', __('Save', 'newsletter')); ?>
                <?php $controls->button('next', __('Next', 'newsletter') . ' &raquo;'); ?>
            </div>
        </form>
        <?php include NEWSLETTER_DIR . '/emails/subjects.php'; ?>
    </div>
</div>