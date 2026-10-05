import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Quote from '@editorjs/quote';
import Table from '@editorjs/table';
import ImageTool from '@editorjs/image';
import edjsHTML from 'editorjs-html';

const activeEditorJs = new Map();
const edjsParser = edjsHTML({
  image: (block) => {
    const url = block.data.file ? block.data.file.url : (block.data.url || '');
    const caption = block.data.caption || '';
    return `<figure class="fed-block-image my-4"><img src="${url}" alt="${caption}" class="rounded-xl max-w-full h-auto" />${caption ? `<figcaption class="text-xs text-slate-500 text-center mt-1.5">${caption}</figcaption>` : ''}</figure>`;
  }
});

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
 * Custom WordPress Media Image Tool for Editor.js
 */
class WpImageTool extends ImageTool {
  render() {
    const el = super.render();
    const btn = el.querySelector('.cdx-button');
    if (btn) {
      btn.addEventListener('click', (e) => {
        const wpMedia = getWpMedia();
        if (wpMedia) {
          e.preventDefault();
          e.stopPropagation();
          this.openWpMediaPicker();
        }
      }, true);
    }
    return el;
  }

  appendCallback() {
    const wpMedia = getWpMedia();
    if (wpMedia) {
      this.openWpMediaPicker();
    } else {
      super.appendCallback();
    }
  }

  openWpMediaPicker() {
    if (typeof window.fedOpenMediaPicker === 'function') {
      window.fedOpenMediaPicker((attachment) => {
        const imgUrl = (attachment.sizes && attachment.sizes.large) 
          ? attachment.sizes.large.url 
          : (attachment.sizes && attachment.sizes.medium 
            ? attachment.sizes.medium.url 
            : (attachment.sizes && attachment.sizes.full ? attachment.sizes.full.url : attachment.url));
        const caption = attachment.caption || attachment.title || '';

        this.onUpload({
          success: 1,
          file: { url: imgUrl },
          caption: caption
        });
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
          const caption = attachment.caption || attachment.title || '';

          this.onUpload({
            success: 1,
            file: { url: imgUrl },
            caption: caption
          });
        });

        mediaFrame.open();
        return;
      } catch (err) {
        console.error('EditorJS WpImageTool wp.media error:', err);
      }
    }
  }
}

/**
 * Initialize Editor.js on a container
 * @param {HTMLElement} wrapper 
 */
