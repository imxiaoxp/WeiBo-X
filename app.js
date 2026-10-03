function formatTime(e) {
  const t = Math.floor(Date.now() / 1e3) - e;
  if (t < 60) return "刚刚";
  if (t < 2592e3) {
    const e = [{
      label: "分钟前",
      value: 60
    }, {
      label: "小时前",
      value: 3600
    }, {
      label: "天前",
      value: 86400
    }].reverse().find(e => t >= e.value);
    return Math.floor(t / e.value) + e.label
  }
  return new Intl.DateTimeFormat("zh-CN", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    hour12: !1
  }).format(new Date(1e3 * e)).replace(/\//g, "-")
}

function formatPostDate(e) {
  document.querySelectorAll(".meta__date").forEach(e => {
    const t = parseInt(e.getAttribute("time"));
    isNaN(t) || (e.innerText = formatTime(t))
  })
}

function initTextareaHeight() {
  document.querySelectorAll(".comment__textarea,.inline-comments__textarea").forEach(e => {
    e.addEventListener("input", function() {
      this.style.height = "auto", this.style.height = this.scrollHeight + "px"
    })
  })
}

function initWaterfall() {
  const e = document.querySelector(".post__list"),
    t = document.querySelector("#load-indicator");
  if (!e || !t) return;
  let n = !1;
  const a = new IntersectionObserver(r => {
    r[0].isIntersecting && (() => {
      const r = t.querySelector("a.next");
      if (!r || n) return;
      n = !0;
      const i = t.querySelector(".load-indicator__text");
      i && (i.textContent = "加载中..."), fetch(r.href).then(e => {
        if (!e.ok) throw new Error("网络请求失败");
        return e.text()
      }).then(n => {
        const r = (new DOMParser).parseFromString(n, "text/html"),
          i = r.querySelectorAll(".post__list .post__item");
        if (i.length > 0) {
          const t = document.createDocumentFragment();
          i.forEach(e => t.appendChild(e.cloneNode(!0))), e.appendChild(t), formatPostDate(e), "function" == typeof initVideoCollectors && initVideoCollectors()
        }
        const o = r.querySelector("#load-indicator");
        o && o.querySelector("a.next") ? t.innerHTML = o.innerHTML : (t.innerHTML = '<span class="load-indicator__text"></span>', a.disconnect())
      }).catch(e => {
        i && (i.textContent = "加载失败，请重试")
      }).finally(() => {
        n = !1
      })
    })()
  }, {
    rootMargin: "200px"
  });
  a.observe(t)
}
const state = {
    parent: null,
    author: null
  },
  qs = e => document.querySelector(e);

function showCancel(e) {
  const t = qs(".comment__cancel");
  t && (t.style.display = e ? "block" : "none")
}

function setReply(e) {
  const t = e.target.dataset.coid,
    n = e.target.dataset.author;
  state.parent = t, state.author = n;
  const a = qs(".comment__form");
  if (!a) return;
  a.scrollIntoView({
    behavior: "smooth",
    block: "center"
  }), setTimeout(() => {
    const e = a.querySelector(".comment__textarea");
    e && e.focus()
  }, 100);
  const r = qs(".comment__textarea__tip");
  r && (r.innerHTML = `正在回复 @${n} 的评论`), showCancel(!0)
}

function resetReply() {
  state.parent = null, state.author = null;
  const e = qs(".comment__textarea__tip");
  e && (e.innerHTML = ""), showCancel(!1)
}
let commentSubmitting = !1;

