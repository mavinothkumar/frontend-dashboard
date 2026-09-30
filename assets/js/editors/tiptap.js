import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import Link from '@tiptap/extension-link';

const activeEditors = new Map();

/**
 * Safely get WordPress wp.media instance
 */
function getWpMedia() {
  if (typeof window.wp !== 'undefined' && window.wp.media) {
    return window.wp.media;
  }
  if (typeof window.parent !== 'undefined' && window.parent.wp && window.parent.wp.media) {
    return window.parent.wp.media;
  }
  return null;
}

/**
 * Initialize TipTap Editor on a container element
 * @param {HTMLElement} wrapper 
 */
export function initTipTapEditor(wrapper) {
  if (!wrapper || activeEditors.has(wrapper)) {
    return activeEditors.get(wrapper);
  }

  const editorArea = wrapper.querySelector('.fed-tiptap-editor-area');
  const hiddenInput = wrapper.querySelector('.fed-tiptap-hidden-input');
  const toolbar = wrapper.querySelector('.fed-tiptap-toolbar');

  if (!editorArea || !hiddenInput) return null;

  const initialContent = hiddenInput.value || '';

  const editor = new Editor({
    element: editorArea,
    extensions: [
      StarterKit.configure({
        heading: {
          levels: [1, 2, 3, 4]
        }
      }),
      Image.configure({
        inline: true,
        allowBase64: true,
        HTMLAttributes: {
          class: 'rounded-xl max-w-full h-auto my-4 border border-slate-200/80 shadow-xs'
        }
      }),
      Placeholder.configure({
        placeholder: editorArea.getAttribute('data-placeholder') || 'Write your content here...',
        emptyEditorClass: 'is-editor-empty'
      }),
      Link.configure({
        openOnClick: false,
        HTMLAttributes: {
          class: 'text-indigo-600 underline font-medium hover:text-indigo-800 transition-colors'
        }
      })
    ],
    content: initialContent,
    editorProps: {
      attributes: {
        class: 'prose prose-sm sm:prose lg:prose-lg max-w-none focus:outline-none min-h-[260px] p-5 text-slate-800 leading-relaxed'
      }
    },
    onUpdate({ editor }) {
      hiddenInput.value = editor.getHTML();
    },
    onSelectionUpdate({ editor }) {
      updateToolbarState(toolbar, editor);
    },
    onTransaction({ editor }) {
      updateToolbarState(toolbar, editor);
    }
  });

  if (toolbar) {
    bindToolbarEvents(toolbar, editor);
  }

  activeEditors.set(wrapper, editor);
  return editor;
}

/**
 * Update active state styles on toolbar buttons
 */
function updateToolbarState(toolbar, editor) {
  if (!toolbar || !editor) return;

  const buttons = toolbar.querySelectorAll('[data-action]');
  buttons.forEach(btn => {
    const action = btn.getAttribute('data-action');
    const level = btn.getAttribute('data-level');
    let isActive = false;

    if (action === 'heading' && level) {
      isActive = editor.isActive('heading', { level: parseInt(level, 10) });
    } else if (action === 'bold') {
      isActive = editor.isActive('bold');
    } else if (action === 'italic') {
      isActive = editor.isActive('italic');
    } else if (action === 'strike') {
      isActive = editor.isActive('strike');
    } else if (action === 'bulletList') {
      isActive = editor.isActive('bulletList');
    } else if (action === 'orderedList') {
      isActive = editor.isActive('orderedList');
    } else if (action === 'blockquote') {
      isActive = editor.isActive('blockquote');
    } else if (action === 'codeBlock') {
      isActive = editor.isActive('codeBlock');
    } else if (action === 'link') {
      isActive = editor.isActive('link');
    }

    if (isActive) {
      btn.classList.add('bg-indigo-100', 'text-indigo-700', 'font-bold');
      btn.classList.remove('text-slate-600', 'hover:bg-slate-100');
    } else {
      btn.classList.remove('bg-indigo-100', 'text-indigo-700', 'font-bold');
      btn.classList.add('text-slate-600', 'hover:bg-slate-100');
    }
  });
}

/**
 * Bind toolbar actions
 */
