<?php

/**
 * 主题配置
 * 
 * @param Typecho_Widget_Helper_Form $form
 * @return void
 */
function themeConfig($form)
{
    // setConfigLayoutHeader();

    // ================================ 全局设置 ================================
    addTitle($form, '全局设置');

    // 头像源
    addSelect($form, 'avatarUrl', array(
        'lty' => _t('lty'),
        'cravatar' => _t('cravatar'),
        'weavatar' => _t('weavatar'),
        'loli' => _t('loli'),
        'gravatar' => _t('gravatar')
    ), 'lty', '头像源', '');

    // 背景模式
    addSelect($form, 'bgMode', array(
        'image' => _t('图片模式'),
        'color' => _t('纯色模式')
    ), 'color', '背景模式', '切换后，下方会自动显示对应的输入框');

    // 背景图片地址
    addText($form, 'bgImage', '/assets/images/bg.jpg', '背景图片 URL', '/assets/images/bg.jpg');

    // 背景颜色代码
    addText($form, 'bgColor', '#f0f0f0', '背景颜色代码', '请输入 Hex 颜色值，例如 #f0f0f0');

    // 访问统计开关
    addSelect($form, 'counterEnabled', array(
        '0' => _t('关闭'),
        '1' => _t('开启'),
    ), '0', '访问统计', '开启后显示今日/昨日/总访问统计，数据以 SQL 写入站点数据库（首次访问自动建表）');

    // 统计插入位置
    addSelect($form, 'counterPosition', array(
        'side' => _t('侧边栏'),
        'bottom-right' => _t('悬浮右下'),
        'bottom-left' => _t('悬浮左下'),
        'top-right' => _t('悬浮右上'),
        'top-left' => _t('悬浮左上'),
    ), 'side', '统计插入位置', '侧边栏：内联显示在侧边栏顶部；悬浮：固定在视口对应角落（右下会自动避开返回顶部按钮）');

    // 统计自动刷新
    addSelect($form, 'counterRefresh', array(
        '0' => _t('关闭'),
        '10' => _t('每 10 秒'),
        '30' => _t('每 30 秒'),
        '60' => _t('每 60 秒'),
        '300' => _t('每 5 分钟'),
    ), '0', '统计自动刷新', '开启后页面定时通过轻量 JSON 接口拉取最新访问数并滚动更新（轮询本身不计入访问）');

    // 统计时区
    addSelect($form, 'counterTimezone', array(
        '8' => _t('北京时间 UTC+8'),
        '9' => _t('东京/首尔 UTC+9'),
        '7' => _t('曼谷/河内 UTC+7'),
        '5.5' => _t('印度 UTC+5.5'),
        '4' => _t('迪拜 UTC+4'),
        '3' => _t('莫斯科 UTC+3'),
        '2' => _t('雅典/开罗 UTC+2'),
        '1' => _t('柏林/巴黎 UTC+1'),
        '0' => _t('世界协调时 UTC±0'),
        '-3' => _t('圣保罗 UTC-3'),
        '-5' => _t('纽约 UTC-5'),
        '-8' => _t('洛杉矶 UTC-8'),
    ), '8', '统计时区', '访问统计的"今天/昨天"按此时区翻转，默认北京时间（不受服务器时区影响）');

    // ================================ 主页设置 ================================
    addTitle($form, '主页设置');

    // 性别
    addSelect($form, 'headerGender', array(
        'male' => _t('男'),
        'female' => _t('女'),
        'none' => _t('不显示'),
    ), 'male', '性别', '');

    // 卡片头像链接
    addText($form, 'headerAvatarUrl', 'example@example.com', '卡片头像链接', '输入图片链接或者邮箱地址，如果是邮箱地址，会使用头像源获取。');

    // 头部图片
    addText($form, 'headerImage', '/assets/images/card-bg.jpg', '卡片背景图片链接', '默认：/assets/images/card-bg.jpg');

    // 侧边栏统计
    addCheckbox($form, 'sidebarStat', array(
        'posts' => _t('文章总数'),
        'comments' => _t('评论总数'),
        'tags' => _t('标签总数'),
        'categories' => _t('分类总数'),
    ), array('posts', 'comments', 'tags'), '侧边栏统计', '勾选后，会在侧边栏显示统计数据（推荐勾选3个）');

    // 侧边栏资料
    addCheckbox($form, 'sidebarInfo', array(
        'hometown' => _t('家乡'),
        'email' => _t('邮箱'),
        'birthday' => _t('生日'),
        'intro' => _t('简介'),
        'links' => _t('链接'),
    ), array('hometown', 'email', 'birthday', 'intro', 'links'), '侧边栏资料', '勾选后，会在侧边栏显示资料');

    // 邮箱
    addText($form, 'email', NULL, '邮箱', '');
    // 家乡
    addText($form, 'hometown', NULL, '家乡', '');
    // 生日
    addText($form, 'birthday', NULL, '生日', '');
    // 简介
    addText($form, 'intro', NULL, '简介', '');
    // 链接
    addText($form, 'links', NULL, '链接', '');

    // 侧边栏最近评论
    addCheckbox($form, 'sidebarRecentComments', array(
        'comments' => _t('最近评论'),
    ), array('comments'), '侧边栏最近评论', '勾选后，会在侧边栏显示最近评论');

    // 侧边栏最近评论数量
    addText($form, 'sidebarRecentCommentsNum', 5, '侧边栏最近评论数量', '侧边栏最近评论数量，默认为5');

    // ================================ 友链页设置 ================================
    addTitle($form, '友链页设置');
    addSubTitle($form, '仅在使用「友链」自定义模板的独立页中生效');
    // 友链列表：每行一条的多行内容，改用 textarea（rows=8）加高输入框，字段名与存储不变
    $friendLinks = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'friendLinks',
        null,
        '',
        _t('友链列表'),
        _t('每行一条，格式：名称|链接 或 名称|链接|描述 或 名称|链接|描述|LOGO地址。例如：<br>博客A|https://example.com|一个博客|https://example.com/logo.png<br>博客B|https://b.com')
    );
    $friendLinks->input->setAttribute('rows', 8);
    $form->addInput($friendLinks);

    // setConfigLayoutFooter();
    setStyle();
    setScript();
}

