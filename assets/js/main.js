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

// Classic TinyMCE Add Media Button Binding
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.fed-classic-add-media-btn');
  if (!btn) return;
  e.preventDefault();
  const editorId = btn.getAttribute('data-editor') || 'post_content';
  
  window.fedOpenMediaPicker((item) => {
    const imgHtml = `<p><img src="${item.url}" alt="${item.alt || ''}" /></p>`;
    
    // Check if TinyMCE is active and not hidden (visual mode)
    if (window.tinymce && window.tinymce.get(editorId) && !window.tinymce.get(editorId).isHidden()) {
      window.tinymce.get(editorId).insertContent(imgHtml);
    } else {
      // Raw textarea fallback (Text/HTML mode)
      const textarea = document.getElementById(editorId);
      if (textarea) {
        const start = textarea.selectionStart || 0;
        const end = textarea.selectionEnd || 0;
        textarea.value = textarea.value.substring(0, start) + imgHtml + textarea.value.substring(end);
      }
    }
  }, {
    title: 'Insert Media into Classic Editor'
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
