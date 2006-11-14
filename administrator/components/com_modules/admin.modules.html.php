<?php
/**
* @version $Id$
* @package Joomla
* @subpackage Modules
* @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
* @license GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* @package Joomla
* @subpackage Modules
*/
class HTML_modules {

	/**
	* Writes a list of the defined modules
	* @param array An array of category objects
	*/
	function showModules( &$rows, &$client, &$page, $option, &$lists )
	{
		$user =& JFactory::getUser();

		//Ordering allowed ?
		$ordering = (($lists['order'] == 'm.position'));

		JCommonHTML::loadOverlib();
		?>
		<form action="index.php?option=com_modules" method="post" name="adminForm">

			<table>
			<tr>
				<td align="left" width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php
					echo $lists['assigned'];
					echo $lists['position'];
					echo $lists['type'];
					echo $lists['state'];
					?>
				</td>
			</tr>
			</table>

			<table class="adminlist" cellspacing="1">
			<thead>
			<tr>
				<th width="20">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" />
				</th>
				<th class="title">
					<?php JCommonHTML::tableOrdering( 'Module Name', 'm.title', $lists ); ?>
				</th>
				<th nowrap="nowrap" width="7%">
					<?php JCommonHTML::tableOrdering( 'Published', 'm.published', $lists ); ?>
				</th>
				<th width="80" nowrap="nowrap">
					<a href="javascript:tableOrdering('m.position','ASC');" title="<?php echo JText::_( 'Order by' ); ?> <?php echo JText::_( 'Order' ); ?>">
						<?php echo JText::_( 'Order' ); ?>
					</a>
				</th>
				<th width="1%">
					<?php JCommonHTML::saveorderButton( $rows ); ?>
				</th>
				<?php
				if ( $client->id == 0 ) {
					?>
					<th nowrap="nowrap" width="7%">
						<?php JCommonHTML::tableOrdering( 'Access', 'groupname', $lists ); ?>
					</th>
					<?php
				}
				?>
				<th nowrap="nowrap" width="3%">
					<?php JCommonHTML::tableOrdering( 'ID', 'm.id', $lists ); ?>
				</th>
				<th nowrap="nowrap" width="7%">
					<?php JCommonHTML::tableOrdering( 'Position', 'm.position', $lists ); ?>
				</th>
				<th nowrap="nowrap" width="5%">
					<?php JCommonHTML::tableOrdering( 'Pages', 'pages', $lists ); ?>
				</th>
				<th nowrap="nowrap" width="10%"  class="title">
					<?php JCommonHTML::tableOrdering( 'Type', 'm.module', $lists ); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
				<td colspan="12">
					<?php echo $page->getListFooter(); ?>
				</td>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row 	= &$rows[$i];

				$link 		= ampReplace( 'index.php?option=com_modules&client_id='. $client->id .'&task=edit&hidemainmenu=1&cid[]='. $row->id );

				$access 	= JCommonHTML::AccessProcessing( $row, $i );
				$checked 	= JCommonHTML::CheckedOutProcessing( $row, $i );
				$published 	= JCommonHTML::PublishedProcessing( $row, $i );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="right">
						<?php echo $page->getRowOffset( $i ); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
					<?php
					if ( $row->checked_out && ( $row->checked_out != $user->get('id') ) ) {
						echo $row->title;
					} else {
						?>
						<a href="<?php echo $link; ?>">
							<?php echo $row->title; ?>
						</a>
						<?php
					}
					?>
					</td>
					<td align="center">
						<?php echo $published;?>
					</td>
					<td class="order" colspan="2">
						<span><?php echo $page->orderUpIcon( $i, ($row->position == @$rows[$i-1]->position), 'orderup', 'Move Up', $ordering ); ?></span>
						<span><?php echo $page->orderDownIcon( $i, $n, ($row->position == @$rows[$i+1]->position),'orderdown', 'Move Down', $ordering ); ?></span>
						<?php $disabled = $ordering ?  '' : '"disabled=disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
					</td>
					<?php
					if ( $client->id == 0 ) {
						?>
						<td align="center">
							<?php echo $access;?>
						</td>
						<?php
					}
					?>
					<td align="center">
						<?php echo $row->id;?>
					</td>
					<td align="center">
						<?php echo $row->position; ?>
					</td>
					<td align="center">
						<?php
						if (is_null( $row->pages )) {
							echo JText::_( 'None' );
						} else if ($row->pages > 0) {
							echo JText::_( 'Varies' );
						} else {
							echo JText::_( 'All' );
						}
						?>
					</td>
					<td>
						<?php echo $row->module ? $row->module : JText::_( 'User' );?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="client_id" value="<?php echo $client->id;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php
	}