async function submitComment() {
  const e = qs(".comment__form");
  if (!e || commentSubmitting) return;
  if (!e.reportValidity()) return;
  const t = qs(".comment__textarea");
  if (!t.value.trim()) return;
  const n = qs(".comment__submit"),
    a = qs(".comment__textarea__tip"),
    r = () => {
      n && (n.disabled = !0, n.textContent = "提交中…"), a && (a.style.color = "", a.innerHTML = "正在提交评论，请稍候…")
    },
    i = () => {
      commentSubmitting = !1, n && (n.disabled = !1, n.textContent = "提交评论")
    },
    o = e => {
      a && (a.style.color = "#e0245e", a.innerHTML = e)
    };
  commentSubmitting = !0, r();
  try {
    const n = e.dataset.action,
      a = new FormData(e);
    state.parent && a.append("parent", state.parent);
    const r = await fetch(n, {
        method: "POST",
        body: a,
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        }
      }),
      i = await r.text(),
      s = (new DOMParser).parseFromString(i, "text/html"),
      c = s.querySelector(".comment__wrapper");
    if (!c) {
      if (!r.ok) {
        const e = s.querySelector(".container") || s.body;
        o((e ? e.textContent.trim().replace(/\s+/g, " ") : "") || "评论提交失败（" + r.status + "），请稍后重试")
      } else o("评论提交失败，请稍后重试");
      return
    }
    const m = qs(".comment__wrapper");
    if (m && (m.innerHTML = c.innerHTML), formatPostDate(), state.parent) {
      const e = qs("#li-comment-" + state.parent);
      if (!e) return;
      e.scrollIntoView({
        behavior: "smooth",
        block: "center"
      })
    } else {
      const e = m.querySelectorAll(".comment__item"),
        t = e.length ? e[e.length - 1] : null;
      if (!t) return;
      t.scrollIntoView({
        behavior: "smooth",
        block: "center"
      })
    }
    resetReply(), t.value = ""
  } catch (e) {
    o("网络错误，评论未提交，请重试")
  } finally {
    i()
  }
}

function initCommentEvents() {
  document.addEventListener("click", e => {
    if (e.target.closest(".comment__reply")) setReply(e);
    else {
      if (!e.target.closest(".comment__cancel")) return e.target.closest(".comment__submit") ? (e.preventDefault(), void submitComment()) : void 0;
      resetReply()
    }
  })
}

function initThemeToggle() {
  const e = document.getElementById("theme-toggle"),
    t = document.documentElement;
  if (!e) return;
  const n = () => e.setAttribute("aria-label", "dark" === t.getAttribute("data-theme") ? "切换到浅色模式" : "切换到暗色模式");
  n();
  e.addEventListener("click", () => {
    const n = "dark" !== t.getAttribute("data-theme");
    n ? t.setAttribute("data-theme", "dark") : t.removeAttribute("data-theme");
    try {
      localStorage.setItem("theme-mode", n ? "dark" : "light")
    } catch (e) {}
    n()
  })
}

function initBackTop() {
  const e = document.getElementById("back-top");
  if (!e) return;
  const t = () => e.classList.toggle("show", window.scrollY > 300);
  window.addEventListener("scroll", t, {
    passive: !0
  }), t();
  e.addEventListener("click", () => window.scrollTo({
    top: 0,
    behavior: "smooth"
  }))
}

function initCounter() {
  const e = document.getElementById("vc-counter");
  if (!e) return;

  function n(s) {
    const r = document.createElement("span");
    r.className = "vc-strip";
    for (let i = 0; i < 10; i++) {
      const o = document.createElement("span");
      o.textContent = i;
      r.appendChild(o)
    }
    return r.style.transform = "translateY(-" + 10 * s + "%)", r
  }

  function renderNumber(el) {
    const str = String(parseInt(el.dataset.value, 10) || 0);
    el.innerHTML = "";
    for (let j = 0; j < str.length; j++) {
      const d = document.createElement("span");
      d.className = "vc-digit";
      d.appendChild(n(0));
      el.appendChild(d)
    }
    void el.offsetWidth;
    el.querySelectorAll(".vc-strip").forEach((strip, j) => {
      strip.style.transform = "translateY(-" + parseInt(str[j], 10) * 10 + "%)"
    })
  }

  e.querySelectorAll(".vc-number").forEach(renderNumber);

  const refresh = parseInt(e.dataset.refresh, 10) || 0;
  if (refresh > 0 && e.dataset.endpoint) {
    let timer = null;

    const fetchStats = () => {
      fetch(e.dataset.endpoint, { cache: "no-store" })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
          if (!data) return;
          e.querySelectorAll(".vc-number").forEach(el => {
            const v = parseInt(data[el.dataset.key], 10) || 0;
            if (String(v) !== el.dataset.value) {
              el.dataset.value = String(v);
              renderNumber(el)
            }
          })
        })
        .catch(() => {})
    };
    const stopPolling = () => {
      if (timer) {
        clearInterval(timer);
        timer = null
      }
    };
    const startPolling = () => {
      if (!timer) timer = setInterval(fetchStats, refresh * 1000)
    };

    startPolling();

    document.addEventListener("visibilitychange", () => {
      if (document.hidden) {
        stopPolling()
      } else {
        fetchStats();
        startPolling()
      }
    })
  }
}

