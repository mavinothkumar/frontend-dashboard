/**
 * Frontend Dashboard - Native Modern Media Modal
 *
 * Lightweight, zero-dependency WordPress media library and uploader modal.
 */

let modalInstance = null;

class FedMediaModal {
  constructor() {
    this.isOpen = false;
    this.activeTab = 'library'; // 'library' | 'upload'
    this.items = [];
    this.selectedItem = null;
    this.selectedSize = 'full';
    this.searchQuery = '';
    this.currentPage = 1;
    this.totalPages = 1;
    this.isLoading = false;
    this.callback = null;
    this.options = {};

    this.createDom();
    this.bindEvents();
  }

  getAjaxUrl() {
    if (typeof frontend_dashboard !== 'undefined' && frontend_dashboard.fed_admin_form_post) {
      // Extract base ajaxurl from fed_admin_form_post if needed or standard admin-ajax.php
      return frontend_dashboard.fed_admin_form_post.split('?')[0];
    }
    return '/wp-admin/admin-ajax.php';
  }

  getNonce() {
    if (typeof frontend_dashboard !== 'undefined' && frontend_dashboard.fed_admin_form_post) {
      const match = frontend_dashboard.fed_admin_form_post.match(/fed_nonce=([^&]+)/);
      if (match) return match[1];
    }
    const nonceInput = document.querySelector('input[name="fed_nonce"]');
    return nonceInput ? nonceInput.value : '';
  }

  createDom() {
    const existing = document.getElementById('fed-native-media-modal');
    if (existing) {
      existing.remove();
    }

    const modal = document.createElement('div');
    modal.id = 'fed-native-media-modal';
    modal.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999999 !important; display: none; align-items: center; justify-content: center; background-color: rgba(15, 23, 42, 0.75) !important; backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); padding: 1rem; box-sizing: border-box;';
    modal.innerHTML = `
      <div class="fed-modal-dialog bg-white rounded-3xl shadow-2xl border border-slate-200/90 w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden text-slate-800 font-sans transform transition-all duration-200" style="position: relative !important; z-index: 100000000 !important; background-color: #ffffff !important; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.4) !important;">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-xs border border-indigo-100/60 shrink-0">
              <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900 leading-none fed-modal-title">Select Media</h3>
              <p class="text-[11px] text-slate-400 mt-1">Choose from your WordPress media library or upload new files</p>
            </div>
          </div>
          <button type="button" class="fed-media-close-btn w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Navigation Tabs & Search -->
        <div class="px-6 py-3 border-b border-slate-100 bg-white flex flex-wrap items-center justify-between gap-3">
          <div class="flex items-center gap-2 p-1 bg-slate-100/80 rounded-xl">
            <button type="button" class="fed-tab-btn px-4 py-1.5 rounded-lg text-xs font-semibold transition-all shadow-xs bg-white text-indigo-600 flex items-center gap-1.5" data-tab="library">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              <span>Media Library</span>
            </button>
            <button type="button" class="fed-tab-btn px-4 py-1.5 rounded-lg text-xs font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5" data-tab="upload">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <span>Upload Files</span>
            </button>
          </div>

          <div class="fed-library-search-wrapper relative flex-1 max-w-xs">
            <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" class="fed-media-search-input w-full pl-8 pr-8 py-1.5 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-hidden" placeholder="Search media by title..." />
            <button type="button" class="fed-media-search-clear absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 hidden text-xs flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-hidden relative min-h-[360px] flex flex-col">
          
          <!-- Library Tab View -->
          <div class="fed-view-library flex-1 flex flex-col overflow-hidden">
            <div class="flex-1 p-5 overflow-y-auto fed-media-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 content-start">
              <!-- Dynamically populated cards -->
            </div>
            
            <!-- Details Bar for Selected Item -->
            <div class="fed-selected-info-bar border-t border-slate-100 bg-slate-50/80 px-6 py-3 flex items-center justify-between gap-4 hidden">
              <div class="flex items-center gap-3 overflow-hidden min-w-0">
                <img src="" alt="" class="fed-info-thumb w-10 h-10 object-cover rounded-lg border border-slate-200 bg-white shrink-0" />
                <div class="overflow-hidden min-w-0">
                  <p class="text-xs font-bold text-slate-800 truncate m-0 fed-info-name"></p>
                  <p class="text-[11px] text-slate-400 m-0 fed-info-meta"></p>
                </div>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <label class="text-xs text-slate-500 font-medium">Size:</label>
                <select class="fed-size-select text-xs border border-slate-200 bg-white rounded-lg px-2.5 py-1 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden">
                  <option value="full">Full Size</option>
                  <option value="large">Large</option>
                  <option value="medium">Medium</option>
                  <option value="thumbnail">Thumbnail</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Upload Tab View -->
          <div class="fed-view-upload flex-1 p-6 overflow-y-auto flex flex-col items-center justify-center hidden">
            <div class="fed-dropzone-area w-full max-w-lg border-2 border-dashed border-slate-300 rounded-3xl p-10 flex flex-col items-center justify-center text-center hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer bg-slate-50/60">
              <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 shadow-xs border border-indigo-100/60">
                <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              <h4 class="text-sm font-bold text-slate-800 mb-1">Drag and drop files here</h4>
              <p class="text-xs text-slate-400 mb-5">Supported formats: JPG, PNG, GIF, WEBP, SVG</p>
              <button type="button" class="fed-select-files-btn px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs hover:shadow-indigo-500/25 transition-all">
                Browse Files
              </button>
              <input type="file" class="fed-file-input hidden" accept="image/*" />
            </div>

            <!-- Upload Progress Card -->
            <div class="fed-upload-progress-card w-full max-w-lg mt-4 p-4 rounded-2xl border border-slate-200 bg-white shadow-xs hidden">
              <div class="flex items-center justify-between text-xs mb-2">
                <span class="font-bold text-slate-700 fed-uploading-filename truncate max-w-xs">Uploading...</span>
                <span class="text-indigo-600 font-semibold fed-upload-percent">0%</span>
              </div>
              <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="fed-upload-bar h-full bg-indigo-600 rounded-full transition-all duration-200" style="width: 0%"></div>
              </div>
            </div>
          </div>

          <!-- Loading Overlay -->
          <div class="fed-media-loader absolute inset-0 bg-white/70 backdrop-blur-2xs flex items-center justify-center z-10 hidden">
            <div class="flex flex-col items-center gap-3">
              <div class="w-8 h-8 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
              <span class="text-xs font-semibold text-slate-600">Loading media library...</span>
            </div>
          </div>

          <!-- Empty State -->
          <div class="fed-media-empty absolute inset-0 flex flex-col items-center justify-center p-6 text-center z-5 hidden">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-3">
              <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h4 class="text-sm font-bold text-slate-700 m-0">No media files found</h4>
            <p class="text-xs text-slate-400 mt-1 mb-4">Upload your first image to get started.</p>
            <button type="button" class="fed-switch-upload-btn px-4 py-1.5 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold transition-colors">
              Upload Files
            </button>
          </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
          <div class="text-xs text-slate-500 fed-selection-counter">
            No item selected
          </div>
          <div class="flex items-center gap-2.5">
            <button type="button" class="fed-media-cancel-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-200/60 transition-colors">
              Cancel
            </button>
            <button type="button" class="fed-media-insert-btn px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-bold shadow-xs hover:shadow-indigo-500/25 transition-all" disabled>
              Insert into Post
            </button>
          </div>
        </div>

      </div>
    `;