function bindToolbarEvents(toolbar, editor) {
  toolbar.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action]');
    if (!btn) return;
    e.preventDefault();

    const action = btn.getAttribute('data-action');
    const level = btn.getAttribute('data-level');

    switch (action) {
      case 'bold':
        editor.chain().focus().toggleBold().run();
        break;
      case 'italic':
        editor.chain().focus().toggleItalic().run();
        break;
      case 'strike':
        editor.chain().focus().toggleStrike().run();
        break;
      case 'heading':
        editor.chain().focus().toggleHeading({ level: parseInt(level, 10) }).run();
        break;
      case 'bulletList':
        editor.chain().focus().toggleBulletList().run();
        break;
      case 'orderedList':
        editor.chain().focus().toggleOrderedList().run();
        break;
      case 'blockquote':
        editor.chain().focus().toggleBlockquote().run();
        break;
      case 'codeBlock':
        editor.chain().focus().toggleCodeBlock().run();
        break;
      case 'horizontalRule':
        editor.chain().focus().setHorizontalRule().run();
        break;
      case 'link': {
        const previousUrl = editor.getAttributes('link').href;
        const url = window.prompt('Enter URL:', previousUrl || 'https://');
        if (url === null) return;
        if (url === '') {
          editor.chain().focus().extendMarkRange('link').unsetLink().run();
        } else {
          editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
        }
        break;
      }
      case 'image':
        openWpMediaForTipTap(editor);
        break;
      case 'undo':
        editor.chain().focus().undo().run();
        break;
      case 'redo':
        editor.chain().focus().redo().run();
        break;
    }
  });
}

/**
 * Open WordPress Media Modal for TipTap
 */
function openWpMediaForTipTap(editor) {
  if (typeof window.fedOpenMediaPicker === 'function') {
    window.fedOpenMediaPicker((attachment) => {
      const imgUrl = (attachment.sizes && attachment.sizes.large) 
        ? attachment.sizes.large.url 
        : (attachment.sizes && attachment.sizes.medium 
          ? attachment.sizes.medium.url 
          : (attachment.sizes && attachment.sizes.full ? attachment.sizes.full.url : attachment.url));
      const altText = attachment.alt || attachment.title || '';

      editor.chain().focus().setImage({ src: imgUrl, alt: altText, title: attachment.title || '' }).run();
    }, {
      title: 'Select or Upload Post Image',
      button: { text: 'Insert into Post' },
      multiple: false
    });
    return;
  }

  const wpMedia = getWpMedia();
  if (wpMedia) {
    try {
      const mediaFrame = wpMedia({
        title: 'Select or Upload Post Image',
        button: { text: 'Insert into Post' },
        multiple: false
      });

      mediaFrame.on('select', () => {
        const attachment = mediaFrame.state().get('selection').first().toJSON();
        const imgUrl = (attachment.sizes && attachment.sizes.large) 
          ? attachment.sizes.large.url 
          : (attachment.sizes && attachment.sizes.medium 
            ? attachment.sizes.medium.url 
            : (attachment.sizes && attachment.sizes.full ? attachment.sizes.full.url : attachment.url));
        const altText = attachment.alt || attachment.title || '';

        editor.chain().focus().setImage({ src: imgUrl, alt: altText, title: attachment.title || '' }).run();
      });

      mediaFrame.open();
      return;
    } catch (err) {
      console.error('TipTap wp.media error:', err);
    }
  }

  const url = window.prompt('Enter image URL:', 'https://');
  if (url) {
    editor.chain().focus().setImage({ src: url }).run();
  }
}

/**
 * Save all active TipTap editors to their hidden inputs
 */
export function saveAllTipTap() {
  activeEditors.forEach((editor, wrapper) => {
    const hiddenInput = wrapper.querySelector('.fed-tiptap-hidden-input');
    if (hiddenInput && editor) {
      hiddenInput.value = editor.getHTML();
    }
  });
}

/**
 * Scan DOM and initialize any pending TipTap instances
 */
export function autoInitTipTap() {
  document.querySelectorAll('.fed-tiptap-container').forEach(wrapper => {
    initTipTapEditor(wrapper);
  });
}
