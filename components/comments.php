<?php
if (!defined('__TYPECHO_ROOT_DIR__'))
    exit;

/**
 * 评论容器（文章页 / 首页内联评论共用，评论者认证唯一实现）
 *
 * 文章页：$this->need('components/comments.php') 调用，$this 为 Widget\Archive，
 *         行为与历史版本一致（完整表单 / 评论分页）。
 *
 * 首页内联：action/comments.php 设置以下变量后 include：
 *   $comments_inline     = true
 *   $comments_content    = 文章内容行（需含 allowComment/password/commentsNum/cid/created/authorId）
 *   $comments_permalink  = 文章永久链接
 *   $comments_token      = 反垃圾 token
 *
 * 两处共用的认证数据（只此一份，与核心原生路径一致）：
 *   $commentsHasLogin  登录态（User::hasLogin，与文章页 $this->user->hasLogin() 同一单例）
 *   $commentsGuest     访客记忆（Cookie::get('__typecho_remember_*')，与 $this->remember() 同源）
 *   $commentsHasInfo   访客信息是否完备（登录态覆盖访客信息）
 *   $commentsAllow     评论开关（等价核心 Contents::allow('comment') + ___hidden）
 */

$commentsInline = !empty($comments_inline);
$commentsOptions = \Widget\Options::alloc();
$commentsUser = \Widget\User::alloc();

/* ============ 评论者认证（唯一实现） ============ */
$commentsHasLogin = $commentsUser->hasLogin();

// 访客记忆：核心 Cookie::get('__typecho_remember_*')，即文章页 $this->remember() 的同款调用
// （核心 remember() 白名单仅 author/mail/url，text 不预填，与此处行为保持一致）
$commentsGuest = array('author' => '', 'mail' => '', 'url' => '');
if (!$commentsHasLogin) {
    foreach (array_keys($commentsGuest) as $commentsGuestKey) {
        $commentsGuest[$commentsGuestKey] = (string)\Typecho\Cookie::get('__typecho_remember_' . $commentsGuestKey, '');
    }
}
unset($commentsGuestKey);

// 访客信息是否完备：登录用户由 Feedback 服务端用账号信息兜底，不受此判定影响
$commentsHasInfo = $commentsHasLogin
    || ($commentsGuest['author'] !== ''
        && (!$commentsOptions->commentsRequireMail || $commentsGuest['mail'] !== '')
        && (!$commentsOptions->commentsRequireUrl || $commentsGuest['url'] !== ''));

// 内容行：文章页取当前文章，内联模式取端点传入（$this 仅在文章页分支被触碰）
if ($commentsInline) {
    $commentsRow = $comments_content;
} else {
    $commentsRow = $this->row;
}

/* ============ 评论开关（唯一实现，等价核心 Contents::allow('comment') + ___hidden） ============ */
$commentsAllow = (int) $commentsRow['allowComment'] === 1;
$commentsHidden = strlen($commentsRow['password']) > 0
    && $commentsRow['password'] !== \Typecho\Cookie::get('protectPassword_' . $commentsRow['cid'])
    && $commentsRow['authorId'] != $commentsUser->uid
    && !$commentsUser->pass('editor', true);
if ($commentsAllow && !$commentsHidden && $commentsOptions->commentsPostTimeout > 0 && $commentsOptions->commentsAutoClose
    && $commentsOptions->time - $commentsRow['created'] > $commentsOptions->commentsPostTimeout) {
    $commentsAllow = false;
}

/* 首页内联列表组件：复用核心 Comments\Archive 的查询/树化/排序/总数统计，仅在收尾
 * 把顶级评论截取为最近 N 条（子评论随父完整保留）。不借用核心分页——其「最后一页」
 * 的 ceil 语义在总数非 pageSize 整数倍时只剩零头（如 11 条只显 1 条）；也不改变
 * commentsOrder，列表顺序与文章页保持一致。 */