function initTabScroll() {
  document.querySelectorAll(".tab-list-wrap").forEach(wrap => {
    const el = wrap.querySelector(".tab-list");
    if (!el) return;
    const update = () => {
      const overflow = el.scrollWidth > el.clientWidth + 1;
      const atEnd = el.scrollLeft + el.clientWidth >= el.scrollWidth - 1;
      wrap.classList.toggle("is-overflow", overflow && !atEnd)
    };
    el.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
    window.addEventListener("load", update);
    update()
  })
}

function initExternalLinks() {
  document.querySelectorAll(".post__content a[href]").forEach(a => {
    if (a.hostname && a.hostname !== location.hostname) {
      a.target = "_blank";
      a.rel = "noopener noreferrer"
    }
  })
}

// 点赞功能（localStorage 防重复点赞）
function initLikeButtons() {
  document.querySelectorAll(".like-btn").forEach(btn => {
    const cid = btn.dataset.cid;
    if (cid && localStorage.getItem("liked_" + cid)) {
      btn.classList.add("liked");
    }
  });
  document.addEventListener("click", e => {
    const btn = e.target.closest(".like-btn");
    if (!btn) return;
    const cid = btn.dataset.cid;
    const url = btn.dataset.url;
    if (!cid || !url || btn.dataset.busy) return;
    // 已点赞 → 取消；未点赞 → 点赞
    const liked = !!localStorage.getItem("liked_" + cid);
    if (!liked) {
      // 动画
      btn.classList.add("animate");
      setTimeout(() => btn.classList.remove("animate"), 300);
    }
    // 请求飞行中防连点（避免计数与状态错乱）
    btn.dataset.busy = "1";
    fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "cid=" + cid + "&cancel=" + (liked ? "1" : "0")
    })
      .then(res => res.json())
      .then(data => {
        if (data.count !== undefined) {
          const countEl = btn.querySelector(".like-btn__count");
          if (countEl) countEl.textContent = data.count;
          if (liked) {
            btn.classList.remove("liked");
            localStorage.removeItem("liked_" + cid);
          } else {
            btn.classList.add("liked");
            localStorage.setItem("liked_" + cid, "1");
          }
        }
      })
      .catch(() => { })
      .finally(() => { delete btn.dataset.busy; });
  });
}

