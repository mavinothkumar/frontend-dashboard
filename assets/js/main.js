// Defensive WP globals shim to prevent unhandled script crashes on frontend
window.wp = window.wp || {};
window.wp.editor = window.wp.editor || {};
if (!window.wp.i18n) {
  window.wp.i18n = {
    __: (text) => text,
    _x: (text) => text,
    _n: (single, plural, number) => (number === 1 ? single : plural),
    _nx: (single, plural, number) => (number === 1 ? single : plural),
    isRtl: () => false,
    setLocaleData: () => {},
    sprintf: (text) => text
  };
}

import { initTipTapEditor, autoInitTipTap, saveAllTipTap } from './editors/tiptap';
import { initEditorJs, autoInitEditorJs, saveAllEditorJs } from './editors/editorjs';
import { openMediaPicker, getMediaModal, registerMediaTab, registerMediaFilter } from './media-modal';

// Expose native modern media picker globally
window.fedOpenMediaPicker = function(callback, options) {
  openMediaPicker(callback, options);
};

// Expose comprehensive media picker API for third-party plugins
window.FedMedia = {
  open: openMediaPicker,
  getModal: getMediaModal,
  registerTab(config) {
    registerMediaTab(config);
  },
  registerFilter(fn) {
    registerMediaFilter(fn);
  },
  registerItemTransformer(fn) {
    if (typeof fn === 'function') {
      this._itemTransformers.push(fn);
    }
  },
  _itemTransformers: []
};

// Expose global API for Frontend Dashboard
window.FedEditors = {
  initTipTap: initTipTapEditor,
  autoInitTipTap,
  saveAllTipTap,
  initEditorJs,
  autoInitEditorJs,
  saveAllEditorJs,
  async saveAll() {
    saveAllTipTap();
    await saveAllEditorJs();
  },
  autoInitAll() {
    autoInitTipTap();
    autoInitEditorJs();
  }
};

function escapeAttr(str) {
  return String(str || '')
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

function escapeHtml(str) {
  return String(str || '')
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

function insertHtmlIntoEditor(editorId, html) {
  if (!html) return;

  // Set active editor context for WordPress core helpers
  window.wpActiveEditor = editorId;

  // 1. Delegate to WordPress core send_to_editor if available
  if (typeof window.send_to_editor === 'function') {
    window.send_to_editor(html);
    return;
  }

  // 2. TinyMCE visual editor mode
  const hasTinymce = typeof window.tinymce !== 'undefined';
  const editor = hasTinymce ? window.tinymce.get(editorId) : null;
  if (editor && !editor.isHidden()) {
    editor.focus();
    editor.execCommand('mceInsertContent', false, html);
    return;
  }

  // 3. Quicktags text mode
  if (typeof window.QTags !== 'undefined' && typeof window.QTags.insertContent === 'function') {
    window.QTags.insertContent(html);
    return;
  }

  // 4. Raw Textarea fallback
  const textarea = document.getElementById(editorId);
  if (textarea) {
    textarea.focus();
    const start = typeof textarea.selectionStart === 'number' ? textarea.selectionStart : textarea.value.length;
    const end = typeof textarea.selectionEnd === 'number' ? textarea.selectionEnd : textarea.value.length;
    const val = textarea.value;
    textarea.value = val.substring(0, start) + html + val.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + html.length;
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.dispatchEvent(new Event('change', { bubbles: true }));
  }
}

/**
 * Send items to WordPress backend to apply 'media_send_to_editor' (e.g. PDF Embedder) and 'fed_media_send_to_editor'
 */
async function sendMediaToEditor(items, editorId) {
  let ajaxUrl = '/wp-admin/admin-ajax.php';
  let nonce = '';

  if (typeof frontend_dashboard !== 'undefined' && frontend_dashboard.media && frontend_dashboard.media.ajax_url) {
    ajaxUrl = frontend_dashboard.media.ajax_url;
  } else if (typeof frontend_dashboard !== 'undefined' && frontend_dashboard.fed_admin_form_post) {
    ajaxUrl = frontend_dashboard.fed_admin_form_post.split('?')[0];
  }

  if (typeof frontend_dashboard !== 'undefined' && frontend_dashboard.media && frontend_dashboard.media.nonce) {
    nonce = frontend_dashboard.media.nonce;
  } else if (typeof frontend_dashboard !== 'undefined' && frontend_dashboard.fed_admin_form_post) {
    const match = frontend_dashboard.fed_admin_form_post.match(/fed_nonce=([^&]+)/);
    if (match) nonce = match[1];
  }
  if (!nonce) {
    const nonceInput = document.querySelector('input[name="fed_nonce"]');
    if (nonceInput) nonce = nonceInput.value;
  }

  const formData = new FormData();
  formData.append('action', 'fed_send_media_to_editor');
  formData.append('fed_nonce', nonce);
  formData.append('editor_id', editorId);

  items.forEach((item, index) => {
    Object.keys(item).forEach((key) => {
      if (typeof item[key] !== 'object' || item[key] === null) {
        formData.append(`items[${index}][${key}]`, item[key] !== undefined && item[key] !== null ? item[key] : '');
      }
    });
  });

  try {
    const res = await fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    });
    const data = await res.json();
    if (data && data.success && data.data && Array.isArray(data.data.snippets)) {
      return data.data.snippets;
    }
  } catch (err) {
    console.warn('Frontend Dashboard: Could not fetch server media transformation, falling back to local.', err);
  }

  return null;
}

