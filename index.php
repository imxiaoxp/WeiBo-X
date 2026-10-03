<?php

/**
 * Sagittarius Theme 修改版：Sagittarius - X
 * 基于 夏源 的 Sagittarius Theme 修改。
 *
 * @package Sagittarius - X
 * @author 夏源 <https://blog.xiayuanovo.cn>
 * @author Xiao <https://be.de.cool> (修改者)
 * @version 1.0.0
 * @license GPL-3.0-only
 * @original https://github.com/xiayuanOvO/typecho-sagittarius
 * @link https://github.com/imxiaoxp/sagittarius-x
 * @modified 2026-10-03
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="site__wrapper">
    <?php $this->need('components/header.php'); ?>
    <main class="site__content">
        <?php $this->need('components/sidebar.php'); ?>
        <div class="site__main">
            <?php $this->need('components/search.php'); ?>
            <?php $this->need('components/tabs.php'); ?>
            <div class="post__list">
                <?php while ($this->next()): ?>
                    <div class="post__item">
                        <!-- 文章侧边栏 -->
                        <div class="post__sidebar">
                            <a class="post__avatar" href="<?php $this->author->permalink(); ?>"
                                title="<?php $this->author(); ?>">
                                <img src="<?php echo htmlspecialchars(getAuthorAvatar($this->author->mail, $this->options)); ?>"
                                    alt="<?php echo htmlspecialchars($this->author->name); ?>">
                            </a>
                        </div>
                        <div class="post__main">
                            <div class="post__meta">
                                <div class="meta__name">
                                    <?php $this->author(); ?>
                                </div>
                                <a class="meta__date" href="<?php $this->permalink(); ?>"
                                    time="<?php echo $this->created; ?>">
                                    <?php echo $this->date('Y-m-d H:i'); ?>
                                </a>
                            </div>
                            <div class="post__content">
                                <a href="<?php $this->permalink(); ?>">
                                <?php echo getPostExcerpt($this->content); ?>
                                </a>
                                <?php echo getPostPlayerHtml($this->content); ?>
                                <?php
                                // 最多显示 9 张（3x3 九宫格），10 张以上截断；无图不渲染容器
                                $images = array_slice(getAllImages(content: $this->content), 0, 9);
                                if ($images):
                                ?>
                                <div class="post__images">
                                    <?php foreach ($images as $image): ?>
                                        <img class="post__image" loading="lazy" src="<?php echo $image; ?>" alt="<?php $this->title(); ?>">
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="post__footer">
                                <div class="footer__item">
                                    <?php echo getSvg('eye', 'footer__icon'); ?>
                                    <span><?php viewsNum($this); ?></span>
                                </div>
                                <button class="footer__item footer__comment" data-cid="<?php echo $this->cid; ?>" data-url="<?php echo getCommentsUrl(); ?>">
                                    <?php echo getSvg('comment', 'footer__icon'); ?>
                                    <span><?php $this->commentsNum('%d'); ?></span>
                                </button>
                                <button class="footer__item like-btn" data-cid="<?php echo $this->cid; ?>" data-url="<?php echo getLikeUrl(); ?>">
                                    <?php echo getSvg('thumbs-up', 'footer__icon'); ?>
                                    <span class="like-btn__count"><?php echo getAgreeNum($this); ?></span>
                                </button>
                            </div>
                            <div class="post__comments" data-cid="<?php echo $this->cid; ?>"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div id="load-indicator" class="load-indicator">
                <span class="load-indicator__text"></span>
                <?php $this->pageLink('', 'next'); ?>
            </div>
        </div>
    </main>
</div>

<?php $this->need('footer.php'); ?>