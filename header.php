<!DOCTYPE html>
<html lang="<?php echo $this->options->language; ?>"
      data-prismjs-copy="复制"
      data-prismjs-copy-success="已复制"
      data-prismjs-copy-error="Press Ctrl+C to copy">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script>
        (function () {
            try {
                var mode = localStorage.getItem('theme-mode');
                var dark = mode ? mode === 'dark' : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (dark) document.documentElement.setAttribute('data-theme', 'dark');
            } catch (e) {}
        })();
    </script>
    <title><?php $this->archiveTitle([
                'category' => _t('分类 %s 下的文章'),
                'search' => _t('包含关键字 %s 的文章'),
                'tag' => _t('标签 %s 下的文章'),
                'author' => _t('%s 发布的文章')
            ], '', ' - '); ?><?php $this->options->title(); ?></title>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('main.min.css'); ?>?v=<?php echo filemtime(__DIR__ . '/main.min.css'); ?>">
    <link rel="stylesheet" href="https://mirrors.sustech.edu.cn/cdnjs/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css">
    <script src="https://mirrors.sustech.edu.cn/cdnjs/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://mirrors.sustech.edu.cn/cdnjs/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    <script src="https://mirrors.sustech.edu.cn/cdnjs/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
    <script src="https://mirrors.sustech.edu.cn/cdnjs/ajax/libs/prism/1.29.0/plugins/toolbar/prism-toolbar.min.js"></script>
    <script src="https://mirrors.sustech.edu.cn/cdnjs/ajax/libs/prism/1.29.0/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js"></script>
    <script src="<?php $this->options->themeUrl('app.min.js'); ?>?v=<?php echo filemtime(__DIR__ . '/app.min.js'); ?>"></script>
    <?php $this->header(); ?>
</head>
<?php $bgModeClass = ($this->options->bgMode == 'image') ? 'bg-image-mode' : 'bg-color-mode'; ?>
<?php $counterClass = ($this->options->counterEnabled == '1' && $this->options->counterPosition == 'bottom-right') ? ' has-vc-counter' : ''; ?>
<?php if ($this->options->bgMode == 'image'): ?>
<body class="<?php echo $bgModeClass . $counterClass; ?>" style="background-image: url(<?php echo getImageUrl($this->options->bgImage ?? ''); ?>);">
<?php else: ?>
<body class="<?php echo $bgModeClass . $counterClass; ?>" style="background-color: <?php echo $this->options->bgColor ?? '#FFFFFF'; ?>;">
<?php endif; ?>