/**
 * Robust client-side fallback formatting with native PDF Embedder support
 */
function formatClientMediaSnippet(item) {
  if (!item || !item.url) return '';
  const url = escapeAttr(item.url);
  const isImage = Boolean(item.is_image) || (item.mime && item.mime.startsWith('image/'));
  const isPdf = item.mime === 'application/pdf' || (item.url && item.url.match(/\.pdf(\?|$)/i)) || (item.ext && item.ext.toUpperCase() === 'PDF');
  const isPdfEmbedderActive = Boolean(
    (typeof frontend_dashboard !== 'undefined' && frontend_dashboard.media && frontend_dashboard.media.pdf_embedder_active) ||
    window.fed_pdf_embedder_active === true ||
    (typeof window.pdfemb_args !== 'undefined') ||
    (typeof window.PDFEMB_NS !== 'undefined')
  );

  // PDF Embedder shortcode transformation
  if (isPdf && isPdfEmbedderActive) {
    const titleAttr = item.title ? ` title="${escapeAttr(item.title)}"` : '';
    return `[pdf-embedder url="${url}"${titleAttr}]`;
  }

  if (!isImage) {
    // Document / Non-image link
    const labelText = item.title || item.filename || 'Download File';
    const label = escapeHtml(labelText);
    let linkHref = url;
    if (item.linkTo === 'custom' && item.linkUrl) {
      linkHref = escapeAttr(item.linkUrl);
    } else if (item.linkTo === 'post' && item.link) {
      linkHref = escapeAttr(item.link);
    }
    return `<p><a href="${linkHref}" target="_blank" rel="noopener noreferrer">${label}</a></p>`;
  }

  // Standard WordPress Image element with user-configured display settings
  const alt = escapeAttr(item.alt || item.title || '');
  const alignClass = item.align && item.align !== 'none' ? `align${item.align}` : 'alignnone';
  const sizeClass = item.size ? `size-${item.size}` : 'size-full';
  const idClass = item.id ? ` wp-image-${item.id}` : '';
  let imgTag = `<img src="${url}" alt="${alt}" class="${alignClass} ${sizeClass}${idClass}" />`;

  let linkHref = '';
  if (item.linkTo === 'file') {
    linkHref = escapeAttr(item.full || item.url);
  } else if (item.linkTo === 'post' && item.link) {
    linkHref = escapeAttr(item.link);
  } else if (item.linkTo === 'custom' && item.linkUrl) {
    linkHref = escapeAttr(item.linkUrl);
  }

  if (linkHref) {
    imgTag = `<a href="${linkHref}">${imgTag}</a>`;
  }

  if (item.caption) {
    const captionText = escapeHtml(item.caption);
    const widthAttr = item.width ? ` width="${item.width}"` : '';
    return `[caption id="attachment_${item.id}" align="${alignClass}"${widthAttr}]${imgTag} ${captionText}[/caption]`;
  }

  return `<p>${imgTag}</p>`;
}

// Classic TinyMCE Add Media Button Binding
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.fed-classic-add-media-btn');
  if (!btn) return;
  e.preventDefault();
  const editorId = btn.getAttribute('data-editor') || 'post_content';
  
  window.fedOpenMediaPicker(async (selected) => {
    const items = Array.isArray(selected) ? selected : [selected];
    if (!items.length) return;

    // 1. Fetch server-filtered snippets (executes WordPress core 'media_send_to_editor' and FED filters)
    let snippets = await sendMediaToEditor(items, editorId);

    // 2. If server formatting is unavailable, fall back to robust client generator
    if (!snippets || !snippets.length) {
      snippets = items.map(item => formatClientMediaSnippet(item)).filter(Boolean);
    }

    // 3. Apply client-side JavaScript filters and registered transformers
    snippets = snippets.map((snippet, idx) => {
      let finalSnippet = snippet;
      const item = items[idx];

      if (window.wp && window.wp.hooks && typeof window.wp.hooks.applyFilters === 'function') {
        finalSnippet = window.wp.hooks.applyFilters('fed.media.insert_html', finalSnippet, item, editorId);
        finalSnippet = window.wp.hooks.applyFilters('fed_media_send_to_editor', finalSnippet, item, editorId);
      }

      if (window.FedMedia && Array.isArray(window.FedMedia._itemTransformers)) {
        window.FedMedia._itemTransformers.forEach((fn) => {
          try {
            finalSnippet = fn(finalSnippet, item, editorId) || finalSnippet;
          } catch (err) {
            console.error('Frontend Dashboard: Error in media item transformer:', err);
          }
        });
      }

      return finalSnippet;
    });

    if (!snippets.length) return;

    const combinedHtml = snippets.join('\n\n');
    insertHtmlIntoEditor(editorId, combinedHtml);

    // 4. Dispatch actions and events for third-party extensions
    if (window.wp && window.wp.hooks && typeof window.wp.hooks.doAction === 'function') {
      window.wp.hooks.doAction('fed.media.inserted', snippets, items, editorId);
    }

    document.dispatchEvent(new CustomEvent('fed:media:inserted', {
      detail: { snippets, items, editorId }
    }));
  }, {
    title: 'Insert Media into Classic Editor',
    multiple: true,
    mime: 'all'
  });
});

// Auto-initialize on document ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    window.FedEditors.autoInitAll();
  });
} else {
  window.FedEditors.autoInitAll();
}
