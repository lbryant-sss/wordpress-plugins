<?php
$p = sanitize_key($_GET['page'] ?? '');
?>
<ul class="tnp-nav">
    <li class="tnp-nav-title"><?php esc_html_e('Subscribers', 'newsletter'); ?></li>
    <li class="<?php echo $p === 'newsletter_users_index' ? 'active' : '' ?>"><a href="?page=newsletter_users_index"><?php esc_html_e('Manage', 'newsletter') ?></a></li>
    <li class="<?php echo $p === 'newsletter_profile_index' ? 'active' : '' ?>"><a href="?page=newsletter_profile_index"><?php esc_html_e('Profile page', 'newsletter') ?></a></li>
    <li class="<?php echo $p === 'newsletter_unsubscription_index' ? 'active' : '' ?>"><a href="?page=newsletter_unsubscription_index"><?php esc_html_e('Unsubscribe', 'newsletter') ?></a></li>
    <li class="<?php echo $p === 'newsletter_users_statistics' ? 'active' : '' ?>"><a href="?page=newsletter_users_statistics"><?php esc_html_e('Statistics', 'newsletter') ?></a></li>
    <li class="<?php echo $p === 'newsletter_users_massive' ? 'active' : '' ?>"><a href="?page=newsletter_users_massive"><?php esc_html_e('Maintenance', 'newsletter') ?></a></li>
    <?php if (!class_exists('NewsletterImport')) { ?>
        <li class="<?php echo $p === 'newsletter_users_import' ? 'active' : '' ?>"><a href="?page=newsletter_users_import"><?php esc_html_e('Import/Export', 'newsletter') ?></a></li>
    <?php } else { ?>
        <li class="<?php echo $p === 'newsletter_import_index' ? 'active' : '' ?>"><a href="?page=newsletter_import_index"><?php esc_html_e('Import/Export', 'newsletter') ?></a></li>
    <?php } ?>
</ul>
<?php
unset($p);
?>
