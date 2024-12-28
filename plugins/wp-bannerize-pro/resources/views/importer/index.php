<?php if (!defined('ABSPATH')) {
  exit();
} ?>

<div class="wrap">

  <h2><?php esc_attr_e('WP Bannerize Importer'); ?></h2>

  <div class="wpbones-tabs">

    <?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::openTab(__('Import'), null, true); ?>

    <p>
      <?php esc_attr_e(
        'A previous release of Bannerize table has been found. You can import your previous banners by following the instruction below.'
      ); ?>
    </p>

    <table class="wp-bannerize-importer-table-info">
      <tbody>

        <tr>
          <th>
            <?php esc_attr_e('Table name'); ?>
          </th>
          <td>
            <?php echo esc_attr($importer->Tablename); ?>
          </td>
        </tr>

        <tr>
          <th>
            <?php esc_attr_e('Total records'); ?>
          </th>
          <td>
            <?php echo esc_attr($importer->totalRecords); ?>
          </td>
        </tr>

        <tr>
          <th>
            <?php esc_attr_e('Total enabled'); ?>
          </th>
          <td>
            <?php echo esc_attr(importer->totalEnabled); ?>
          </td>
        </tr>

        <tr>
          <th>
            <?php esc_attr_e('Total disabled'); ?>
          </th>
          <td>
            <?php echo esc_attr($importer->totalDisabled); ?>
          </td>
        </tr>

        <tr>
          <th>
            <?php esc_attr_e('Total trash'); ?>
          </th>
          <td>
            <?php echo esc_attr($importer->totalTrash); ?>
          </td>
        </tr>

      </tbody>
    </table>

    <form method="post">

      <?php wp_nonce_field('wp_bannerize_importer'); ?>

      <?php if ($importer->totalRecords > 0): ?>

        <p>
          <?php esc_attr_e('Select at least one group of Banner below that you would like to import'); ?>
        </p>

        <ul class="wp-bannerize-ul-column">
          <?php foreach ($importer->groups as $key => $label): ?>
            <li>
              <label for="<?php echo esc_attr($key); ?>">
                <input type="checkbox" checked="checked" value="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr(
  $key
); ?>" name="wp_bannerize_importer_groups[]" />
                <?php echo esc_attr($label); ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>

        <p>
          <label for="wp_bannerize_importer_groups">
            <?php  ?>
          </label>
        </p>

        <p>
          <?php esc_attr_e('Select at least one type of Banner below that you would like to import'); ?>
        </p>

        <p>
          <label for="wp_bannerize_importer_local">
            <input type="checkbox" checked="checked" value="1" id="wp_bannerize_importer_local" name="wp_bannerize_importer_types[]" />
            <?php esc_attr_e('Local'); ?>
          </label>
        </p>

        <p>
          <label for="wp_bannerize_importer_remote">
            <input type="checkbox" checked="checked" value="2" id="wp_bannerize_importer_remote" name="wp_bannerize_importer_types[]" />
            <?php esc_attr_e('Remote'); ?>
          </label>
        </p>

        <p>
          <label for="wp_bannerize_importer_text">
            <input type="checkbox" checked="checked" value="3" id="wp_bannerize_importer_text" name="wp_bannerize_importer_types[]" />
            <?php esc_attr_e('text'); ?>
          </label>
        </p>

        <hr />

        <p>
          <?php esc_attr_e('Optionals'); ?>
        </p>

        <p>
          <label for="wp_bannerize_importer_trash">
            <?php WPBannerize\Html::checkbox()
              ->name('wp_bannerize_importer_trash')
              ->id('wp_bannerize_importer_trash')
              ->value('1')
              ->render(); ?>

            <?php esc_attr_e('Import Trash'); ?>
          </label>
        </p>

        <p>
          <label for="wp_bannerize_importer_disabled">
            <?php WPBannerize\Html::checkbox()
              ->name('wp_bannerize_importer_disabled')
              ->id('wp_bannerize_importer_disabled')
              ->value('1')
              ->render(); ?>

            <?php esc_attr_e('Import Disabled'); ?>
          </label>
        </p>

        <p>
          <label for="wp_bannerize_importer_drop_table">
            <?php WPBannerize\Html::checkbox()
              ->name('wp_bannerize_importer_drop_table')
              ->id('wp_bannerize_importer_drop_table')
              ->value('1')
              ->render(); ?>

            <?php esc_attr_e('Drop previous bannerize table after import'); ?>

          </label>
        </p>

        <hr>

        <p>
          <button name="wp_bannerize_destroy_previous_table" value="do_destroy" data-confirm="<?php esc_attr_e(
            'Warning! Are you sure you want to permanently delete the previous Bannerize table?'
          ); ?>" class="button button-hero alignleft">
            <?php esc_attr_e('I don\'t want to import previous banners! Drop previous table!'); ?>
          </button>

          <button name="wp_bannerize_import" value="do_import" class="button-primary button button-hero alignright">
            <?php esc_attr_e('Start import'); ?>
          </button>
        </p>

      <?php else: ?>

        <p>
          <?php esc_attr_e(
            'The previous WP Bannerize database table is empty! Of course, you can drop it by click the button below.',
            'wp-bannerize'
          ); ?>
        </p>

        <p style="text-align: center">
          <button name="wp_bannerize_destroy_previous_table" value="do_destroy" data-confirm="<?php esc_attr_e(
            'Warning! Are you sure you want to permanently delete the previous Bannerize table?'
          ); ?>" class="button button-hero">
            <?php esc_attr_e('Drop previous table!'); ?>
          </button>
        </p>

      <?php endif; ?>

    </form>

    <?php WPBannerize\PureCSSTabs\PureCSSTabsProvider::closeTab(); ?>

  </div>

</div>

<script>
  jQuery(function($) {

    $('button[name="wp_bannerize_destroy_previous_table"]').on('click', function() {
      return confirm($(this).data('confirm'));

    });

  });
</script>