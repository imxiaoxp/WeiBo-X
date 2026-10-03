<?php

/**
 * 评论 AJAX 接口（首页内联评论）
 *
 * GET  /usr/themes/sagittarius/action/comments.php?cid=<文章ID>
 *      以「首页内联」模式 include components/comments.php（评论容器的唯一实现），
 *      输出评论列表 + 内联表单。评论者认证（登录态/访客 Cookie/评论开关）与文章页
 *      共用同一份代码，不存在第二套实现。
 *
 * POST /usr/themes/sagittarius/action/comments.php
 *      服务端接管评论提交：Feedback::alloc(['checkReferer' => 'false'])
 *      跳过 Referer 门（首页来源路径与文章路径不匹配是正常业务场景），
 *      保留 token 反垃圾、评论审核、插件钩子、记忆 Cookie 全流程。
 *      成功时 Feedback 内部 redirect()->respond() 会 exit（302 到文章页）；
 *      失败时返回 JSON {ok:0, message}
 */

$config = dirname(dirname(dirname(dirname(__DIR__)))) . '/config.inc.php';
if (!file_exists($config)) {
    http_response_code(500);
    exit('Config not found');
}
require_once $config;

/* 统一 Cookie 前缀（关键修复）：登录/访客记忆 Cookie 的键名 = md5(rootUrl) + 键名，
 * 而 rootUrl 由 getRequestRoot() 按「当前请求」探测（scheme://host + SCRIPT_NAME 再
 * 去掉 .php/ 一层）。前台入口 SCRIPT_NAME=/index.php → rootUrl=https://host；
 * 本独立端点的 SCRIPT_NAME 指向主题目录 → rootUrl=https://host/usr/themes/.../action，
 * 两边前缀完全不同——首页写入的访客 Cookie 文章页读不到（反之亦然），登录态也会
 * 在端点里失效。这里利用官方预留的 __TYPECHO_ROOT_URL__（___rootUrl 的最高优先
 * 分支）显式定义安装根：从请求路径剥离主题子路径（兼容子目录安装），使本端点与
 * 前台 / admin 的 Cookie 前缀完全一致。 */
if (!defined('__TYPECHO_ROOT_URL__')) {
    $commentsThemePath = str_replace('\\', '/', str_replace(__TYPECHO_ROOT_DIR__, '', dirname(__DIR__)));
    $commentsScriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $commentsPos = '' === $commentsThemePath ? false : strrpos($commentsScriptName, $commentsThemePath);
    $commentsSiteBase = false === $commentsPos ? '' : substr($commentsScriptName, 0, $commentsPos);
    define('__TYPECHO_ROOT_URL__', \Typecho\Request::getInstance()->getUrlPrefix() . $commentsSiteBase);
    unset($commentsThemePath, $commentsScriptName, $commentsPos, $commentsSiteBase);
}

/* 核心引导（与前台入口同款）：加载路由表、插件钩子、时区，并在 Init 内部按站点
 * 配置初始化 Cookie 前缀。评论者认证与访客记忆全部由 components/comments.php
 * 走核心原生路径（User::hasLogin / Cookie::get('__typecho_remember_*')），本端点
 * 只负责引导与 HTTP 层，不做任何认证或 Cookie 的额外处理。
 * 缺少 Init 时 Router::$routingTable 为空，Router::url() 会静默返回 '#'，
 * Feedback::action 里 Router::match('#') 匹配不到路由 → 「找不到内容」。 */
\Widget\Init::alloc();

/* ---------------- POST：提交评论 ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        \Widget\Feedback::alloc(['checkReferer' => 'false'])->action();
        // 成功路径在 Feedback::comment() 内部 redirect()->respond() 已 exit
    } catch (\Typecho\Exception $e) {
        // \Typecho\Exception 同时覆盖 Widget\Exception（如「找不到内容」）与 Router\Exception
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8', true);
        }
        echo json_encode(['ok' => 0, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8', true);
        }
        echo json_encode(['ok' => 0, 'message' => 'Server error'], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

/* ---------------- GET：以「首页内联」模式渲染评论容器 ---------------- */

$db = \Typecho\Db::get();

$cid = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;
if ($cid <= 0) {
    exit('Invalid cid');
}

// 完整内容行（容器内的评论开关判定与隐藏字段需要 password/commentsNum/cid 等字段）
$content = $db->fetchRow(
    $db->select()->from('table.contents')->where('cid = ?', $cid)
);
if (!$content) {
    exit('Content not found');
}

// 真实路由路径（提交成功判定与回复跳转锚点用），附带日期字段以兼容按日期的自定义路由
$data = $content;
if (isset($content['created'])) {
    $data['year'] = date('Y', $content['created']);
    $data['month'] = date('m', $content['created']);
    $data['day'] = date('d', $content['created']);
}
try {
    $permalink = \Typecho\Router::url($content['type'], $data);
} catch (\Throwable $e) {
    $permalink = $content['type'] === 'page' ? '/' . $content['slug'] . '.html' : '/archives/' . $content['slug'] . '.html';
}

// 反垃圾 token：与提交页 URL（即本片段被加载时的 Referer）绑定
try {
    $token = \Widget\Security::alloc()->getToken(\Typecho\Request::getInstance()->getReferer());
} catch (\Throwable $e) {
    $token = '';
}

// 评论容器唯一实现：认证信息（登录态/访客记忆/评论开关）只此一份，与文章页完全一致
$comments_inline    = true;
$comments_content   = $content;
$comments_permalink = $permalink;
$comments_token     = $token;
include dirname(__DIR__) . '/components/comments.php';
exit;