// 首页内联评论
function initInlineComments() {
  // 同步卡片底部的评论数（data-count 由评论片段输出，为最新已审核评论数）
  function updateCommentCount(container) {
    const list = container.querySelector(".inline-comments__list");
    const card = container.closest(".post__item");
    if (!list || !card || list.dataset.count === undefined) return;
    const span = card.querySelector(".footer__comment span");
    if (span) span.textContent = list.dataset.count;
  }

  // 访客信息（页面会话级）：模态框确认后暂存，同页再次弹窗时自动预填
  const lastGuest = { author: "", mail: "", url: "" };

  // 访客信息模态框（单例，所有卡片共用）
  let guestModal = null;
  let pendingForm = null;

  function ensureGuestModal() {
    if (guestModal) return guestModal;
    guestModal = document.createElement("div");
    guestModal.className = "guest-modal";
    guestModal.hidden = true;
    guestModal.innerHTML =
      '<div class="guest-modal__mask"></div>' +
      '<div class="guest-modal__box">' +
      '<div class="guest-modal__title">评论者信息</div>' +
      '<input class="guest-modal__input" name="author" type="text" placeholder="称呼 *" autocomplete="name">' +
      '<input class="guest-modal__input" name="mail" type="email" placeholder="邮箱 *" autocomplete="email">' +
      '<input class="guest-modal__input" name="url" type="url" placeholder="网站（选填）" autocomplete="url">' +
      '<div class="guest-modal__actions">' +
      '<button type="button" class="guest-modal__btn" data-action="cancel">取消</button>' +
      '<button type="button" class="guest-modal__btn guest-modal__btn--primary" data-action="confirm">确定</button>' +
      "</div></div>";
    document.body.appendChild(guestModal);

    guestModal.addEventListener("click", e => {
      const action = e.target.closest("[data-action]")?.dataset.action;
      if (action === "cancel" || e.target.classList.contains("guest-modal__mask")) {
        closeGuestModal();
      } else if (action === "confirm") {
        confirmGuestModal();
      }
    });
    document.addEventListener("keydown", e => {
      if (e.key === "Escape" && guestModal && !guestModal.hidden) closeGuestModal();
    });
    return guestModal;
  }

  function closeGuestModal() {
    if (guestModal) guestModal.hidden = true;
    pendingForm = null;
  }

  function openGuestModal(form) {
    const modal = ensureGuestModal();
    pendingForm = form;
    ["author", "mail", "url"].forEach(name => {
      const input = modal.querySelector(`[name="${name}"]`);
      // 预填优先级：本页上次填写 > 表单里服务端记住的值
      input.value = lastGuest[name] || form.querySelector(`[name="${name}"]`)?.value || "";
    });
    modal.hidden = false;
    modal.querySelector('[name="author"]').focus();
  }

  function confirmGuestModal() {
    if (!pendingForm) {
      closeGuestModal();
      return;
    }
    const modal = guestModal;
    const author = modal.querySelector('[name="author"]').value.trim();
    const mail = modal.querySelector('[name="mail"]').value.trim();
    const url = modal.querySelector('[name="url"]').value.trim();
    if (!author) {
      alert("请填写称呼");
      return;
    }
    if (!mail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) {
      alert("请填写正确的邮箱地址");
      return;
    }

    Object.assign(lastGuest, { author, mail, url });
    ["author", "mail", "url"].forEach(name => {
      const field = pendingForm.querySelector(`[name="${name}"]`);
      if (field) field.value = lastGuest[name];
    });
    pendingForm.dataset.hasInfo = "1";
    const form = pendingForm;
    closeGuestModal();
    submitComment(form);
  }

  // 提交评论（调用前保证表单已含完整游客信息）
  function submitComment(form) {
    const textarea = form.querySelector(".inline-comments__textarea");
    if (!textarea || !textarea.value.trim()) return;

    const formData = new FormData(form);
    const action = form.action;
    const container = form.closest(".post__comments");
    const permalink = form.querySelector('[name="permalink"]')?.value || "";

    fetch(action, {
      method: "POST",
      body: formData,
      headers: { "X-Requested-With": "XMLHttpRequest" }
    })
      .then(res => {
        // JSON 响应 = 服务端返回的错误信息
        const ct = res.headers.get("content-type") || "";
        if (ct.includes("application/json")) {
          return res.json().then(d => ({ kind: "error", message: d.message || "评论提交失败" }));
        }
        // 302 跟随后的 HTML：落在文章页 = 成功，落在首页 = token 校验失败被弹回
        return res.text().then(() => {
          const final = decodeURIComponent(res.url || "");
          const ok = permalink && (final.includes(permalink) || final.includes(decodeURIComponent(permalink)));
          return ok ? { kind: "ok" } : { kind: "error", message: "评论提交失败，请刷新页面后重试" };
        });
      })
      .then(r => {
        if (r.kind !== "ok") {
          alert(r.message);
          return;
        }
        // 成功：重新加载评论列表
        if (container) {
          const cid = container.dataset.cid;
          const url = container.previousElementSibling
            ?.querySelector(".footer__comment")?.dataset.url;
          if (url && cid) {
            fetch(url + "?cid=" + cid)
              .then(r2 => r2.text())
              .then(html => {
                container.innerHTML = html;
                container.dataset.loaded = "1";
                initTextareaHeight();
                formatPostDate();
                // 同步更新卡片底部的评论数
                updateCommentCount(container);
                const fresh = container.querySelector(".inline-comments__textarea");
                if (fresh) fresh.value = "";
              });
          }
        }
      })
      .catch(() => {
        alert("评论提交失败，请稍后重试");
      });
  }

  document.addEventListener("click", e => {
    const btn = e.target.closest(".footer__comment");
    if (!btn) return;

    const cid = btn.dataset.cid;
    const url = btn.dataset.url;
    const postItem = btn.closest(".post__item");
    if (!postItem || !cid || !url) return;

    const container = postItem.querySelector(".post__comments");
    if (!container) return;

    // 切换显示/隐藏
    if (container.style.display === "none" || !container.style.display) {
      container.style.display = "block";
      // 首次加载
      if (!container.dataset.loaded) {
        container.innerHTML = '<div class="inline-comments__loading">加载中...</div>';
        fetch(url + "?cid=" + cid)
          .then(res => res.text())
          .then(html => {
            container.innerHTML = html;
            container.dataset.loaded = "1";
            initTextareaHeight();
            formatPostDate();
          })
          .catch(() => {
            container.innerHTML = '<div class="inline-comments__loading">加载失败</div>';
          });
      }
    } else {
      container.style.display = "none";
    }
  });

  // 内联评论提交：默认不显示称呼/邮箱表单，信息不全时弹出访客信息窗口补充
  document.addEventListener("submit", e => {
    const form = e.target.closest(".inline-comments__form");
    if (!form) return;
    e.preventDefault();

    const textarea = form.querySelector(".inline-comments__textarea");
    if (!textarea || !textarea.value.trim()) return;

    // data-has-info 由服务端片段输出：已登录或记住了访客 Cookie 时为 1
    if (form.dataset.hasInfo !== "1") {
      const author = form.querySelector('[name="author"]')?.value.trim() || "";
      const mail = form.querySelector('[name="mail"]')?.value.trim() || "";
      if (!author || !mail) {
        openGuestModal(form);
        return;
      }
    }

    submitComment(form);
  });

  // 首页评论的「回复」按钮：不跳文章页，直接在本卡片内联回复该评论
  function startReply(form, coid, author) {
    const parentInput = form.querySelector('[name="parent"]');
    const textarea = form.querySelector(".inline-comments__textarea");
    const wrap = form.querySelector(".inline-comments__input-wrap");
    if (!parentInput || !textarea || !wrap) return;
    parentInput.value = coid;

    // 提示条置于输入框上方（input-wrap 为纵向布局）
    let bar = form.querySelector(".inline-comments__replying");
    if (!bar) {
      bar = document.createElement("div");
      bar.className = "inline-comments__replying";
      bar.innerHTML =
        '<span class="inline-comments__replying-text"></span>' +
        '<button type="button" class="inline-comments__replying-cancel">取消回复</button>';
      wrap.prepend(bar);
    }
    const label = "回复 @" + (author || "评论者");
    bar.querySelector(".inline-comments__replying-text").textContent = label;
    textarea.placeholder = label + "...";
    textarea.focus();
  }

  function cancelReply(form) {
    const parentInput = form.querySelector('[name="parent"]');
    const textarea = form.querySelector(".inline-comments__textarea");
    if (parentInput) parentInput.value = "";
    form.querySelector(".inline-comments__replying")?.remove();
    if (textarea) {
      textarea.placeholder = "写评论...";
      textarea.blur();
    }
  }

  document.addEventListener("click", e => {
    const btn = e.target.closest(".post__comments .comment__reply");
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest(".post__comments")?.querySelector(".inline-comments__form");
    if (!form) return;
    startReply(form, btn.dataset.coid || "", btn.dataset.author || "");
  });

  // 取消回复（提示条为动态插入，走委托）
  document.addEventListener("click", e => {
    if (!e.target.closest(".inline-comments__replying-cancel")) return;
    const form = e.target.closest(".inline-comments__form");
    if (form) cancelReply(form);
  });

  // Esc 退出回复模式
  document.addEventListener("keydown", e => {
    if ("Escape" !== e.key) return;
    const form = e.target.closest?.(".inline-comments__form");
    if (form?.querySelector(".inline-comments__replying")) cancelReply(form);
  });
}