    document.body.appendChild(modal);
    this.el = modal;
  }

  bindEvents() {
    const el = this.el;

    // Prevent clicks inside the dialog from bubbling to the backdrop
    const dialog = el.querySelector('.fed-modal-dialog');
    if (dialog) {
      dialog.addEventListener('click', (e) => {
        e.stopPropagation();
      });
    }

    // Close buttons
    el.querySelector('.fed-media-close-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      this.close();
    });
    el.querySelector('.fed-media-cancel-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      this.close();
    });
    el.addEventListener('click', (e) => {
      if (e.target === el) this.close();
    });

    // Tab switcher
    el.querySelectorAll('.fed-tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const tab = btn.getAttribute('data-tab');
        this.switchTab(tab);
      });
    });

    el.querySelector('.fed-switch-upload-btn').addEventListener('click', () => {
      this.switchTab('upload');
    });

    // Search input
    const searchInput = el.querySelector('.fed-media-search-input');
    const searchClear = el.querySelector('.fed-media-search-clear');
    let debounceTimer = null;

    searchInput.addEventListener('input', (e) => {
      const val = e.target.value.trim();
      searchClear.classList.toggle('hidden', !val);
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => {
        this.searchQuery = val;
        this.fetchMedia(1);
      }, 300);
    });

    searchClear.addEventListener('click', () => {
      searchInput.value = '';
      searchClear.classList.add('hidden');
      this.searchQuery = '';
      this.fetchMedia(1);
    });

    // Size selector change
    const sizeSelect = el.querySelector('.fed-size-select');
    sizeSelect.addEventListener('change', (e) => {
      this.selectedSize = e.target.value;
    });

    // File Upload interactions
    const dropzone = el.querySelector('.fed-dropzone-area');
    const fileInput = el.querySelector('.fed-file-input');
    const selectBtn = el.querySelector('.fed-select-files-btn');

    selectBtn.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('click', (e) => {
      if (e.target !== selectBtn) fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
      if (e.target.files && e.target.files[0]) {
        this.uploadFile(e.target.files[0]);
      }
    });

    // Drag & Drop
    ['dragenter', 'dragover'].forEach(eventName => {
      dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('border-indigo-500', 'bg-indigo-50/50');
      });
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('border-indigo-500', 'bg-indigo-50/50');
      });
    });

    dropzone.addEventListener('drop', (e) => {
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]) {
        this.uploadFile(e.dataTransfer.files[0]);
      }
    });

    // Insert Button
    el.querySelector('.fed-media-insert-btn').addEventListener('click', () => {
      if (!this.selectedItem) return;

      const item = this.selectedItem;
      let selectedUrl = item.url || item.full;
      if (this.selectedSize === 'thumbnail' && item.thumbnail) selectedUrl = item.thumbnail;
      else if (this.selectedSize === 'medium' && item.medium) selectedUrl = item.medium;
      else if (this.selectedSize === 'large' && item.large) selectedUrl = item.large;

      const result = {
        id: item.id,
        title: item.title || item.filename || '',
        url: selectedUrl,
        alt: item.title || '',
        filename: item.filename || '',
        sizes: {
          thumbnail: { url: item.thumbnail || item.url },
          medium: { url: item.medium || item.url },
          large: { url: item.large || item.url },
          full: { url: item.full || item.url }
        }
      };

      if (typeof this.callback === 'function') {
        this.callback(result);
      }

      this.close();
    });
  }

  switchTab(tab) {
    this.activeTab = tab;
    const el = this.el;

    el.querySelectorAll('.fed-tab-btn').forEach(btn => {
      const isTarget = btn.getAttribute('data-tab') === tab;
      btn.classList.toggle('bg-white', isTarget);
      btn.classList.toggle('text-indigo-600', isTarget);
      btn.classList.toggle('shadow-xs', isTarget);
      btn.classList.toggle('text-slate-600', !isTarget);
    });

    const searchWrapper = el.querySelector('.fed-library-search-wrapper');
    const libraryView = el.querySelector('.fed-view-library');
    const uploadView = el.querySelector('.fed-view-upload');

    if (tab === 'library') {
      searchWrapper.classList.remove('hidden');
      libraryView.classList.remove('hidden');
      uploadView.classList.add('hidden');
      if (!this.items.length) {
        this.fetchMedia(1);
      }
    } else {
      searchWrapper.classList.add('hidden');
      libraryView.classList.add('hidden');
      uploadView.classList.remove('hidden');
    }
  }

  open(callback, options = {}) {
    this.callback = callback;
    this.options = options;
    this.isOpen = true;

    const el = this.el;
    el.style.setProperty('display', 'flex', 'important');
    document.body.style.overflow = 'hidden';

    if (options.title) {
      el.querySelector('.fed-modal-title').textContent = options.title;
    }
    if (options.button && options.button.text) {
      el.querySelector('.fed-media-insert-btn').textContent = options.button.text;
    } else {
      el.querySelector('.fed-media-insert-btn').textContent = 'Insert into Post';
    }

    this.switchTab('library');
    this.fetchMedia(1);
  }

  close() {
    this.isOpen = false;
    const el = this.el;
    el.style.setProperty('display', 'none', 'important');
    document.body.style.overflow = '';
    this.selectedItem = null;
    this.updateSelectionUI();
  }

  async fetchMedia(page = 1) {
    this.currentPage = page;
    this.setLoading(true);

    const ajaxUrl = this.getAjaxUrl();
    const nonce = this.getNonce();

    const formData = new FormData();
    formData.append('action', 'fed_get_media_library');
    formData.append('fed_nonce', nonce);
    formData.append('search', this.searchQuery);
    formData.append('page', page);

    try {
      const res = await fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });

      const data = await res.json();
      if (data.success && data.data && data.data.items) {
        this.items = data.data.items;
        this.totalPages = data.data.total_pages || 1;
        this.renderGrid();
      } else {
        this.items = [];
        this.renderGrid();
      }
    } catch (err) {
      console.error('Error fetching media library:', err);
      this.items = [];
      this.renderGrid();
    } finally {
      this.setLoading(false);
    }
  }

  renderGrid() {
    const el = this.el;
    const grid = el.querySelector('.fed-media-grid');
    const emptyState = el.querySelector('.fed-media-empty');

    grid.innerHTML = '';

    if (!this.items.length) {
      emptyState.classList.remove('hidden');
      return;
    }
    emptyState.classList.add('hidden');

    this.items.forEach(item => {
      const card = document.createElement('div');
      const isSelected = this.selectedItem && this.selectedItem.id === item.id;
      
      card.className = `fed-media-card group relative aspect-square rounded-2xl overflow-hidden border-2 cursor-pointer transition-all duration-150 bg-slate-100 ${
        isSelected 
          ? 'border-indigo-600 ring-4 ring-indigo-500/20 shadow-md' 
          : 'border-slate-200/90 hover:border-indigo-300 hover:shadow-xs'
      }`;

      card.innerHTML = `
        <img src="${item.thumbnail || item.url}" alt="${item.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy" />
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-2 text-white">
          <span class="text-[10px] font-bold truncate leading-tight">${item.title || item.filename}</span>
          <span class="text-[9px] text-slate-300">${item.filesize || ''}</span>
        </div>
        ${
          isSelected 
            ? `<div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shadow-sm animate-in zoom-in-50"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg></div>` 
            : ''
        }
      `;

      card.addEventListener('click', () => {
        this.selectItem(item);
      });

      grid.appendChild(card);
    });
  }

  selectItem(item) {
    if (this.selectedItem && this.selectedItem.id === item.id) {
      // Toggle or keep selected
      this.selectedItem = item;
    } else {
      this.selectedItem = item;
    }

    this.renderGrid();
    this.updateSelectionUI();
  }

  updateSelectionUI() {
    const el = this.el;
    const insertBtn = el.querySelector('.fed-media-insert-btn');
    const counter = el.querySelector('.fed-selection-counter');
    const infoBar = el.querySelector('.fed-selected-info-bar');

    if (this.selectedItem) {
      insertBtn.disabled = false;
      counter.innerHTML = `<span class="font-bold text-slate-700">1</span> item selected`;

      infoBar.classList.remove('hidden');
      infoBar.querySelector('.fed-info-thumb').src = this.selectedItem.thumbnail || this.selectedItem.url;
      infoBar.querySelector('.fed-info-name').textContent = this.selectedItem.title || this.selectedItem.filename;
      infoBar.querySelector('.fed-info-meta').textContent = `${this.selectedItem.width ? `${this.selectedItem.width} × ${this.selectedItem.height} • ` : ''}${this.selectedItem.filesize || ''} • ${this.selectedItem.date || ''}`;
    } else {
      insertBtn.disabled = true;
      counter.textContent = 'No item selected';
      infoBar.classList.add('hidden');
    }
  }

  async uploadFile(file) {
    const el = this.el;
    const progressCard = el.querySelector('.fed-upload-progress-card');
    const progressBar = el.querySelector('.fed-upload-bar');
    const percentText = el.querySelector('.fed-upload-percent');
    const filenameText = el.querySelector('.fed-uploading-filename');

    filenameText.textContent = file.name;
    progressCard.classList.remove('hidden');
    progressBar.style.width = '10%';
    percentText.textContent = '10%';

    const ajaxUrl = this.getAjaxUrl();
    const nonce = this.getNonce();

    const formData = new FormData();
    formData.append('action', 'fed_upload_media_file');
    formData.append('fed_nonce', nonce);
    formData.append('file', file);

    try {
      progressBar.style.width = '50%';
      percentText.textContent = '50%';

      const res = await fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });

      const data = await res.json();
      progressBar.style.width = '100%';
      percentText.textContent = '100%';

      if (data.success && data.data) {
        const newItem = data.data;
        // Prepend to items list
        this.items.unshift(newItem);
        this.selectedItem = newItem;
        
        setTimeout(() => {
          progressCard.classList.add('hidden');
          this.switchTab('library');
          this.renderGrid();
          this.updateSelectionUI();
        }, 400);
      } else {
        alert(data.data && data.data.message ? data.data.message : 'Upload failed. Please try again.');
        progressCard.classList.add('hidden');
      }
    } catch (err) {
      console.error('File upload error:', err);
      alert('Upload failed due to network error.');
      progressCard.classList.add('hidden');
    }
  }

  setLoading(val) {
    this.isLoading = val;
    const loader = this.el.querySelector('.fed-media-loader');
    loader.classList.toggle('hidden', !val);
  }
}

/**
 * Get or create singleton instance of FedMediaModal
 */
export function getMediaModal() {
  if (!modalInstance) {
    modalInstance = new FedMediaModal();
  }
  return modalInstance;
}

/**
 * Global helper exposed to window
 */
export function openMediaPicker(callback, options = {}) {
  const modal = getMediaModal();
  modal.open(callback, options);
}