/**
 * 按邮箱获取头像 URL，同邮箱只计算一次
 *
 * @param string $mail 作者邮箱
 * @param mixed $options 主题选项（含 avatarUrl）
 * @return string 头像 URL
 */
function getAuthorAvatar($mail, $options)
{
    static $cache = [];
    if (isset($cache[$mail])) {
        return $cache[$mail];
    }
    $avatarUrl = $options->avatarUrl;
    if ($avatarUrl == 'loli') {
        $url = 'https://gravatar.loli.net/avatar/' . md5($mail) . '?s=128&r=X';
    } else if ($avatarUrl == 'gravatar') {
        $url = 'https://gravatar.com/avatar/' . md5($mail) . '?s=128&r=X';
    } else if ($avatarUrl == 'cravatar') {
        $url = 'https://cravatar.com/avatar/' . md5($mail) . '?s=128&r=X&d=mp';
    } else if ($avatarUrl == 'weavatar') {
        $url = 'https://weavatar.com/avatar/' . md5($mail) . '?s=128&r=X&d=mp';
    } else {
        // lty（默认）
        $url = 'https://api.lty.fun/avatar/' . md5($mail) . '?s=128&r=X';
    }
    $cache[$mail] = $url;
    return $url;
}

// 设置配置布局头部结构（预留）
function setConfigLayoutHeader()
{
    ?>

    <div class="config-content">
        <?php
}

// 设置配置布局尾部结构（预留）
function setConfigLayoutFooter()
{
    ?>

    </div>
    <?php
}

/**
 * 设置样式
 */
function setStyle()
{
    ?>
    <style>
        .typecho-page-main {
            flex-wrap: nowrap;
            justify-content: space-between;
        }

        .typecho-page-main h2 {
            border-bottom: 2px solid #467b96;
            padding-bottom: 10px;
            margin-top: 40px;
            color: #467b96;
        }

        /* 侧边栏容器 */
        .config-sidebar {
            min-width: 120px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            min-height: 120px;
            height: min-content;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 200px;
        }

        .config-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .config-sidebar li {
            margin-bottom: 8px;
        }

        .config-sidebar a {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            display: block;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .config-sidebar a:hover {
            background-color: #f3f3f3;
            color: #467b96;
        }

        .config-sidebar a.active {
            background-color: #467b96;
            color: #fff;
            font-weight: bold;
        }
    </style>
    <?php
}

/**
 * 设置脚本
 */
function setScript()
{
    ?>
    <script>
        window.onload = function () {
            var main = document.querySelector('.typecho-page-main');

            if (main) {
                var sidebar = document.createElement('div');
                sidebar.className = 'config-sidebar';
                sidebar.innerHTML = '<ul id="config-nav"></ul>';
                main.appendChild(sidebar);
            }


            // 背景模式切换
            var selector = document.getElementsByName('bgMode')[0];
            var imgContainer = document.getElementsByName('bgImage')[0]?.closest('li');
            var colorContainer = document.getElementsByName('bgColor')[0]?.closest('li');
            function updateVisibility() {
                if (!selector) return;
                if (selector.value === 'image') {
                    if (imgContainer) imgContainer.style.display = '';
                    if (colorContainer) colorContainer.style.display = 'none';
                } else {
                    if (imgContainer) imgContainer.style.display = 'none';
                    if (colorContainer) colorContainer.style.display = '';
                }
            }
            if (selector) selector.onchange = updateVisibility;
            updateVisibility();

            // 侧边栏
            const nav = document.getElementById('config-nav');
            const headers = document.querySelectorAll('.typecho-page-main h2');

            headers.forEach((header, index) => {
                const id = 'section-' + index;
                header.setAttribute('id', id);

                const li = document.createElement('li');
                li.innerHTML = `<a href="#${id}">${header.innerText}</a>`;
                li.onclick = (e) => {
                    e.preventDefault();
                    header.scrollIntoView({ behavior: 'smooth' });
                };
                nav.appendChild(li);
            });

            // 快捷键保存
            document.addEventListener('keydown', function (e) {
                if ((e.ctrlKey || e.metaKey) && e.keyCode === 83) {
                    e.preventDefault();
                    // 提交
                    const submitBtn = document.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        // submitBtn.innerText = "正在保存...";
                        // submitBtn.style.opacity = "0.7";
                        submitBtn.click();
                    }
                }
            });
        };
    </script>
    <?php
}