if (!class_exists('SagittariusInlineComments')) {
    class SagittariusInlineComments extends \Widget\Comments\Archive
    {
        /** 截取前的顶级评论总数，供「查看更多」显示判定（核心 total 为 private） */
        public $inlineTotal = 0;

        protected function initParameter(\Typecho\Config $parameter)
        {
            parent::initParameter($parameter);
            $parameter->setDefault(['inlinePageSize' => 5]);
        }

        public function execute()
        {
            parent::execute();

            $this->inlineTotal = count($this->stack);

            if ($this->inlineTotal > $this->parameter->inlinePageSize) {
                $size = $this->parameter->inlinePageSize;
                // ASC（旧→新）取末尾 N 条即最新；DESC（新→旧）取开头 N 条即最新，均保序保键
                $this->stack = 'DESC' == $this->options->commentsOrder
                    ? array_slice($this->stack, 0, $size, true)
                    : array_slice($this->stack, -$size, null, true);
                $this->length = count($this->stack);
                $this->row = $this->length > 0 ? current($this->stack) : [];
                reset($this->stack);
            }
        }
    }
}

// 评论列表：文章页走 $this->comments()（带 parentContext 供分页），内联按 cid 构造，渲染共用
if ($commentsInline) {
    // 禁用核心评论分页（若站点全局开启）：全量树化后由上方子类统一截取最近 5 条，
    // execute() 在 alloc 内即完成，返回后立即恢复，不影响本请求其它逻辑
    $commentsInlinePageSize = 5;
    $commentsSavedPageBreak = $commentsOptions->commentsPageBreak;
    $commentsOptions->commentsPageBreak = false;

    $commentsList = SagittariusInlineComments::alloc([
        'parentId'       => $commentsRow['cid'],
        'respondId'      => '',
        'commentPage'    => 0,
        'allowComment'   => $commentsAllow ? 1 : 0,
        'inlinePageSize' => $commentsInlinePageSize,
    ]);

    $commentsOptions->commentsPageBreak = $commentsSavedPageBreak;

    // 截取前的顶级评论总数，用于「查看更多」的显示判定
    $commentsInlineTotal = $commentsList->inlineTotal;
} else {
    $commentsList = $this->comments();
}

// 主题函数：threadedComments 评论回调 + getAuthorAvatar 头像源（require_once 幂等）
require_once dirname(__DIR__) . '/functions.php';

if ($commentsInline):
    /* ==================== 首页内联模式 ==================== */
    ?>
<div class="inline-comments__list" data-count="<?php echo (int)$commentsRow['commentsNum']; ?>">
    <?php if ($commentsList->have()): ?>
        <?php $commentsList->listComments(array(
            'before' => '<ol class="comment__list">',
            'after' => '</ol>',
            'replyWord' => '回复',
            'customTemplate' => 'threadedComments' // functions.php 的回调函数名
        )); ?>
    <?php else: ?>
        <div class="inline-comments__empty">暂无评论，来说两句吧</div>
    <?php endif; ?>
</div>
<?php if ($commentsInlineTotal > $commentsInlinePageSize): ?>
<a class="inline-comments__more" href="<?php echo htmlspecialchars($comments_permalink); ?>#comments">查看更多评论</a>
<?php endif; ?>
<?php if ($commentsAllow && !$commentsHidden): ?>
<form class="inline-comments__form" method="post" action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" data-has-info="<?php echo $commentsHasInfo ? '1' : '0'; ?>">
    <?php if ($commentsHasLogin || '' !== $commentsGuest['author']):
        // 登录态显示账号头像，游客显示访客 Cookie 记忆的邮箱头像（与评论列表同源链路）
        $commentsAvatarMail = $commentsHasLogin ? $commentsUser->mail : $commentsGuest['mail']; ?>
        <img class="inline-comments__avatar"
            src="<?php echo htmlspecialchars(getAuthorAvatar($commentsAvatarMail, $commentsOptions)); ?>"
            alt="<?php echo htmlspecialchars($commentsHasLogin ? $commentsUser->screenName : $commentsGuest['author']); ?>">
    <?php endif; ?>
    <div class="inline-comments__input-wrap">
        <div class="inline-comments__input-row">
            <textarea class="inline-comments__textarea" name="text" placeholder="写评论..." rows="1" required></textarea>
            <button class="owo-trigger" type="button"
                data-owo="<?php echo htmlspecialchars(\Typecho\Common::url('assets/OwO.json', $commentsOptions->themeUrl)); ?>"
                aria-label="插入表情" title="插入表情">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M8.5 14.5s1.2 1.8 3.5 1.8 3.5-1.8 3.5-1.8" />
                    <line x1="9" y1="9.5" x2="9.01" y2="9.5" />
                    <line x1="15" y1="9.5" x2="15.01" y2="9.5" />
                </svg>
            </button>
        </div>
        <div class="owo-box" hidden></div>
        <input type="hidden" name="type" value="comment">
        <input type="hidden" name="permalink" value="<?php echo htmlspecialchars($comments_permalink); ?>">
        <input type="hidden" name="_" value="<?php echo htmlspecialchars($comments_token); ?>">
        <input type="hidden" name="cid" value="<?php echo (int)$commentsRow['cid']; ?>">
        <input type="hidden" name="parent" value="">
        <input type="hidden" name="author" value="<?php echo htmlspecialchars($commentsGuest['author']); ?>">
        <input type="hidden" name="mail" value="<?php echo htmlspecialchars($commentsGuest['mail']); ?>">
        <input type="hidden" name="url" value="<?php echo htmlspecialchars($commentsGuest['url']); ?>">
    </div>
    <button class="inline-comments__submit" type="submit">发送</button>
