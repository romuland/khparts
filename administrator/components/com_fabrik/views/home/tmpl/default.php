<?php
JHTML::stylesheet('media/com_fabrik/css/admin.css');
jimport('joomla.html.pane');
$pane	=& JPane::getInstance('Sliders');
JToolBarHelper::title(JText::_('COM_FABRIK_WELCOME'), 'fabrik.png');
?>

<table class="adminForm" style="width: 100%">
	<tbody>
		<tr>
			<td valign="top" style="width: 50%">
			<a href="http://fabrikar.com">
				<?php echo JHTML::image('media/com_fabrik/images/logo.png', 'Fabrik logo'); ?>
				</a>
			<div style="float:left;width:250px;margin-top:30px;">
			<a href="http://fabrikar.com/subscribe">
				<?php echo JHTML::image('media/com_fabrik/images/box.png', 'Fabrik'); ?>
			</a>
			</div>
			<div style="margin-left:200px;margin-top:30px;">
			<h1><?php echo JText::_('COM_FABRIK_HOME_SUBSCRIBE_TITLE')?></h1>
			<?php echo JText::_('COM_FABRIK_HOME_SUBSCRIBE_FEATURES')?>
			<a href="http://fabrikar.com/subscribe">
			<?php echo JHTML::image('media/com_fabrik/images/subscribe-now.png', 'Fabrik'); ?>
			</a><br />
			</div>

			</td>
			<td valign="top"  style="width: 50%"><?php
			echo $pane->startPane("content-pane");
			echo $pane->startPanel( JText::_('COM_FABRIK_HOME_ABOUT'), "publish-page");
			echo "<table class='adminlist'>
			<tr><td>".JText::_('COM_FABRIK_HOME_ABOUT_TEXT')."</td></tr></table>";
			echo $pane->endPanel();

			echo $pane->startPanel( JText::_('COM_FABRIK_HOME_NEWS'), "publish-page");
			echo $this->feed;
			echo $pane->endPanel();

			echo $pane->startPanel( JText::_('COM_FABRIK_HOME_STATS'), "publish-page");
			?>
			<table class='adminlist'>
			<thead>
				<tr>
					<th style="width:20%"><?php echo JText::_('COM_FABRIK_HOME_DATE')?></th>
					<th><?php echo JText::_('COM_FABRIK_HOME_ACTION')?></th>
				</tr>
			</thead>
			<tbody>
					<?php foreach ($this->logs as $log) {?>
					<tr>
						<td>
						<?php echo $log->timedate_created;?>
						</td>
						<td>
						<span class="editlinktip hasTip" title="<?php echo $log->message_type . "::" . $log->message; ?>">
							<?php echo $log->message_type;?>
						</span>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			<?php
			echo $pane->endPanel();

			echo $pane->startPanel( JText::_('COM_FABRIK_HOME_USEFUL_LINKS'), "publish-page");
			?>

			<ul class="adminlist">
				<li><a href="http://fabrikar.com/"><?php echo JText::_('COM_FABRIK_HOME_FABRIK_WEB_SITE')?></a></li>
				<li><a href="http://fabrikar.com/forums"><?php echo JText::_('COM_FABRIK_HOME_FORUM')?></a>
				<li><a href="http://fabrikar.com/wiki/"><?php echo JText::_('COM_FABRIK_HOME_DOCUMENTATION_WIKI')?></a></li>
			</ul>

			<?php
			echo $pane->endPanel();

			echo $pane->startPanel( JText::_('COM_FABRIK_HOME_TOOLS'), "publish-page"); ?>

			<ul class="adminlist">
				<li><a href="index.php?option=com_fabrik&task=home.installSampleData">
				<?php echo JText::_('COM_FABRIK_HOME_INSTALL_SAMPLE_DATA')?></a>
				</li>
				<li><a onclick="return confirm('<?php echo JText::_('COM_FABRIK_HOME_CONFIRM_WIPE', true);?>')" href="index.php?option=com_fabrik&task=home.reset">
					<?php echo JText::_('COM_FABRIK_HOME_RESET_FABRIK') ?>
				</a></li>
				<li>
					<a href="index.php?option=com_fabrik&task=upgrade.check">Upgrade from 2.1</a>
				</li>
			</ul>

			<?php
			echo $pane->endPanel();
			echo $pane->endPane();
			?></td>
		</tr>
	</tbody>
</table>