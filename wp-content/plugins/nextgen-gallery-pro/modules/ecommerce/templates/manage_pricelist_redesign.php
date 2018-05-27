<?php if ($errors): ?>
		<?php foreach ($errors as $msg): ?>
			<?php echo $msg ?>
		<?php endforeach ?>
	<?php endif ?>
	<?php if ($success AND empty($errors)): ?>
		<div class='success updated'>
			<p><?php esc_html_e($success);?></p>
		</div>
<?php endif ?>

<div class="wrap ngg_manage_pricelist" id='ngg_page_content' style='position: relative; visibility: hidden;'>

	<div class="ngg_page_content_header "><img src='<?php esc_html_e($logo) ?>' class='ngg_admin_icon'><h3><?php esc_html_e($page_heading)?></h3></div>

	<div class="ngg_page_content_main"">

		<form method="POST" action="<?php echo nextgen_esc_url($_SERVER['REQUEST_URI'])?>">
			<input type="hidden" name="pricelist[ID]" value="<?php echo esc_attr($model->id()) ?>"/>
			<br/>
			<div id="titlediv">
				<div id="titlewrap">
					<input type="text" placeholder='Enter title here' autocomplete="off" id="title" value="<?php echo esc_attr($model->title)?>" size="30" name="pricelist[title]">
				</div>
			</div>
			<?php if (isset($form_header)): ?>
				<?php echo $form_header."\n"; ?>
			<?php endif ?>
			<input type="hidden" name="action"/>
			<div class="accordion" id="nextgen_admin_accordion">
				<?php foreach($tabs as $tab): ?>
					<?php echo $tab ?>
				<?php endforeach ?>
			</div>
			<?php if ($show_save_button): ?>
				<p>
					<button type="submit" name='action_proxy' class="button-primary"   value="Save"><?php _e('Save', 'nextgen-gallery-pro'); ?></button>
					<button type="submit" name="action_proxy" class="button-primary" value="Delete"><?php _e('Delete', 'nextgen-gallery-pro'); ?></button>
	                <input
	                    type="submit"
	                    value="<?php _e('Cancel', 'nextgen-gallery-pro'); ?>"
	                    id="cancel_btn"
	                    class="button-primary"
	                    data-redirect="<?php echo admin_url('/edit.php?post_type=ngg_pricelist')?>"
	                />

				</p>
			<?php endif ?>
		</form>

	</div>

</div>

<script type="text/javascript">
    jQuery(function($){
        $('.price_field').live('change', function(){
            var $this = $(this);
            var parts = $this.val().split('.');
            var new_parts = [parts[0]];
            if (parts.length > 1) new_parts.push(parts[1]);
            var val = new_parts.join('.');
            val = val.replace(/[^0-9\.]/g, '');
            if (val.length > 0 && val != '.') val = sprintf("%.2f", parseFloat(val));
            $this.val(val);
        }).change();

        $('#cancel_btn').click(function(e){
            e.stopPropagation();
            e.stopImmediatePropagation();
            e.preventDefault();
            window.location = $(this).attr('data-redirect');
            return false;
        })
    });
</script>
