<div class="wrap">
  <?php $options = get_option(REPAIR_PLUGIN_SLUG); ?>
  <div class="icon">
    <h2>
      <?php _e("TentBlogger's 404 Repair Plugin", REPAIR_PLUGIN_LOCALE); ?>
    </h2>
  </div>

  <p class="description">
    <?php _e("Repair 404 Errors WordPress Plugin simply notifies you of any page or file requests that a user (or search engine) makes to your blog.", REPAIR_PLUGIN_LOCALE); ?>
  </p>
  
  <div id="repair-container">
    <div id="poststuff">
      <div class="inside">
        
        <?php if($this->has_404s()) { ?>
          <div class="options">
            <a href="javascript:;" class="repair-all-link">
              <?php _e('Repair All', REPAIR_PLUGIN_LOCALE); ?>
            </a>
          </div>
        <?php } // end if ?>
        
        <?php echo $this->load_missing_pages(); ?>
        
        <?php if($this->has_404s()) { ?>
          <div class="options">
            <a href="javascript:;" class="repair-all-link">
              <?php _e('Repair All', REPAIR_PLUGIN_LOCALE); ?>
            </a>
          </div>
        <?php } // end if ?>
      </div>
      <div class="inside">
        <p>
          <?php _e('Feel free to <a href="http://twitter.com/tentblogger" target="_blank">follow me</a> on Twitter!', REPAIR_PLUGIN_LOCALE); ?>
        </p>
      </div>
    </div>
  </div><!-- /repair-container -->
  
</div>