// OwO 表情面板（评论区通用：文章页表单 + 首页内联表单）
function initOwO() {
  let owoPromise = null;

  const loadOwO = url => {
    if (!owoPromise) {
      owoPromise = fetch(url).then(r => {
        if (!r.ok) throw new Error("HTTP " + r.status);
        return r.json();
      }).catch(err => {
        owoPromise = null; // 失败不缓存，下次点击可重试
        throw err;
      });
    }
    return owoPromise;
  };

  // 由 OwO.json 构建面板：多分组时顶部 Tab 切换，每组一页网格
  function buildPanel(box, data) {
    const groups = Object.keys(data).filter(name => {
      const g = data[name];
      return g && Array.isArray(g.container) && g.container.length;
    });
    box.innerHTML = "";
    if (!groups.length) {
      box.innerHTML = '<div class="owo-empty">暂无表情</div>';
      return;
    }
    if (groups.length > 1) {
      const tabs = document.createElement("div");
      tabs.className = "owo-tabs";
      groups.forEach((name, i) => {
        const tab = document.createElement("button");
        tab.type = "button";
        tab.className = "owo-tab" + (i === 0 ? " active" : "");
        tab.textContent = name;
        tabs.appendChild(tab);
      });
      box.appendChild(tabs);
    }
    const pages = document.createElement("div");
    pages.className = "owo-pages";
    groups.forEach((name, i) => {
      const group = data[name];
      const page = document.createElement("div");
      page.className = "owo-page" + (i === 0 ? " active" : "");
      group.container.forEach(item => {
        if (!item || !item.text || !item.icon) return;
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "owo-item";
        // icon 为主题自带 OwO.json 的白名单 HTML
        btn.innerHTML = item.icon;
        btn.dataset.text = item.text;
        if (group.type !== "image") btn.dataset.raw = "1";
        page.appendChild(btn);
      });
      pages.appendChild(page);
    });
    box.appendChild(pages);
    if (groups.length > 1) {
      box.querySelector(".owo-tabs").addEventListener("click", e => {
        const tab = e.target.closest(".owo-tab");
        if (!tab) return;
        const idx = [...tab.parentElement.children].indexOf(tab);
        box.querySelectorAll(".owo-tab").forEach((t, i) => t.classList.toggle("active", i === idx));
        box.querySelectorAll(".owo-page").forEach((p, i) => p.classList.toggle("active", i === idx));
      });
    }
  }

  function closeOwO() {
    document.querySelectorAll(".owo-box:not([hidden])").forEach(box => {
      box.hidden = true;
      const trigger = box.closest("form")?.querySelector(".owo-trigger.active");
      if (trigger) trigger.classList.remove("active");
    });
  }

  // fixed 定位坐标：宽度对齐输入框，默认向上弹出，上方放不下时翻到下方
  function positionPanel(box) {
    const anchor = box.parentElement;
    if (!anchor) return;
    const rect = anchor.getBoundingClientRect();
    const width = rect.width;
    const h = box.offsetHeight;
    const above = rect.top;
    const below = window.innerHeight - rect.bottom;
    let top;
    if (above >= h + 8) top = rect.top - h - 8;
    else if (below >= h + 8) top = rect.bottom + 8;
    else top = above >= below ? rect.top - h - 8 : rect.bottom + 8;
    top = Math.max(8, Math.min(top, window.innerHeight - h - 8));
    box.style.width = width + "px";
    box.style.left = Math.max(8, Math.min(rect.left, window.innerWidth - width - 8)) + "px";
    box.style.top = top + "px";
  }

  const repositionOpenPanels = () => {
    document.querySelectorAll(".owo-box:not([hidden])").forEach(positionPanel);
  };
  window.addEventListener("scroll", repositionOpenPanels, { passive: true });
  window.addEventListener("resize", repositionOpenPanels);

  function insertToTextarea(textarea, text) {
    if (!textarea) return;
    const s = textarea.selectionStart ?? textarea.value.length;
    const e = textarea.selectionEnd ?? textarea.value.length;
    if (textarea.setRangeText) textarea.setRangeText(text, s, e, "end");
    else textarea.value = textarea.value.slice(0, s) + text + textarea.value.slice(e);
    // 触发输入框自动增高
    textarea.dispatchEvent(new Event("input", { bubbles: true }));
    textarea.focus();
  }

  document.addEventListener("click", e => {
    const trigger = e.target.closest(".owo-trigger");
    if (trigger) {
      const box = trigger.closest("form")?.querySelector(".owo-box");
      if (!box) return;
      const opening = box.hidden;
      closeOwO();
      if (opening) {
        box.hidden = false;
        positionPanel(box);
        trigger.classList.add("active");
        if (!box.dataset.loaded && !box.dataset.loading) {
          box.dataset.loading = "1";
          loadOwO(trigger.dataset.owo)
            .then(data => {
              buildPanel(box, data);
              box.dataset.loaded = "1";
              delete box.dataset.loading;
              positionPanel(box);
            })
            .catch(() => {
              box.innerHTML = '<div class="owo-empty">表情加载失败，请重试</div>';
              delete box.dataset.loading;
              positionPanel(box);
            });
        }
      }
      return;
    }
    const item = e.target.closest(".owo-item");
    if (item) {
      const box = item.closest(".owo-box");
      const textarea = box.closest("form")?.querySelector("textarea");
      // 图片表情插入 {:码:}（服务端渲染时还原为图片），文本表情直接插入
      insertToTextarea(textarea, item.dataset.raw ? item.dataset.text : "{:" + item.dataset.text + ":}");
      return;
    }
    if (!e.target.closest(".owo-box")) closeOwO();
  });

  document.addEventListener("keydown", e => {
    if ("Escape" === e.key) closeOwO();
  });
}

