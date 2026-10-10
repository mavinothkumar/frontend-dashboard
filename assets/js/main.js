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
import { openMediaPicker } from './media-modal';

// Expose native modern media picker globally
window.fedOpenMediaPicker = function(callback, options) {
  openMediaPicker(callback, options);
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
  }
}

// Classic TinyMCE Add Media Button Binding
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.fed-classic-add-media-btn');
  if (!btn) return;
  e.preventDefault();
  const editorId = btn.getAttribute('data-editor') || 'post_content';
  
  window.fedOpenMediaPicker((selected) => {
    const items = Array.isArray(selected) ? selected : [selected];
    const htmlSnippets = [];

    items.forEach((item) => {
      if (!item || !item.url) return;
      const url = escapeAttr(item.url);
      const isImage = Boolean(item.is_image) || (item.mime && item.mime.startsWith('image/'));

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
        htmlSnippets.push(`<p><a href="${linkHref}" target="_blank" rel="noopener noreferrer">${label}</a></p>`);
      } else {
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
          htmlSnippets.push(`[caption id="attachment_${item.id}" align="${alignClass}"${widthAttr}]${imgTag} ${captionText}[/caption]`);
        } else {
          htmlSnippets.push(`<p>${imgTag}</p>`);
        }
      }
    });

    if (!htmlSnippets.length) return;

    insertHtmlIntoEditor(editorId, htmlSnippets.join('\n\n'));
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