	/**
	* Writes the edit form for new and existing module
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.
	* @param JTableCategory The category object
	* @param array <p>The modules of the left side.  The array elements are in the form
	* <var>$leftorder[<i>order</i>] = <i>label</i></var>
	* where <i>order</i> is the module order from the db table and <i>label</i> is a
	* text label associciated with the order.</p>
	* @param array See notes for leftorder
	* @param array An array of select lists
	* @param object Parameters
	*/
	function editModule( &$row, &$orders2, &$lists, &$params, $option, $client )
	{
		// Check for component metadata.xml file
		//$path = JApplicationHelper::getPath( 'mod'.$client->id.'_xml', $row->module );
		//$params = new JParameter( $row->params, $path );

		$editor 	=& JFactory::getEditor();

		$row->titleA = '';
		if ( $row->id ) {
			$row->titleA = '<small><small>[ '. $row->title .' ]</small></small>';
		}

		JCommonHTML::loadOverlib();
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			if ( ( pressbutton == 'save' ) && ( document.adminForm.title.value == "" ) ) {
				alert("<?php echo JText::_( 'Module must have a title', true ); ?>");
			} else {
				<?php
				if ($row->module == '' || $row->module == 'custom') {
					echo $editor->save( 'content' );
				}
				?>
			}
			submitform(pressbutton);
		}
		<!--
		var originalOrder 	= '<?php echo $row->ordering;?>';
		var originalPos 	= '<?php echo $row->position;?>';
		var orders 			= new Array();	// array in the format [key,value,text]
		<?php	$i = 0;
		foreach ($orders2 as $k=>$items) {
			foreach ($items as $v) {
				echo "\n	orders[".$i++."] = new Array( \"$k\",\"$v->value\",\"$v->text\" );";
			}
		}
		?>
		//-->
		</script>
		<form action="index.php" method="post" name="adminForm">
		<div class="col60">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Details' ); ?></legend>

				<table class="admintable" cellspacing="1">
					<tr>
						<td valign="top" class="key">
							<?php echo JText::_( 'Module Type' ); ?>:
						</td>
						<td>
							<strong>
								<?php echo JText::_($row->module); ?>
							</strong>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="title">
								<?php echo JText::_( 'Title' ); ?>:
							</label>
						</td>
						<td>
							<input class="text_area" type="text" name="title" id="title" size="35" value="<?php echo $row->title; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100" class="key">
							<?php echo JText::_( 'Show title' ); ?>:
						</td>
						<td>
							<?php echo $lists['showtitle']; ?>
						</td>
					</tr>
					<tr>
						<td valign="top" class="key">
							<?php echo JText::_( 'Published' ); ?>:
						</td>
						<td>
							<?php echo $lists['published']; ?>
						</td>
					</tr>
					<tr>
						<td valign="top" class="key">
							<label for="position">
								<?php echo JText::_( 'Position' ); ?>:
							</label>
						</td>
						<td>
							<?php echo $lists['position']; ?>
						</td>
					</tr>
					<tr>
						<td valign="top"  class="key">
							<label for="ordering">
								<?php echo JText::_( 'Module Order' ); ?>:
							</label>
						</td>
						<td>
							<script language="javascript" type="text/javascript">
							<!--
							writeDynaList( 'class="inputbox" name="ordering" id="ordering" size="1"', orders, originalPos, originalPos, originalOrder );
							//-->
							</script>
						</td>
					</tr>
					<tr>
						<td valign="top" class="key">
							<label for="access">
								<?php echo JText::_( 'Access Level' ); ?>:
							</label>
						</td>
						<td>
							<?php echo $lists['access']; ?>
						</td>
					</tr>
					<tr>
						<td valign="top" class="key">
							<?php echo JText::_( 'ID' ); ?>:
						</td>
						<td>
							<?php echo $row->id; ?>
						</td>
					</tr>
					<tr>
						<td valign="top" class="key">
							<?php echo JText::_( 'Description' ); ?>:
						</td>
						<td>
							<?php echo JText::_($row->description); ?>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Parameters' ); ?></legend>
					<table class="admintable">
					<tr>
						<td>
							<?php echo $params->render();?>
						</td>
					</tr>
					</table>
			</fieldset>
		</div>
		<div class="col40">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Pages / Items' ); ?></legend>

