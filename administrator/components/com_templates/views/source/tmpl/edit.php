<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
<!--
	function submitbutton(task)
	{
		if (task == 'source.cancel' || document.formvalidator.isValid(document.id('source-form'))) {
			<?php echo $this->form->getField('source')->save(); ?>
			submitform(task);
		}
		else {
			alert('<?php echo $this->escape(JText::_('JValidation_Form_failed'));?>');
		}
	}
// -->
</script>

<form action="<?php JRoute::_('index.php?option=com_templates'); ?>" method="post" name="adminForm" id="source-form" class="form-validate">
	<fieldset class="adminform">
		<legend><?php echo JText::sprintf('Templates_Template_Filename', $this->source->filename, $this->template->element); ?></legend>

		<?php if ($this->ftp) : ?>
		<?php $this->loadTemplate('ftp'); ?>
		<?php endif; ?>

		<?php echo $this->form->getLabel('source'); ?>
		<div class="clr"></div>
		<div class="editor-border">
		<?php echo $this->form->getInput('source'); ?>
		</div>
	</fieldset>

	<?php echo $this->form->getInput('extension_id'); ?>
	<?php echo $this->form->getInput('filename'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
