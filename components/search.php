<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$searchKeywords = $this->is('search') ? (string) $this->request->get('keywords') : '';
?>
<form class="search-box" method="get" action="<?php $this->options->siteUrl(); ?>" role="search">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <circle cx="11" cy="11" r="7" />
        <path d="m21 21-4.35-4.35" />
    </svg>
    <input type="text" name="s" value="<?php echo htmlspecialchars($searchKeywords); ?>"
        placeholder="<?php _e('搜索文章…'); ?>" aria-label="<?php _e('搜索'); ?>">
</form>