				<table class="admintable">
				<tr>
					<td class="key vtop">
						<label for="selections">
							<?php echo JText::_( 'Menu Item Link(s)' ); ?>:
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $lists['selections']; ?>
					</td>
				</tr>
				</table>


			</fieldset>

		</div>
		<div class="clr"></div>

		<?php
		if ( !$row->module || $row->module == 'custom' || $row->module == 'mod_custom' ) {
			?>
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'Custom Output' ); ?></legend>

				<?php
				// parameters : areaname, content, width, height, cols, rows
				echo $editor->display( 'content', $row->content, '100%', '400', '60', '20' ) ;
				echo $editor->getButtons('content');
				?>

			</fieldset>
			<?php
		}
		?>

		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="original" value="<?php echo $row->ordering; ?>" />
		<input type="hidden" name="module" value="<?php echo $row->module; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="client_id" value="<?php echo $client->id ?>" />
		</form>
		<?php
	}

	function previewModule()
	{
		$editor =& JFactory::getEditor();

		?>
		<script>
		var form = window.top.document.adminForm
		var title = form.title.value;

		var alltext = window.top.<?php echo $editor->getContent('text') ?>;
		</script>

		<table align="center" width="90%" cellspacing="2" cellpadding="2" border="0">
			<tr>
				<td class="contentheading" colspan="2"><script>document.write(title);</script></td>
			</tr>
		<tr>
			<script>document.write("<td valign=\"top\" height=\"90%\" colspan=\"2\">" + alltext + "</td>");</script>
		</tr>
		</table>
		<?php
	}

/**
	/**
	* Displays a selection list for module types
	*/
	function addModule( &$modules, $client )
	{
 		JCommonHTML::loadOverlib();

		?>
		<form action="index.php" method="post" name="adminForm">

		<table class="adminlist" cellpadding="1">
		<thead>
		<tr>
			<th colspan="4">
			<?php echo JText::_( 'Modules' ); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th colspan="4">
			</th>
		</tr>
		</tfoot>

		<tbody>
		<?php
		$k 		= 0;
		$x 		= 0;
		$count 	= count( $modules );
		for ( $i=0; $i < $count; $i++ ) {
			$row = &$modules[$i];

			$link = 'index.php?option=com_modules&amp;task=edit&amp;module='. $row->module .'&amp;created=1&amp;client_id='. $client->id;
			if ( !$k ) {
				?>
				<tr class="<?php echo "row$x"; ?>" valign="top">
				<?php
				$x = 1 - $x;
			}
			?>
				<td width="50%">
					<input type="radio" id="cb<?php echo $i; ?>" name="module" value="<?php echo $row->module; ?>" onclick="isChecked(this.checked);" />
					<?php
					echo mosToolTip( stripslashes( $row->descrip ), stripslashes( $row->name ), 300, '', stripslashes( $row->name ), $link, 'LEFT' );
					?>
				</td>
			<?php
			if ( $k ) {
				?>
				</tr>
				<?php
			}
			?>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
		</table>

		<input type="hidden" name="option" value="com_modules" />
		<input type="hidden" name="client_id" value="<?php echo $client->id; ?>" />
		<input type="hidden" name="created" value="1" />
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="1" />
		</form>
		<?php
	}
}
?>
