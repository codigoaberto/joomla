<?php
/**
 * @version		$Id: default.php 11476 2009-01-25 06:58:51Z eddieajau $
 * @package		Joomla.Administrator
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');

	// Set toolbar items for the page
	JToolBarHelper::title(JText::_('FIELD_MANAGER'), 'generic.png');

	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::deleteList();
	JToolBarHelper::editListX();
	JToolBarHelper::addNewX();
	//JToolBarHelper::preferences('com_contact', '500');
	//JToolBarHelper::help('screen.contactmanager');
?>

<form action="index.php" method="post" name="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_('FILTER'); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_('GO'); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_('RESET'); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php
				echo $this->lists['state'];
			?>
		</td>
	</tr>
</table>
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_('NUM'); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort',  JText::_('TITLE'), 'f.title', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th class="alias">
				<?php echo JHtml::_('grid.sort',  JText::_('ALIAS'), 'f.alias', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th width="10%" class="type">
				<?php echo JHtml::_('grid.sort', JText::_('TYPE'), 'f.type', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th width="5%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('PUBLISHED'), 'f.published', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th width="8%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('ORDER'), 'f.ordering', $this->lists['order_Dir'], $this->lists['order']); ?>
				<?php echo JHtml::_('grid.order',  $this->items); ?>
			</th>
			<th width="10%">
				<?php echo JHtml::_('grid.sort',  JText::_('POSITION'), 'f.pos', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
			<th width="10%">
				<?php echo JHtml::_('grid.sort',  JText::_('ACCESS_LEVEL'), 'groupname', @$this->lists['order_Dir'], @$this->lists['order']); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('ID'), 'f.id', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="10">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count($this->items); $i < $n; $i++)
	{
		$row = &$this->items[$i];

		$link 		= JRoute::_('index.php?option=com_contact&controller=field&view=field&task=edit&cid[]='. $row->id);
		$access 	= JHtml::_('grid.access',   $row, $i);
		$checked 	= JHtml::_('grid.checkedout',   $row, $i);
		$published 	= JHtml::_('grid.published', $row, $i);

		$ordering = ($this->lists['order'] == 'f.ordering');

		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset($i); ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php
				if ( JTable::isCheckedOut($this->user->get ('id'), $row->checked_out)) {
					echo $row->title;
				} else {
				?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('EDIT_FIELDS');?>::<?php echo $row->title; ?>">
					<a href="<?php echo $link; ?>">
						<?php echo $row->title; ?></a></span>
				<?php
				}
				?>
			</td>
			<td>
				<?php echo $row->alias; ?>
			</td>
			<td>
				<?php echo $row->type; ?>
			</td>
			<td align="center">
				<?php echo $published;?>
			</td>
			<td class="order">
				<span><?php echo $this->pagination->orderUpIcon($i, ($row->pos == @$this->items[$i-1]->pos),'orderup', 'Move Up', $ordering); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $n, ($row->pos == @$this->items[$i+1]->pos), 'orderdown', 'Move Down', $ordering); ?></span>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
			</td>
			<td>
				<?php echo $row->pos;?>
			</td>
			<td align="center">
				<?php echo $access;?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

	<input type="hidden" name="controller" value="field" />
	<input type="hidden" name="option" value="com_contact" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form> 