/**
 * 添加标题
 *
 * @param \Typecho\Widget\Helper\Form $form 表单对象
 * @param string $title 标题文字
 */
function addTitle($form, $title = '')
{
    $layout = new \Typecho\Widget\Helper\Layout('h2');
    $layout->html(_t($title));
    $form->addItem($layout);
}

/**
 * 添加子标题
 * 
 * @param \Typecho\Widget\Helper\Form $form 表单对象
 * @param string $title 标题文字
 */
function addSubTitle($form, $title = '')
{
    $layout = new \Typecho\Widget\Helper\Layout('h3');
    $layout->html(_t($title));
    $form->addItem($layout);
}

/**
 * 添加选择框
 * 
 * @param Typecho_Widget_Helper_Form $form 表单对象
 * @param string $name 字段名
 * @param array $options 选项数组
 * @param string $default 默认值
 * @param string $title 标题
 * @param string $description 描述
 */
function addSelect($form, $name = '', $options = [], $default = '', $title = '', $description = '')
{
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Select(
        $name,
        $options,
        $default,
        _t($title),
        _t($description)
    ));
}

/**
 * 添加文本框
 * 
 * @param Typecho_Widget_Helper_Form $form 表单对象
 * @param string $name 字段名
 * @param string $default 默认值
 * @param string $title 标题
 * @param string $description 描述
 */
function addText($form, $name = '', $default = '', $title = '', $description = '')
{
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text(
        $name,
        null,
        $default,
        _t($title),
        _t($description)
    ));
}

/**
 * 添加复选框
 * 
 * @param Typecho_Widget_Helper_Form $form 表单对象
 * @param string $name 字段名
 * @param array $options 选项数组 [value => label]
 * @param array $default 默认值(注意是数组)
 * @param string $title 标题
 * @param string $description 描述
 */
function addCheckbox($form, $name = '', $options = [], $default = '', $title = '', $description = '')
{
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Checkbox(
        $name,
        $options,
        $default,
        _t($title),
        _t($description)
    ));
}

/**
 * 获取 SVG 文件内容
 * 
 * @param string $name SVG 文件名
 * @param string $class SVG 类名
 * @return string SVG 文件内容
 */
function getSvg($name, $class = '')
{
    $file = dirname(__FILE__) . '/assets/icons/' . $name . '.svg';
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // 如果传入了 class，则注入到 svg 标签中
        if ($class) {
            $content = str_replace('<svg', '<svg class="' . $class . '"', $content);
        }
        return $content;
    }
    return '';
}

/**
 * 获取图片 URL
 * @param string $path 原始路径字符串
 * @return string 图片 URL
 */
function getImageUrl($path)
{
    $path = (string) $path; // 防止空值
    if (preg_match('/^https?:\/\/|^\/\//i', $path)) {
        return $path;
    }
    $options = Helper::options();
    if (strpos($path, 'usr/uploads/') === 0) {
        return $options->siteUrl($path);
    }
    return $options->themeUrl($path);
}

/**
 * 获取文章内容中的所有图片
 * 
 * @param string $content 文章内容
 * @return array 图片列表
 */
function getAllImages($content)
{
    $imgList = array();

    preg_match_all("/<img.*?src=\"(.*?)\".*?>/i", $content, $matches);
    if (isset($matches[1])) {
        $imgList = $matches[1];
    }
    return $imgList;
}

/**
 * 主题初始化：开启访问统计时注册访问记录钩子
 *
 * @param mixed $archive Widget_Archive 实例
 * @return void
 */
