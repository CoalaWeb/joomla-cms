<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$trashed	= $this->state->get('filter.state') == -2 ? true : false;
$canOrder	= $user->authorise('core.edit.state', 'com_modules');
$saveOrder	= ($listOrder == 'ordering');
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_modules&task=modules.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'moduleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_modules'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<?php
		// Search tools bar and filters
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('COM_MODULES_MSG_MANAGE_NO_MODULES'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="moduleList">
				<thead>
					<tr>
						<th width="1%" class="hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="hidden-phone">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center" style="min-width:55px">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th class="title">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="15%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MODULES_HEADING_POSITION', 'position', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone" >
							<?php echo JHtml::_('searchtools.sort', 'COM_MODULES_HEADING_MODULE', 'name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_MODULES_HEADING_PAGES', 'pages', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th width="5%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'ordering');
					$canCreate  = $user->authorise('core.create',     'com_modules');
					$canEdit	= $user->authorise('core.edit',		  'com_modules.module.' . $item->id);
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id')|| $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_modules.module.' . $item->id) && $canCheckin;
				?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->ordering; ?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu"></span>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
							<?php endif; ?>
						</td>
						<td class="center">
							<?php if ($item->enabled > 0) : ?>
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							<?php endif; ?>
						</td>
						<td class="center">
							<div class="btn-group">
							<?php // Check if extension is enabled ?>
							<?php if ($item->enabled > 0) : ?>
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'modules.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								<?php // Create dropdown items ?>
								<?php JHtml::_('actionsdropdown.duplicate', 'cb' . $i, 'modules'); ?>
								<?php $action = $trashed ? 'untrash' : 'trash'; ?>
								<?php JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'modules'); ?>
								<?php // Render dropdown list ?>
								<?php echo JHtml::_('actionsdropdown.render', $this->escape($item->title)); ?>
							<?php else : ?>
								<?php // Extension is not enabled, show a message that indicates this. ?>
								<button class="btn-micro hasTooltip" title="<?php echo JText::_('COM_MODULES_MSG_MANAGE_EXTENSION_DISABLED'); ?>"><i class="icon-ban-circle"></i></button>
							<?php endif; ?>
							</div>
						</td>
						<td class="has-context">
							<div class="pull-left">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'modules.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_modules&task=module.edit&id=' . (int) $item->id); ?>">
										<?php echo $this->escape($item->title); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->title); ?>
								<?php endif; ?>

								<?php if (!empty($item->note)) : ?>
									<div class="small">
										<?php echo JText::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note));?>
									</div>
								<?php endif; ?>
							</div>
						</td>
						<td class="small hidden-phone">
							<?php if ($item->position) : ?>
								<span class="label label-info">
									<?php echo $item->position; ?>
								</span>
							<?php else : ?>
								<span class="label">
									<?php echo JText::_('JNONE'); ?>
								</span>
							<?php endif; ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $item->name;?>
						</td>
						<td class="small hidden-phone">
							<?php echo $item->pages; ?>
						</td>

						<td class="small hidden-phone">
							<?php echo $this->escape($item->access_level); ?>
						</td>
						<td class="small hidden-phone">
							<?php if ($item->language == ''):?>
								<?php echo JText::_('JDEFAULT'); ?>
							<?php elseif ($item->language == '*'):?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php else:?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
							<?php endif;?>
						</td>
						<td class="hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif;?>

		<?php // Load the batch processing form. ?>
		<?php if ($user->authorise('core.create', 'com_modules')
			&& $user->authorise('core.edit', 'com_modules')
			&& $user->authorise('core.edit.state', 'com_modules')) : ?>
			<?php echo JHtml::_(
				'bootstrap.renderModal',
				'collapseModal',
				array(
					'title' => JText::_('COM_MODULES_BATCH_OPTIONS'),
					'footer' => $this->loadTemplate('batch_footer')
				),
				$this->loadTemplate('batch_body')
			); ?>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