function initLightbox() {
  const SEL = ".post__content img, .post__image";
  let box = null,
    img = null,
    prevBtn = null,
    nextBtn = null,
    counter = null,
    gallery = [],
    index = 0,
    touchX = 0,
    touchY = 0;
  const ARROW = dir =>
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="' +
    (-1 === dir ? "15 18 9 12 15 6" : "9 18 15 12 9 6") + '"/></svg>';
  const ensure = () => {
    if (box) return;
    box = document.createElement("div");
    box.className = "lightbox";
    img = document.createElement("img");
    img.alt = "";
    prevBtn = document.createElement("button");
    prevBtn.type = "button";
    prevBtn.className = "lightbox-nav lightbox-prev";
    prevBtn.setAttribute("aria-label", "上一张");
    prevBtn.innerHTML = ARROW(-1);
    nextBtn = document.createElement("button");
    nextBtn.type = "button";
    nextBtn.className = "lightbox-nav lightbox-next";
    nextBtn.setAttribute("aria-label", "下一张");
    nextBtn.innerHTML = ARROW(1);
    counter = document.createElement("div");
    counter.className = "lightbox__counter";
    box.appendChild(img);
    box.appendChild(counter);
    box.appendChild(prevBtn);
    box.appendChild(nextBtn);
    document.body.appendChild(box);
  };
  const render = () => {
    const t = gallery[index];
    img.src = t ? t.currentSrc || t.src : "";
    const single = gallery.length < 2;
    prevBtn.hidden = single;
    nextBtn.hidden = single;
    counter.hidden = single;
    if (!single) counter.textContent = index + 1 + "/" + gallery.length;
    prevBtn.disabled = 0 === index;
    nextBtn.disabled = index === gallery.length - 1;
  };
  const open = target => {
    ensure();
    // 打开时重新收集；首页卡片内只取当前文章的图片，文章页取全文图片
    const root = target.closest(".post__item") || document;
    gallery = Array.from(root.querySelectorAll(SEL));
    index = Math.max(0, gallery.indexOf(target));
    render();
    box.classList.add("show");
    document.documentElement.classList.add("lightbox-open");
  };
  const close = () => {
    if (!box) return;
    box.classList.remove("show");
    document.documentElement.classList.remove("lightbox-open");
  };
  const step = dir => {
    const next = index + dir;
    if (next < 0 || next >= gallery.length) return;
    index = next;
    render();
  };
  // 事件委托：对 AJAX 追加加载的卡片/内容同样生效
  document.addEventListener("click", e => {
    const nav = e.target.closest(".lightbox-nav");
    if (nav) {
      step(nav.classList.contains("lightbox-prev") ? -1 : 1);
      return;
    }
    const t = e.target.closest(SEL);
    if (t) {
      e.preventDefault();
      open(t);
      return;
    }
    if (e.target.closest(".lightbox")) close();
  });
  document.addEventListener("keydown", e => {
    if (!box || !box.classList.contains("show")) return;
    if ("Escape" === e.key) close();
    else if ("ArrowLeft" === e.key) step(-1);
    else if ("ArrowRight" === e.key) step(1);
  });
  // 移动端：左右滑动切换上一张/下一张
  box.addEventListener("touchstart", e => {
    touchX = e.touches[0].clientX;
    touchY = e.touches[0].clientY;
  }, { passive: true });
  box.addEventListener("touchend", e => {
    if (!box.classList.contains("show") || gallery.length < 2) return;
    const dx = e.changedTouches[0].clientX - touchX,
      dy = e.changedTouches[0].clientY - touchY;
    // 水平位移超过 40px 且以水平为主时判定为滑动；preventDefault 阻止合成点击误关灯箱
    if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
      e.preventDefault();
      step(dx < 0 ? 1 : -1);
    }
  });
}

function init() {
  formatPostDate(), initTextareaHeight(), initWaterfall(), initCommentEvents(), initThemeToggle(), initBackTop(), initCounter(), initTabScroll(), initExternalLinks(), initLikeButtons(), initInlineComments(), initOwO(), initLightbox()
}
document.addEventListener("DOMContentLoaded", init);