function themeInit($archive)
{
    // 注意：themeInit 运行在普通函数作用域，$archive->options 是 protected 属性，
    // 从外部访问会经 __get 魔术方法返回 NULL，必须用 Helper::options() 获取共享配置
    $options = Helper::options();
    if ($options->counterEnabled == '1') {
        // 轻量 JSON 统计接口：在 header 钩子注册前拦截并退出，轮询本身不计入访问
        if (isset($_GET['counter_stats']) && 'json' === $_GET['counter_stats']) {
            counterOutputStatsJson();
        }
        Typecho_Plugin::factory('Widget\Archive')->header = 'recordCounterVisit';
    }
}

/**
 * 记录访问并缓存统计数据（挂载于 Widget\Archive:header 钩子，仅 HTML 页面触发）
 *
 * 数据以 SQL 写入站点数据库：
 *   {prefix}counter_visits —— 访客明细（站点+日期+访客指纹 唯一去重，仅保留 90 天）
 *   {prefix}counter_stats  —— 各站点总访问累计
 * 同一访客（IP+UA 指纹）同一天只计一次；常见爬虫 UA 不计入。
 * 统计结果存入 $GLOBALS 供 renderCounterBar() 渲染，失败时静默跳过不影响页面。
 *
 * @return void
 */
function recordCounterVisit()
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    try {
        counterDoVisit();
    } catch (Exception $e) {
        // 表可能尚未创建：建表后重试一次，仍失败则放弃统计
        try {
            counterCreateTables();
            counterDoVisit();
        } catch (Exception $e) {
        }
    }
}

/**
 * 统计时区偏移小时数（后台主题设置 counterTimezone，默认北京时间 UTC+8）
 *
 * @return float
 */
function counterTimezoneOffset()
{
    static $offset = null;
    if ($offset === null) {
        $value = null;
        try {
            $value = Helper::options()->counterTimezone;
        } catch (Exception $e) {
        }
        // 未保存过设置时为 null，回落到北京时间；'0' 是合法的 UTC±0，不可当作空值
        $offset = ($value === null || $value === '') ? 8.0 : (float) $value;
    }
    return $offset;
}

/**
 * 按后台设置的统计时区计算日期键（默认北京时间 UTC+8，无夏令时）
 *
 * 统计的"今天/昨天"必须按所选时区翻转；海外主机 PHP 默认时区多为 UTC，
 * 直接 date('Y-m-d') 会导致日期在北京时间早上 8 点才翻转。
 *
 * @param int $offsetDays 相对今天的偏移天数（昨天为 -1）
 * @return string Y-m-d 格式日期
 */
function counterBeijingDate($offsetDays = 0)
{
    return gmdate('Y-m-d', time() + counterTimezoneOffset() * 3600 + $offsetDays * 86400);
}

/**
 * 执行一次访问记录与统计查询
 *
 * @return void
 * @throws Exception
 */
function counterDoVisit()
{
    $db = Typecho_Db::get();
    $site = 'default';
    $today = counterBeijingDate();
    $yesterday = counterBeijingDate(-1);

    // 访客指纹：客户端 IP（取第一个合法 XFF）+ UA + 站点标识
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $candidate = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        if (filter_var($candidate, FILTER_VALIDATE_IP)) {
            $ip = $candidate;
        }
    }
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/bot|spider|crawler|slurp|curl|wget|python|headless|phantom/i', $ua)) {
        return;
    }
    $visitorHash = substr(hash('sha256', $ip . '|' . $ua . '|' . $site), 0, 16);

    $visitsTable = '`' . $db->getPrefix() . 'counter_visits`';
    $statsTable = '`' . $db->getPrefix() . 'counter_stats`';
    $now = time();

    // 唯一键去重：同访客同日仅插入一次；rowCount>0 表示今天首次来访
    $inserted = $db->query(
        "INSERT IGNORE INTO {$visitsTable} (`site`, `date`, `visitor_hash`, `created_at`)
         VALUES ('{$site}', '{$today}', '{$visitorHash}', {$now})",
        Typecho_Db::WRITE,
        Typecho_Db::UPDATE
    );

    if ($inserted > 0) {
        // 累计总数；UPDATE 影响 0 行说明站点首次来访，插入初始行
        $updated = $db->query(
            "UPDATE {$statsTable} SET `total` = `total` + 1, `updated_at` = {$now} WHERE `site` = '{$site}'",
            Typecho_Db::WRITE,
            Typecho_Db::UPDATE
        );
        if (0 == $updated) {
            $db->query(
                "INSERT INTO {$statsTable} (`site`, `total`, `updated_at`) VALUES ('{$site}', 1, {$now})",
                Typecho_Db::WRITE,
                Typecho_Db::INSERT
            );
        }
    }

    // 约 10% 概率清理 90 天前明细
    if (mt_rand(1, 100) <= 10) {
        $cutoff = counterBeijingDate(-90);
        $db->query(
            "DELETE FROM {$visitsTable} WHERE `site` = '{$site}' AND `date` < '{$cutoff}'",
            Typecho_Db::WRITE,
            Typecho_Db::DELETE
        );
    }

    // 一条 SQL 同时统计今日/昨日
    $GLOBALS['sagittariusCounterStats'] = counterQueryStats($db, $site);
}

