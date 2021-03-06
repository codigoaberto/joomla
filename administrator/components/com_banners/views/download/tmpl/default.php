<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
?>
<script type="text/javascript">
<!--
	window.addEvent('domready', function() {
		document.id('jform_compressed1').addEvent('click', function(e){
			document.id('jform_inline0').checked=false;
			document.id('jform_inline1').checked=true;
		});
		document.id('jform_inline0').addEvent('click', function(e){
			document.id('jform_compressed0').checked=true;
			document.id('jform_compressed1').checked=false;
		});
	});
// -->
</script>
<form
	action="<?php echo JRoute::_('index.php?option=com_banners&view=tracks&task=tracks.display&format=raw');?>"
	method="post"
	name="adminForm"
	id="download-form"
	class="form-validate">
	<fieldset class="adminform">
		<legend><?php echo JText::_('Banners_Tracks_Download');?></legend>

		<?php foreach($this->form->getFields() as $field): ?>
			<?php if (!$field->hidden): ?>
				<?php echo $field->label; ?>
			<?php endif; ?>
			<?php echo $field->input; ?>
		<?php endforeach; ?>
		<div class="clr"></div>
		<button type="button" onclick="this.form.submit();window.top.setTimeout('window.parent.SqueezeBox.close()', 700);"><?php echo JText::_('Submit');?></button>
		<button type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('Cancel');?></button>

	</fieldset>
</form>

