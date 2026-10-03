  <!-- 访问统计（悬浮模式） -->
  <?php if ($this->options->counterEnabled == '1' && $this->options->counterPosition != 'side'): ?>
      <?php echo renderCounterBar($this->options->counterPosition); ?>
  <?php endif; ?>
  <button id="back-top" class="back-top" type="button" aria-label="返回顶部" title="返回顶部">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round" aria-hidden="true">
          <path d="M12 19V5M5 12l7-7 7 7" />
      </svg>
  </button>
  <button id="theme-toggle" class="theme-toggle" type="button" aria-label="切换暗色模式" title="切换暗色模式">
      <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
      </svg>
      <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="4" />
          <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
      </svg>
  </button>
  <?php $this->footer(); ?>
</body>
</html>