/**
 * 查询当前统计数据（今日/昨日/近7天/近30天明细计数 + 累计总数）
 *
 * 近 N 天按自然日计，含今天（如近7天 = 今天往前共 7 个自然日）。
 *
 * @param Typecho_Db $db 数据库对象
 * @param string $site 站点标识
 * @return array array('today' => int, 'yesterday' => int, 'week' => int, 'month' => int, 'total' => int)
 */
function counterQueryStats($db, $site)
{
    $today = counterBeijingDate();
    $yesterday = counterBeijingDate(-1);
    $weekStart = counterBeijingDate(-6);
    $monthStart = counterBeijingDate(-29);

    // 一次范围扫描同时算出 4 项明细计数（Y-m-d 字符串比较即日期比较）
    $row = $db->fetchRow(
        "SELECT COUNT(CASE WHEN `date` = '{$today}' THEN 1 END) AS `today_cnt`,
                COUNT(CASE WHEN `date` = '{$yesterday}' THEN 1 END) AS `yesterday_cnt`,
                COUNT(CASE WHEN `date` >= '{$weekStart}' THEN 1 END) AS `week_cnt`,
                COUNT(CASE WHEN `date` >= '{$monthStart}' THEN 1 END) AS `month_cnt`
         FROM `{$db->getPrefix()}counter_visits` WHERE `site` = '{$site}' AND `date` >= '{$monthStart}'"
    );
    $totalRow = $db->fetchRow(
        "SELECT `total` FROM `{$db->getPrefix()}counter_stats` WHERE `site` = '{$site}'"
    );

    return array(
        'today' => (int) ($row['today_cnt'] ?? 0),
        'yesterday' => (int) ($row['yesterday_cnt'] ?? 0),
        'week' => (int) ($row['week_cnt'] ?? 0),
        'month' => (int) ($row['month_cnt'] ?? 0),
        'total' => (int) ($totalRow['total'] ?? 0),
    );
}

/**
 * 输出访问统计 JSON（?counter_stats=json，供前端定时刷新拉取）
 *
 * 由 themeInit 在 header 钩子注册前调用并 exit，因此轮询请求不会执行页面渲染、
 * 也不会触发访问记录。表未创建或查询失败时返回全 0；带 no-store 避免 CDN 缓存旧值。
 *
 * @return void
 */
function counterOutputStatsJson()
{
    $stats = array('today' => 0, 'yesterday' => 0, 'total' => 0);
    try {
        $stats = counterQueryStats(Typecho_Db::get(), 'default');
    } catch (Exception $e) {
    }

    @header('Content-Type: application/json; charset=UTF-8');
    @header('Cache-Control: no-store');
    echo json_encode($stats);
    exit;
}

/**
 * 创建访问统计所需数据表（MySQL）
 *
 * @return void
 * @throws Exception
 */
