<?php
/**
 * @version
 * @package		Joomla.Site
 * @subpackage	com_newsfeeds
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php if (empty($this->parents)) : ?>
	<?php echo JText::_('NO PARENTS'); ?>
<?php else : ?>
	<h3><?php echo JText::_('PARENTS'); ?></h3>
	<ul>
		<?php foreach ($this->parents as &$item) : ?>
		<li>
			<a href="<?php echo JRoute::_(NewsfeedsRoute::category($item->slug)); ?>">
				<?php echo $this->escape($item->title); ?></a>
		</li>
		<?php endforeach; ?>
	</ul>

<?php endif; ?>