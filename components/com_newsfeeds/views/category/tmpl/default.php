<?php if ( $this->params->get( 'page_title' ) ) : ?>
	<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo $this->category->name; ?>
	</div>
<?php endif; ?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php if ( @$this->image || @$this->category->description ) : ?>
<tr>
	<td valign="top" class="contentdescription<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php
		if ( isset($this->image) ) :  echo $this->image; endif;
		echo $this->category->description;
	?>
	</td>
</tr>
<?php endif; ?>
<tr>
	<td width="60%" colspan="2">
	<?php echo $this->loadTemplate('items'); ?>
	</td>
</tr>
</table>