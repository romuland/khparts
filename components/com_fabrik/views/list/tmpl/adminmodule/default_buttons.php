<div class="fabrik_buttons">
	<ul class=""><?php if ($this->showAdd) {?>
		<li class="button addbutton">
			<a class="addRecord" href="<?php echo $this->addRecordLink;?>">
				<?php echo $this->buttons->add;?>
				<span><?php echo $this->addLabel?></span>
			</a>
		</li>
	<?php }?>
		<li class="button">
			<a href="index.php?option=com_fabrik&amp;task=list.view&amp;listid=<?php echo $this->list->id?>">
				<?php echo FabrikHelperHTML::image('view.png', 'list', $this->tmpl, 'view all');?>
				<span><?php echo JText::_('view all');?></span>
			</a>
		</li>
	</ul>
</div>