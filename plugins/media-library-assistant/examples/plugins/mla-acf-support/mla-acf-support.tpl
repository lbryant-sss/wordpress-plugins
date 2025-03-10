<!-- template="page" -->
<form>
  <table width="99%" style="display: none">
    <tbody id="mla-acf-support">
      <tr id="mla-bulk-acf-support" class="inline-edit-row inline-edit-row-attachment inline-edit-attachment bulk-acf-support-row bulk-acf-support-row-attachment bulk-acf-support-attachment" style="display: none">
        <td colspan="[+colspan+]" class="colspanchange">
          <fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
              <h4>Copy Items</h4>
              <div id="mla-acf-support-title-div">
                <div id="mla-acf-support-titles"></div>
              </div>
            </div>
          </fieldset>
          <fieldset id="mla-acf-support-settings" class="inline-edit-col-right">
            <div class="inline-edit-col">
            <h4>(Pull down the Help menu and select Copy Items for setting details)</h4>
            <div class="inline-edit-group clear">
			<table><tr>
              <td><label>
                <input name="mla_acf_support_options[map_custom]" id="mla-acf-support-options-map-custom" type="checkbox" [+map-custom-checked+] value="checked" />
                Map Custom Field metadata
              </label></td>
              <td><label>
                <input name="mla_acf_support_options[map_iptc_exif]" id="mla-acf-support-options-map-iptc-exif" type="checkbox" [+map-iptc-exif-checked+] value="checked" />
                Map IPTC/EXIF metadata
              </label></td>
			  </tr><tr>
              <td><label>
                <input name="mla_acf_support_options[copy_terms]" id="mla-acf-support-options-copy-terms" type="checkbox" [+copy-terms-checked+] value="checked" />
                Copy Taxonomy Terms
              </label></td>
              <td><label>
                <input name="mla_acf_support_options[copy_custom]" id="mla-acf-support-options-copy-custom" type="checkbox" [+copy-custom-checked+] value="checked" />
                Copy Custom Fields
              </label></td>
			  </tr><tr>
              <td><label>
                <input name="mla_acf_support_options[copy_item]" id="mla-acf-support-options-acf-support" type="checkbox" [+acf-support-checked+] value="checked" />
                Copy Item Fields
              </label></td>
              <td>&nbsp;</td>
			  </tr></table>
            </div> <!-- inline-edit-group -->
            </div> <!-- inline-edit-col -->
          </fieldset>
          <p class="submit inline-edit-save"> <a accesskey="c" href="#mla-acf-support" title="Cancel" class="button-secondary cancel alignleft">Cancel</a>
            <input type="submit" name="mla-acf-support" id="mla-acf-support-submit" class="button-primary alignright" value="Copy Items" />
            <input type="hidden" name="page" value="mla-menu" />
            <input type="hidden" name="screen" value="media_page_mla-menu" />
            <span class="error" style="display:none;"></span> <br class="clear" />
          </p>
        </td>
      </tr>
    </tbody>
  </table>
</form>
