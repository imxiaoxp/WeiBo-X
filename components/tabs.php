<?php
$isCategory = $this->is('category');
$category = Typecho_Widget::widget('Widget_Metas_Category_List');
$pages = Typecho_Widget::widget('Widget\Contents\Page\Rows');
?>

<div class="tab-list-wrap">
    <div class="tab-list">
        <div class="tab-item<?php if (!$isCategory): ?> active<?php endif; ?>">
            <a href="<?php $this->options->siteUrl(); ?>">全部</a>
        </div>
        <?php while ($category->next()): ?>
            <div class="tab-item <?php if ($isCategory && $this->getArchiveSlug() == $category->slug): ?>active<?php endif; ?>">
                <a href="<?php $category->permalink(); ?>"><?php $category->name(); ?></a>
            </div>
        <?php endwhile; ?>
        <?php while ($pages->next()): ?>
            <div class="tab-item <?php if ($this->is('page') && $this->getArchiveSlug() == $pages->slug): ?>active<?php endif; ?>">
                <a href="<?php $pages->permalink(); ?>"><?php $pages->title(); ?></a>
            </div>
        <?php endwhile; ?>
    </div>
</div>