</form>
<?php else: ?>
<div class="inline-comments__empty">评论已关闭</div>
<?php endif;
else:
    /* ==================== 文章页模式 ==================== */
    ?>
<div id="comments" class="comment__wrapper">
    <?php if ($commentsAllow && !$commentsHidden): ?>
        <div id="<?php $this->respondId(); ?>" class="respond">
            <form class="comment__form <?php echo $commentsHasLogin ? 'login' : 'guest'; ?>" method="post" action="<?php $this->commentUrl() ?>"
                role="form" data-action="<?php echo $this->commentUrl(); ?>">
                <div class="comment__form__content">
                    <?php if ($commentsHasLogin): ?>
                        <a href="<?php $commentsOptions->profileUrl(); ?>">
                            <img class="comment__avatar"
                                src="<?php echo htmlspecialchars(getAuthorAvatar($commentsUser->mail, $commentsOptions)); ?>"
                                alt="<?php echo htmlspecialchars($commentsUser->screenName()); ?>">
                        </a>
                    <?php else: ?>
                        <div class="comment__input__list">
                            <div class="comment__input__item">
                                <?php echo getSvg('user'); ?>
                                <input type="text" name="author" class="text" placeholder="称呼"
                                    value="<?php echo htmlspecialchars($commentsGuest['author']); ?>" required />
                            </div>
                            <div class="comment__input__item">
                                <?php echo getSvg('mail'); ?>
                                <input type="email" name="mail" class="text" placeholder="邮箱"
                                    value="<?php echo htmlspecialchars($commentsGuest['mail']); ?>" <?php if ($commentsOptions->commentsRequireMail): ?>required
                                <?php endif; ?> />
                            </div>
                            <div class="comment__input__item">
                                <?php echo getSvg('link'); ?>
                                <input type="url" name="url" class="text" placeholder="网站"
                                    value="<?php echo htmlspecialchars($commentsGuest['url']); ?>" />
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="comment__textarea__wrapper">
                        <textarea class="comment__textarea" name="text" placeholder="在这里输入您的评论" required
                            rows="1"></textarea>
                        <button class="owo-trigger" type="button"
                            data-owo="<?php echo htmlspecialchars(\Typecho\Common::url('assets/OwO.json', $commentsOptions->themeUrl)); ?>"
                            aria-label="插入表情" title="插入表情">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M8.5 14.5s1.2 1.8 3.5 1.8 3.5-1.8 3.5-1.8" />
                                <line x1="9" y1="9.5" x2="9.01" y2="9.5" />
                                <line x1="15" y1="9.5" x2="15.01" y2="9.5" />
                            </svg>
                        </button>
                        <div class="owo-box" hidden></div>
                        <div class="comment__textarea__tip"></div>
                    </div>
                </div>
                <div class="comment__login__footer">
                    <button class="comment__cancel" type="button">取消回复</button>
                    <button class="comment__submit" type="submit">提交评论</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <p><?php _t('评论已关闭'); ?></p>
    <?php endif; ?>

    <h3><?php $this->commentsNum(_t('暂无评论'), _t('仅有一条评论'), _t('%d 条评论')); ?></h3>
    <?php if ($commentsList->have()): ?>
        <?php $commentsList->listComments(array(
            'before' => '<ol class="comment__list">',
            'after' => '</ol>',
            'replyWord' => '回复',
            'customTemplate' => 'threadedComments' // 这里必须对应 functions.php 里的函数名
        )); ?>

        <?php $commentsList->pageNav('&laquo; 前一页', '后一页 &raquo;'); ?>
    <?php endif; ?>
</div>
<?php endif;