function counterCreateTables()
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    $db->query("CREATE TABLE IF NOT EXISTS `{$prefix}counter_visits` (
        `vid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `site` VARCHAR(64) NOT NULL DEFAULT '',
        `date` VARCHAR(10) NOT NULL DEFAULT '',
        `visitor_hash` VARCHAR(16) NOT NULL DEFAULT '',
        `created_at` INT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`vid`),
        UNIQUE KEY `uk_site_date_visitor` (`site`, `date`, `visitor_hash`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS `{$prefix}counter_stats` (
        `site` VARCHAR(64) NOT NULL,
        `total` BIGINT UNSIGNED NOT NULL DEFAULT 0,
        `updated_at` INT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`site`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

/**
 * 渲染访问统计条 HTML（数字由 app.js 读取 data-value 后做滚动动画；
 * 明暗配色由主题 CSS 按html[data-theme="dark"]自动切换）
 *
 * @param string $position 插入位置：side / bottom-right / bottom-left / top-right / top-left
 * @return string 统计条 HTML
 */
function renderCounterBar($position = 'side')
{
    $stats = $GLOBALS['sagittariusCounterStats']
        ?? array('today' => 0, 'yesterday' => 0, 'week' => 0, 'month' => 0, 'total' => 0);

    if (in_array($position, array('bottom-right', 'bottom-left', 'top-right', 'top-left'), true)) {
        $class = 'vc-bar vc-floating vc-' . $position;
    } else {
        $class = 'vc-bar vc-inline';
    }

    // 自动刷新：间隔与 JSON 接口地址由 app.js 读取
    $refresh = (int) Helper::options()->counterRefresh;
    $refreshAttr = '';
    if ($refresh > 0) {
        $endpoint = rtrim(Helper::options()->siteUrl, '/') . '/?counter_stats=json';
        $refreshAttr = ' data-refresh="' . $refresh . '" data-endpoint="' . htmlspecialchars($endpoint) . '"';
    }

    // 近 7 天/近 30 天在鼠标悬停气泡中展示；数字复用 .vc-number（app.js 自动构建动画并参与轮询刷新）
    return '<div class="' . $class . '" id="vc-counter"' . $refreshAttr . '>'
        . '<span class="vc-dot"></span>'
        . '<div class="vc-item"><span class="vc-label">今日访问</span><span class="vc-number" data-key="today" data-value="' . (int) $stats['today'] . '"></span></div>'
        . '<div class="vc-item"><span class="vc-label">昨日访问</span><span class="vc-number" data-key="yesterday" data-value="' . (int) $stats['yesterday'] . '"></span></div>'
        . '<div class="vc-item"><span class="vc-label">总访问</span><span class="vc-number" data-key="total" data-value="' . (int) $stats['total'] . '"></span></div>'
        . '<div class="vc-pop" aria-hidden="true">'
        . '<div class="vc-pop-row"><span class="vc-label">近 7 天</span><span class="vc-number" data-key="week" data-value="' . (int) ($stats['week'] ?? 0) . '"></span></div>'
        . '<div class="vc-pop-row"><span class="vc-label">近 30 天</span><span class="vc-number" data-key="month" data-value="' . (int) ($stats['month'] ?? 0) . '"></span></div>'
        . '</div>'
        . '</div>';
}
/**
 * 从已渲染的文章内容中提取第一个视频播放器（VideoCollector）HTML 块
 *
 * 内容经过插件链后 [play] 短代码已转换为 .play-container 播放器 HTML，
 * 按 div 开闭标签配对扫描，得到结构完整的第一个容器块。
 *
 * @param string $content 已渲染的文章内容
 * @return string 播放器 HTML，不存在时返回空字符串
 */
function getPlayerHtml($content)
{
    $pos = strpos($content, '<div class="play-container"');
    if ($pos === false) {
        return '';
    }

    $depth = 0;
    $offset = $pos;
    $len = strlen($content);
    while ($offset < $len) {
        $open = strpos($content, '<div', $offset);
        $close = strpos($content, '</div>', $offset);
        if ($close === false) {
            break;
        }
        if ($open !== false && $open < $close) {
            $depth++;
            $offset = $open + 4;
        } else {
            $depth--;
            $offset = $close + 6;
            if ($depth === 0) {
                return substr($content, $pos, $offset - $pos);
            }
        }
    }
    return '';
}

/**
 * 获取首页展示用的播放器 HTML：只保留播放器本身，去掉分集切换按钮
 *
 * 摘要区不需要分集切换，分集请进入文章页操作。
 * 注意：摘要清理（getPostExcerpt）依赖 getPlayerHtml 返回与原文
 * 完全一致的字符串，因此不能直接修改 getPlayerHtml 的返回值。
 *
 * @param string $content 已渲染的文章内容
 * @return string 播放器 HTML，不存在时返回空字符串
 */
function getPostPlayerHtml($content)
{
    $player = getPlayerHtml($content);
    if ('' === $player) {
        return '';
    }
    // video-tabs 内只有 span，无嵌套 div，非贪婪匹配可安全剔除
    return preg_replace('/<div class="video-tabs">.*?<\/div>/s', '', $player);
}

/**
 * 获取首页摘要 HTML（保留行内文字式样）
 *
 * 内容含播放器时先剔除播放器 HTML，再按 <!--more--> 截取，
 * 避免播放器内的分集标题、影片名等文字漏入摘要。
 * 只保留加粗、斜体、下划线、删除线、行内代码、span（含行内样式）、
 * 换行等行内式样标签，块级标签转为换行；文本超长时按可见字符截断
 * 并补齐闭合标签，避免截断破坏 HTML 结构。
 *
 * @param string $content 已渲染的文章内容
 * @param integer $length 摘要文本长度
 * @param string $trim 摘要后缀
 * @return string 摘要 HTML
 */
function getPostExcerpt($content, $length = 120, $trim = '...')
{
    $playerHtml = getPlayerHtml($content);
    if ($playerHtml !== '') {
        $content = str_replace($playerHtml, '', $content);
    }

    $parts = explode('<!--more-->', $content);
    $html = trim($parts[0]);
    if ('' === $html) {
        return '';
    }

    // 脚本、样式块整体剔除；块级标签结尾转为换行，保留段落感
    $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1\s*>#is', '', $html);
    $html = preg_replace('#</(?:p|div|h[1-6]|li|blockquote|pre)>#i', '<br>', $html);

    // 仅保留行内文字式样标签，其余标签剔除但保留其内文本
    $allowed = '<b><strong><i><em><u><s><del><strike><code><mark><span><sub><sup><small><br>';
    $html = trim(strip_tags($html, $allowed));
    if ('' === $html) {
        return '';
    }

    // 按标签切分，统计可见字符数截断，避免截在标签中间或截出未闭合标签
    $tokens = preg_split('#(<[^>]*>)#', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $result = '';
    $openTags = [];
    $count = 0;
    $truncated = false;

    foreach ($tokens as $token) {
        if ('' === $token) {
            continue;
        }
        if ('<' === $token[0]) {
            $result .= $token;
            if ('</' === substr($token, 0, 2)) {
                if (preg_match('#^</([a-zA-Z][a-zA-Z0-9-]*)#', $token, $m)) {
                    $pos = array_search(strtolower($m[1]), $openTags);
                    if (false !== $pos) {
                        array_splice($openTags, $pos);
                    }
                }
            } elseif (preg_match('#^<([a-zA-Z][a-zA-Z0-9-]*)#', $token, $m)) {
                $tag = strtolower($m[1]);
                // br 与自闭合标签无需配对闭合
                if ('br' !== $tag && !preg_match('#/>\s*$#', $token)) {
                    $openTags[] = $tag;
                }
            }
            continue;
        }

        $charCount = preg_match_all('#.#us', $token);
        if ($count + $charCount <= $length) {
            $result .= $token;
            $count += $charCount;
            continue;
        }

        $remaining = $length - $count;
        if ($remaining > 0 && preg_match('#^(?:.){1,' . $remaining . '}#us', $token, $m)) {
            $result .= $m[0];
        }
        $truncated = true;
        break;
    }

    if ($truncated) {
        $result .= $trim;
    }
    foreach (array_reverse($openTags) as $tag) {
        $result .= '</' . $tag . '>';
    }

    // 去掉首尾多余换行
    $result = preg_replace('#^(?:\s*<br>\s*)+#', '', $result);
    $result = preg_replace('#(?:\s*<br>\s*)+$#', '', $result);

    return $result;
}

/**
 * OwO 表情渲染：把评论中的 {:表情码:} 替换为 assets/OwO.json 中对应的 <img>
 * （码表来自主题自带 JSON，替换 HTML 为白名单内容，无注入风险）
 * @param string $html 已转义输出的评论文本 HTML
 * @return string
 */
function sagittariusOwOReplace($html)
{
    static $map = null;
    if (null === $map) {
        $map = array();
        $file = __DIR__ . '/assets/OwO.json';
        if (is_readable($file)) {
            $data = json_decode((string) file_get_contents($file), true);
            if (is_array($data)) {
                foreach ($data as $group) {
                    if (empty($group['container']) || !is_array($group['container'])) {
                        continue;
                    }
                    foreach ($group['container'] as $item) {
                        if (!empty($item['text']) && !empty($item['icon'])) {
                            $map[$item['text']] = $item['icon'];
                        }
                    }
                }
            }
        }
    }
    if (!$map) {
        return $html;
    }
    return preg_replace_callback('/\{:([^:{}\s]{1,100}):\}/', function ($m) use ($map) {
        return isset($map[$m[1]]) ? $map[$m[1]] : $m[0];
    }, $html);
}

/**
 * 嵌套评论
 * @param mixed $comments
 * @param mixed $options
 * @return void
 */
function threadedComments($comments, $options)
{
    $coid = $comments->coid;
    $author = htmlspecialchars($comments->author);
    // 评论者网址：无协议时补 http://，同时拦截 javascript: 等非法协议
    $authorUrl = trim((string) $comments->url);
    if ('' !== $authorUrl && !preg_match('#^https?://#i', $authorUrl)) {
        $authorUrl = 'http://' . $authorUrl;
    }
    $authorUrl = htmlspecialchars($authorUrl);
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';  // 博主评论样式
        } else {
            $commentClass .= ' comment-by-user';    // 注册用户样式
        }
    }

    ?>
    <li id="li-<?php $comments->theId(); ?>" class="comment__item<?php
      if ($comments->levels > 0) {
          echo ' child';
          $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
      } else {
          echo ' parent';
      }
      $comments->alt(' comment-odd', ' comment-even');
      echo $commentClass;
      ?>">
        <div class="comment__view">
            <img class="comment__avatar" src="<?php echo htmlspecialchars(getAuthorAvatar($comments->mail, $options)); ?>"
                alt="<?php echo $author; ?>">
            <div class="comment__content">
                <div class="comment__meta">
                    <div class="comment__author"><?php if ('' !== $authorUrl): ?><a
                            href="<?php echo $authorUrl; ?>" target="_blank" rel="external nofollow"><?php echo $author; ?></a><?php else: ?><?php echo $author; ?><?php endif; ?>
                    </div>
                    <div class="meta__date" time="<?php echo $comments->created; ?>"><?php $comments->date('Y-m-d H:i'); ?>
                    </div>
                </div>
                <div class="comment__text">
                    <?php ob_start();
                    $comments->text();
                    echo sagittariusOwOReplace(ob_get_clean()); ?>
                </div>
                <div class="comment__actions" data-author="<?php echo $author; ?>" data-coid="<?php echo $coid; ?>"
                    data-html-id="">
                    <button class="comment__reply" type="button" data-coid="<?php echo $coid; ?>" data-author="<?php echo $author; ?>"><?php _e('回复'); ?></button>
                </div>
                <?php if ($comments->children) { ?>
                    <div class="comment__children">
                        <?php $comments->threadedComments($options); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </li>
<?php }

/**
 * 获取文章点赞数（agree 自定义字段）
 *
 * @param mixed $archive 文章组件
 * @return integer 点赞数
 */
function getAgreeNum($archive)
{
    $db = Typecho_Db::get();
    $field = $db->fetchRow($db->select('str_value')->from('table.fields')->where('cid = ?', $archive->cid)->where('name = ?', 'agree'));
    return $field ? (int) $field['str_value'] : 0;
}

/**
 * 输出浏览量（单篇文章页面自动计数，Cookie 防重复）
 *
 * @param mixed $widget 文章组件
 * @return void
 */
function viewsNum($widget)
{
    $db = Typecho_Db::get();
    $cid = intval($widget->cid);

    $field = $db->fetchRow(
        $db->select('str_value')->from('table.fields')
            ->where('cid = ?', $cid)->where('name = ?', 'views')
    );
    $views = $field ? intval($field['str_value']) : 0;

    if ($widget->is('single')) {
        $vieweds = \Typecho\Cookie::get('contents_viewed');
        $vieweds = empty($vieweds) ? [] : explode(',', $vieweds);

        if (!in_array(strval($cid), $vieweds)) {
            $views++;
            if (!$field) {
                $db->query($db->insert('table.fields')->rows([
                    'cid' => $cid, 'name' => 'views', 'type' => 'str',
                    'str_value' => strval($views),
                    'int_value' => 0, 'float_value' => 0,
                ]));
            } else {
                $db->query(
                    $db->update('table.fields')
                        ->rows(['str_value' => strval($views)])
                        ->where('cid = ?', $cid)->where('name = ?', 'views')
                );
            }
            $vieweds[] = strval($cid);
            \Typecho\Cookie::set('contents_viewed', implode(',', $vieweds));
        }
    }

    echo $views;
}

/**
 * 解析主题配置中的友链列表，供友链独立页使用
 * 格式：每行一条，名称|链接 或 名称|链接|描述 或 名称|链接|描述|LOGO地址
 *
 * @return array 元素为 ['name' => string, 'url' => string, 'desc' => string, 'logo' => string]
 */
function getFriendLinks()
{
    $options = Helper::options();
    $raw = $options->friendLinks;
    if (empty($raw) || !is_string($raw)) {
        return array();
    }
    $lines = array_filter(array_map('trim', explode("\n", $raw)));
    $list = array();
    foreach ($lines as $line) {
        $parts = array_map('trim', explode('|', $line, 4));
        if (count($parts) >= 2 && $parts[0] !== '' && $parts[1] !== '') {
            $list[] = array(
                'name'  => $parts[0],
                'url'   => $parts[1],
                'desc'  => isset($parts[2]) ? $parts[2] : '',
                'logo'  => isset($parts[3]) ? $parts[3] : '',
            );
        }
    }
    return $list;
}

/**
 * 获取评论接口 URL（根相对路径，避免跨域）
 *
 * @return string
 */
function getCommentsUrl()
{
    $root = rtrim(str_replace('\\', '/', realpath(__TYPECHO_ROOT_DIR__)), '/');
    $path = $root . '/usr/themes/' . Helper::options()->theme . '/action/comments.php';
    $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    return substr($path, strlen($docRoot));
}

/**
 * 获取点赞接口 URL（根相对路径，避免跨域）
 *
 * @return string
 */
function getLikeUrl()
{
    $root = rtrim(str_replace('\\', '/', realpath(__TYPECHO_ROOT_DIR__)), '/');
    $themeDir = $root . '/usr/themes/' . Helper::options()->theme . '/action/like.php';
    $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    return substr($themeDir, strlen($docRoot));
}