export function initEditorJs(wrapper) {
  if (!wrapper || activeEditorJs.has(wrapper)) {
    return activeEditorJs.get(wrapper);
  }

  const holder = wrapper.querySelector('.fed-editorjs-holder');
  const hiddenInput = wrapper.querySelector('.fed-editorjs-hidden-input');
  const addImageBtn = wrapper.querySelector('.fed-edjs-toolbar-add-image');

  if (!holder || !hiddenInput) return null;

  const holderId = holder.id || ('fed_edjs_' + Math.random().toString(36).substring(7));
  holder.id = holderId;

  let initialData = {};
  const rawValue = hiddenInput.value ? hiddenInput.value.trim() : '';

  if (rawValue.startsWith('{') && rawValue.endsWith('}')) {
    try {
      initialData = JSON.parse(rawValue);
    } catch (e) {
      initialData = {};
    }
  } else if (rawValue) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = rawValue;
    const blocks = [];
    Array.from(tempDiv.children).forEach(child => {
      if (child.tagName === 'H1' || child.tagName === 'H2') {
        blocks.push({ type: 'header', data: { text: child.innerHTML, level: 2 } });
      } else if (child.tagName === 'H3' || child.tagName === 'H4') {
        blocks.push({ type: 'header', data: { text: child.innerHTML, level: 3 } });
      } else if (child.tagName === 'IMG') {
        blocks.push({ type: 'image', data: { file: { url: child.src }, caption: child.alt || '' } });
      } else if (child.tagName === 'BLOCKQUOTE') {
        blocks.push({ type: 'quote', data: { text: child.innerHTML } });
      } else {
        blocks.push({ type: 'paragraph', data: { text: child.innerHTML } });
      }
    });
    if (blocks.length) {
      initialData = { blocks };
    }
  }

  const editor = new EditorJS({
    holder: holderId,
    placeholder: holder.getAttribute('data-placeholder') || 'Click here to write your story...',
    data: initialData,
    tools: {
      header: {
        class: Header,
        inlineToolbar: ['link'],
        config: {
          placeholder: 'Heading...',
          levels: [2, 3, 4],
          defaultLevel: 2
        }
      },
      list: {
        class: List,
        inlineToolbar: true
      },
      quote: {
        class: Quote,
        inlineToolbar: true,
        config: {
          quotePlaceholder: 'Enter a quote',
          captionPlaceholder: "Quote's author"
        }
      },
      table: {
        class: Table,
        inlineToolbar: true
      },
      image: {
        class: WpImageTool,
        config: {
          uploader: {
            uploadByFile(file) {
              return new Promise((resolve) => {
                const wpMedia = getWpMedia();
                if (wpMedia) {
                  try {
                    const mediaFrame = wpMedia({
                      title: 'Select or Upload Post Image',
                      button: { text: 'Insert into Post' },
                      multiple: false,
                      library: {
                        type: 'image'
                      }
                    });
                    mediaFrame.on('select', () => {
                      const attachment = mediaFrame.state().get('selection').first().toJSON();
                      const imgUrl = (attachment.sizes && attachment.sizes.large) 
                        ? attachment.sizes.large.url 
                        : (attachment.sizes && attachment.sizes.medium 
                          ? attachment.sizes.medium.url 
                          : (attachment.sizes && attachment.sizes.full ? attachment.sizes.full.url : attachment.url));
                      resolve({
                        success: 1,
                        file: { url: imgUrl }
                      });
                    });
                    mediaFrame.open();
                    return;
                  } catch (err) {
                    console.warn('wp.media error:', err);
                  }
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                  resolve({
                    success: 1,
                    file: { url: e.target.result }
                  });
                };
                reader.readAsDataURL(file);
              });
            },
            uploadByUrl(url) {
              return Promise.resolve({
                success: 1,
                file: { url }
              });
            }
          }
        }
      }
    },
    async onChange() {
      try {
        const output = await editor.save();
        const htmlArray = edjsParser.parse(output);
        hiddenInput.value = htmlArray.join('\n');
      } catch (e) {
        console.error('EditorJS save error:', e);
      }
    }
  });

  // Dedicated Toolbar "Add Media" Button
  if (addImageBtn) {
    addImageBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (typeof window.fedOpenMediaPicker === 'function') {
        window.fedOpenMediaPicker((attachment) => {
          const imgUrl = (attachment.sizes && attachment.sizes.large) 
            ? attachment.sizes.large.url 
            : (attachment.sizes && attachment.sizes.medium 
              ? attachment.sizes.medium.url 
              : (attachment.sizes && attachment.sizes.full ? attachment.sizes.full.url : attachment.url));
          const caption = attachment.caption || attachment.title || '';

          if (editor && editor.blocks) {
            editor.blocks.insert('image', {
              file: { url: imgUrl },
              caption: caption,
              withBorder: false,
              withBackground: false,
              stretched: false
            });
          }
        }, {
          title: 'Select or Upload Post Image',
          button: { text: 'Insert into Post' },
          multiple: false
        });
        return;
      }

      const url = window.prompt('Enter image URL:', 'https://');
      if (url && editor && editor.blocks) {
        editor.blocks.insert('image', {
          file: { url },
          caption: '',
          withBorder: false,
          withBackground: false,
          stretched: false
        });
      }
    });
  }

  activeEditorJs.set(wrapper, editor);
  return editor;
}

/**
 * Save all active Editor.js instances to hidden inputs
 */
export async function saveAllEditorJs() {
  for (const [wrapper, editor] of activeEditorJs.entries()) {
    const hiddenInput = wrapper.querySelector('.fed-editorjs-hidden-input');
    if (hiddenInput && editor && typeof editor.save === 'function') {
      try {
        const output = await editor.save();
        const htmlArray = edjsParser.parse(output);
        hiddenInput.value = htmlArray.join('\n');
      } catch (e) {
        console.error('Error saving EditorJS instance:', e);
      }
    }
  }
}

/**
 * Scan DOM and auto-initialize Editor.js instances
 */
export function autoInitEditorJs() {
  document.querySelectorAll('.fed-editorjs-container').forEach(wrapper => {
    initEditorJs(wrapper);
  });
}
