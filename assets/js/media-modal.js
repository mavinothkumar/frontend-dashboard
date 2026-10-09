/**
 * Frontend Dashboard - Native Modern Media Modal
 *
 * Lightweight, zero-dependency WordPress media library and uploader modal.
 * Supports:
 * - Multi-file concurrent uploads with real-time individual progress bars
 * - Strict instant MIME filtering (Images, Documents/PDFs, Audio, Video)
 * - Complete Attachment Details Sidebar (Alt Text, Title, Caption, Description, Alignment, Link To, Size)
 * - Multi-select and batch insertion into editors
 */

let modalInstance = null;

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

class FedMediaModal {
  constructor() {
    this.isOpen = false;
    this.activeTab = 'library'; // 'library' | 'upload'
    this.items = [];
    this.selectedItems = new Map(); // id -> item
    this.selectedItem = null; // item currently inspected in sidebar
    this.selectedSize = 'full';
    this.selectedAlign = 'none';
    this.selectedLinkTo = 'none';
    this.selectedLinkUrl = '';
    this.searchQuery = '';
    this.mimeFilter = 'all';
    this.currentPage = 1;
    this.totalPages = 1;
    this.isLoading = false;
    this.callback = null;
    this.options = {};
    this.currentAbortController = null;
    this.saveDetailsTimeout = null;

    this.createDom();
    this.bindEvents();
  }

  get isMultiple() {
    return Boolean(this.options.multiple);
  }

  getAjaxUrl() {
    if (typeof frontend_dashboard !== 'undefined' && frontend_dashboard.fed_admin_form_post) {
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

  getFileIconConfig(ext, mime) {
    const extension = (ext || '').toUpperCase();
    const mimeType = (mime || '').toLowerCase();

    // PDF
    if (extension === 'PDF' || mimeType.includes('pdf')) {
      return {
        bgClass: 'bg-red-50',
        textClass: 'text-red-600',
        borderClass: 'border-red-200/80',
        badgeBg: 'bg-red-100',
        badgeText: 'text-red-700',
        svg: `<svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v6h6" />
        </svg>`
      };
    }

    // Documents (Word, Text, RTF, ODT)
    if (['DOC', 'DOCX', 'TXT', 'RTF', 'ODT'].includes(extension) || mimeType.includes('word') || mimeType.includes('text/plain')) {
      return {
        bgClass: 'bg-blue-50',
        textClass: 'text-blue-600',
        borderClass: 'border-blue-200/80',
        badgeBg: 'bg-blue-100',
        badgeText: 'text-blue-700',
        svg: `<svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>`
      };
    }

    // Spreadsheets (Excel, CSV, ODS)
    if (['XLS', 'XLSX', 'CSV', 'ODS'].includes(extension) || mimeType.includes('excel') || mimeType.includes('spreadsheet') || mimeType.includes('csv')) {
      return {
        bgClass: 'bg-emerald-50',
        textClass: 'text-emerald-600',
        borderClass: 'border-emerald-200/80',
        badgeBg: 'bg-emerald-100',
        badgeText: 'text-emerald-700',
        svg: `<svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>`
      };
    }

    // Archives (Zip, Tar, Rar, 7z)
    if (['ZIP', 'RAR', '7Z', 'TAR', 'GZ'].includes(extension) || mimeType.includes('zip') || mimeType.includes('compressed')) {
      return {
        bgClass: 'bg-amber-50',
        textClass: 'text-amber-600',
        borderClass: 'border-amber-200/80',
        badgeBg: 'bg-amber-100',
        badgeText: 'text-amber-700',
        svg: `<svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
        </svg>`
      };
    }

    // Audio
    if (['MP3', 'WAV', 'OGG', 'M4A', 'FLAC', 'AAC'].includes(extension) || mimeType.startsWith('audio/')) {
      return {
        bgClass: 'bg-purple-50',
        textClass: 'text-purple-600',
        borderClass: 'border-purple-200/80',
        badgeBg: 'bg-purple-100',
        badgeText: 'text-purple-700',
        svg: `<svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12 0c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
        </svg>`
      };
    }

    // Video
    if (['MP4', 'MOV', 'AVI', 'WEBM', 'MKV', 'M4V'].includes(extension) || mimeType.startsWith('video/')) {
      return {
        bgClass: 'bg-rose-50',
        textClass: 'text-rose-600',
        borderClass: 'border-rose-200/80',
        badgeBg: 'bg-rose-100',
        badgeText: 'text-rose-700',
        svg: `<svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>`
      };
    }

    // Default fallback
    return {
      bgClass: 'bg-slate-100',
      textClass: 'text-slate-600',
      borderClass: 'border-slate-200',
      badgeBg: 'bg-slate-200',
      badgeText: 'text-slate-700',
      svg: `<svg class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
      </svg>`
    };
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
      <div class="fed-modal-dialog bg-white rounded-3xl shadow-2xl border border-slate-200/90 w-full max-w-6xl h-[88vh] max-h-[88vh] flex flex-col overflow-hidden text-slate-800 font-sans transform transition-all duration-200" style="position: relative !important; z-index: 100000000 !important; background-color: #ffffff !important; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.4) !important;">
        
        <!-- Modal Header -->
        <div class="px-6 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-xs border border-indigo-100/60 shrink-0">
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

        <!-- Navigation Tabs & Search & Filters -->
        <div class="px-6 py-2.5 border-b border-slate-100 bg-white flex flex-wrap items-center justify-between gap-3 shrink-0">
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

          <div class="fed-library-controls flex items-center gap-2 flex-1 max-w-md justify-end">
            <!-- Filter Dropdown -->
            <select class="fed-media-type-filter text-xs border border-slate-200 bg-slate-50 hover:bg-white rounded-xl px-2.5 py-1.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden transition-all text-slate-700 cursor-pointer">
              <option value="all">All Media</option>
              <option value="image">Images</option>
              <option value="document">Documents / PDFs</option>
              <option value="audio">Audio</option>
              <option value="video">Video</option>
            </select>

            <!-- Search Input -->
            <div class="fed-library-search-wrapper relative flex-1 max-w-xs">
              <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <input type="text" class="fed-media-search-input w-full pl-8 pr-8 py-1.5 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-hidden" placeholder="Search media..." />
              <button type="button" class="fed-media-search-clear absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 hidden text-xs flex items-center justify-center">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-hidden relative flex flex-col">
          
          <!-- Library Tab View -->
          <div class="fed-view-library flex-1 flex flex-row overflow-hidden relative">
            
            <!-- Grid Container -->
            <div class="flex-1 p-5 overflow-y-auto fed-media-scroll-area flex flex-col relative">
              <div class="fed-media-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3.5 content-start flex-1">
                <!-- Dynamically populated cards -->
              </div>
              
              <!-- Load More Container -->
              <div class="fed-load-more-container py-5 text-center hidden">
                <button type="button" class="fed-load-more-btn inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-700 text-xs font-bold transition-all shadow-2xs hover:shadow-xs active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                  <span class="fed-load-more-spinner hidden w-3.5 h-3.5 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></span>
                  <span class="fed-load-more-text">Load More Media</span>
                </button>
              </div>

              <!-- Loading Overlay (strictly inside library view) -->
              <div class="fed-media-loader absolute inset-0 bg-white flex items-center justify-center z-20 hidden">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-8 h-8 border-3 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                  <span class="text-xs font-semibold text-slate-600">Loading media library...</span>
                </div>
              </div>

              <!-- Empty State for Library (strictly inside library view) -->
              <div class="fed-media-empty absolute inset-0 flex flex-col items-center justify-center p-6 text-center z-10 hidden">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-3">
                  <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-700 m-0 fed-empty-title">No media files found</h4>
                <p class="text-xs text-slate-400 mt-1 mb-4 fed-empty-desc">Try clearing filters or upload new files.</p>
                <button type="button" class="fed-switch-upload-btn px-4 py-1.5 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold transition-colors">
                  Upload Files
                </button>
              </div>
            </div>

            <!-- Attachment Details Sidebar (Hidden until an item is selected) -->
            <div class="fed-media-sidebar w-80 border-l border-slate-200/90 bg-slate-50/70 overflow-y-auto p-4 flex flex-col gap-4 shrink-0 transition-all duration-200 hidden">

              <!-- Multi-select state inside sidebar -->
              <div class="fed-sidebar-multi flex flex-col gap-3.5 hidden">
                <div class="p-3 bg-indigo-50/70 border border-indigo-100 rounded-2xl">
                  <div class="text-xs font-bold text-indigo-900 fed-multi-count-title">Multiple Items Selected</div>
                  <div class="text-[11px] text-indigo-600/90 mt-0.5">Settings below will be applied to all selected items upon insertion.</div>
                </div>

                <div class="fed-multi-thumbnails flex flex-wrap gap-1.5 max-h-28 overflow-y-auto p-1 bg-white border border-slate-200/80 rounded-xl">
                  <!-- Thumbnail previews -->
                </div>

                <!-- Display Settings (Multi) -->
                <div class="space-y-3 pt-2 border-t border-slate-200/60">
                  <h6 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Display Settings</h6>
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alignment</label>
                    <select class="fed-multi-align w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden">
                      <option value="none">None</option>
                      <option value="left">Left</option>
                      <option value="center">Center</option>
                      <option value="right">Right</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Link To</label>
                    <select class="fed-multi-link-to w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden">
                      <option value="none">None</option>
                      <option value="file">Media File</option>
                      <option value="post">Attachment Page</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Size</label>
                    <select class="fed-multi-size w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden">
                      <option value="full">Full Size</option>
                      <option value="large">Large</option>
                      <option value="medium">Medium</option>
                      <option value="thumbnail">Thumbnail</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Single item details form inside sidebar -->
              <div class="fed-sidebar-single flex flex-col gap-3.5 hidden">
                <!-- Preview card -->
                <div class="p-3 bg-white border border-slate-200/90 rounded-2xl flex items-center gap-3 shadow-2xs">
                  <div class="fed-single-thumb-wrap w-14 h-14 rounded-xl overflow-hidden border border-slate-200/80 bg-slate-100 shrink-0 flex items-center justify-center">
                    <!-- Thumbnail/Icon -->
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-slate-800 truncate m-0 fed-single-filename" title=""></p>
                    <p class="text-[10px] text-slate-400 mt-0.5 m-0 fed-single-meta"></p>
                    <p class="text-[10px] text-slate-400 mt-0.5 m-0 fed-single-date"></p>
                  </div>
                </div>

                <!-- Fields: Alt Text, Title, Caption, Description -->
                <div class="space-y-3">
                  <!-- Alt Text (images only) -->
                  <div class="fed-field-alt-wrap">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alt Text</label>
                    <input type="text" class="fed-single-alt w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden transition-all" placeholder="Alternative text for accessibility & SEO" />
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Describe the image for screen readers.</span>
                  </div>

                  <!-- Title -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Title</label>
                    <input type="text" class="fed-single-title w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden transition-all" placeholder="Media title" />
                  </div>

                  <!-- Caption -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Caption</label>
                    <textarea class="fed-single-caption w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden transition-all h-14 resize-none" placeholder="Add a caption..."></textarea>
                  </div>

                  <!-- Description -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
                    <textarea class="fed-single-description w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden transition-all h-12 resize-none" placeholder="Media description..."></textarea>
                  </div>
                </div>

                <!-- Attachment Display Settings -->
                <div class="space-y-3 pt-3 border-t border-slate-200/80">
                  <h6 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Attachment Display Settings</h6>

                  <!-- Alignment -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alignment</label>
                    <select class="fed-single-align w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden">
                      <option value="none">None</option>
                      <option value="left">Left</option>
                      <option value="center">Center</option>
                      <option value="right">Right</option>
                    </select>
                  </div>

                  <!-- Link To -->
                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Link To</label>
                    <select class="fed-single-link-to w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden">
                      <option value="none">None</option>
                      <option value="file">Media File</option>
                      <option value="post">Attachment Page</option>
                      <option value="custom">Custom URL</option>
                    </select>
                  </div>

                  <!-- Custom URL Input -->
                  <div class="fed-single-custom-url-wrap hidden">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Link URL</label>
                    <input type="url" class="fed-single-custom-url w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden" placeholder="https://example.com" />
                  </div>

                  <!-- Size (images only) -->
                  <div class="fed-field-size-wrap">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Size</label>
                    <select class="fed-single-size w-full text-xs border border-slate-200 rounded-xl px-2.5 py-1.5 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-hidden">
                      <option value="full">Full Size</option>
                      <option value="large">Large</option>
                      <option value="medium">Medium</option>
                      <option value="thumbnail">Thumbnail</option>
                    </select>
                  </div>
                </div>

                <!-- Auto-save notification -->
                <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-1">
                  <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span>Details saved automatically</span>
                </div>
              </div>

            </div>
          </div>

          <!-- Upload Tab View -->
          <div class="fed-view-upload flex-1 p-6 overflow-y-auto flex flex-col items-center justify-start hidden">
            
            <!-- Dropzone Area -->
            <div class="fed-dropzone-area w-full max-w-xl border-2 border-dashed border-slate-300 rounded-3xl p-8 flex flex-col items-center justify-center text-center hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer bg-slate-50/60 mb-6 shrink-0">
              <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 shadow-xs border border-indigo-100/60">
                <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              <h4 class="text-sm font-bold text-slate-800 mb-1">Drag & drop files here to upload</h4>
              <p class="text-xs text-slate-400 mb-4 fed-upload-formats-text">Supported formats: Images, Documents, Audio, Video, Archives</p>
              <button type="button" class="fed-select-files-btn px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs hover:shadow-indigo-500/25 transition-all">
                Select Files
              </button>
              <input type="file" class="fed-file-input hidden" multiple />
            </div>

            <!-- Upload Queue List (Individual Progress Bars) -->
            <div class="fed-upload-queue-container w-full max-w-xl flex flex-col gap-2.5 hidden">
              <div class="flex items-center justify-between px-1">
                <span class="text-xs font-bold text-slate-700 fed-upload-queue-title">Upload Progress</span>
                <span class="text-[11px] text-slate-400 fed-upload-queue-summary">0 of 0 uploaded</span>
              </div>
              <div class="fed-upload-queue-list flex flex-col gap-2 max-h-72 overflow-y-auto pr-1">
                <!-- Dynamically generated rows for each uploading file -->
              </div>
              <div class="text-center pt-2">
                <button type="button" class="fed-switch-to-library-btn px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-bold transition-colors">
                  View in Media Library →
                </button>
              </div>
            </div>

          </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
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

    // Prevent clicks inside dialog from bubbling to backdrop
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

    el.querySelector('.fed-switch-to-library-btn').addEventListener('click', () => {
      this.switchTab('library');
    });

    // Type filter dropdown: Instant client-side filter + Server query
    const filterSelect = el.querySelector('.fed-media-type-filter');
    filterSelect.addEventListener('change', (e) => {
      this.mimeFilter = e.target.value;
      this.selectedItems.clear();
      this.selectedItem = null;
      this.updateSelectionUI();
      // 1. Immediately filter existing items client-side for 0ms response time
      this.renderGrid();
      // 2. Query page 1 for full filtered set
      this.fetchMedia(1, false);
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
      }, 250);
    });

    searchClear.addEventListener('click', () => {
      searchInput.value = '';
      searchClear.classList.add('hidden');
      this.searchQuery = '';
      this.fetchMedia(1);
    });

    // Load More button
    const loadMoreBtn = el.querySelector('.fed-load-more-btn');
    loadMoreBtn.addEventListener('click', () => {
      if (this.currentPage < this.totalPages) {
        this.fetchMedia(this.currentPage + 1, true);
      }
    });

    // File Upload interactions
    const dropzone = el.querySelector('.fed-dropzone-area');
    const fileInput = el.querySelector('.fed-file-input');
    const selectBtn = el.querySelector('.fed-select-files-btn');

    selectBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      fileInput.click();
    });
    dropzone.addEventListener('click', (e) => {
      if (e.target !== selectBtn) fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
      if (e.target.files && e.target.files.length) {
        this.uploadFiles(Array.from(e.target.files));
        e.target.value = '';
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
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
        this.uploadFiles(Array.from(e.dataTransfer.files));
      }
    });

    // Bind Sidebar Details Form Inputs
    this.bindSidebarInputs();

    // Insert Button
    el.querySelector('.fed-media-insert-btn').addEventListener('click', () => {
      const items = Array.from(this.selectedItems.values());
      if (!items.length) return;

      const formattedResults = items.map(item => this.formatItemResult(item));

      if (typeof this.callback === 'function') {
        if (this.isMultiple) {
          this.callback(formattedResults, formattedResults[0]);
        } else {
          this.callback(formattedResults[0]);
        }
      }

      this.close();
    });
  }

  bindSidebarInputs() {
    const el = this.el;

    // Single item inputs
    const altInput = el.querySelector('.fed-single-alt');
    const titleInput = el.querySelector('.fed-single-title');
    const captionInput = el.querySelector('.fed-single-caption');
    const descInput = el.querySelector('.fed-single-description');
    const alignSelect = el.querySelector('.fed-single-align');
    const linkToSelect = el.querySelector('.fed-single-link-to');
    const customUrlInput = el.querySelector('.fed-single-custom-url');
    const sizeSelect = el.querySelector('.fed-single-size');

    const handleSingleChange = () => {
      if (!this.selectedItem) return;
      this.selectedItem.alt = altInput.value;
      this.selectedItem.title = titleInput.value;
      this.selectedItem.caption = captionInput.value;
      this.selectedItem.description = descInput.value;
      this.selectedItem.align = alignSelect.value;
      this.selectedItem.linkTo = linkToSelect.value;
      this.selectedItem.linkUrl = customUrlInput.value;
      this.selectedItem.size = sizeSelect.value;

      this.selectedItems.set(this.selectedItem.id, this.selectedItem);

      // Debounce saving to WordPress backend
      clearTimeout(this.saveDetailsTimeout);
      this.saveDetailsTimeout = setTimeout(() => {
        this.saveAttachmentDetails(this.selectedItem);
      }, 500);
    };

    [altInput, titleInput, captionInput, descInput, customUrlInput].forEach(inp => {
      inp.addEventListener('input', handleSingleChange);
    });

    alignSelect.addEventListener('change', () => {
      this.selectedAlign = alignSelect.value;
      handleSingleChange();
    });

    linkToSelect.addEventListener('change', () => {
      this.selectedLinkTo = linkToSelect.value;
      const customWrap = el.querySelector('.fed-single-custom-url-wrap');
      customWrap.classList.toggle('hidden', linkToSelect.value !== 'custom');
      handleSingleChange();
    });

    sizeSelect.addEventListener('change', () => {
      this.selectedSize = sizeSelect.value;
      handleSingleChange();
    });

    // Multi-select inputs
    const multiAlign = el.querySelector('.fed-multi-align');
    const multiLinkTo = el.querySelector('.fed-multi-link-to');
    const multiSize = el.querySelector('.fed-multi-size');

    multiAlign.addEventListener('change', () => {
      this.selectedAlign = multiAlign.value;
      this.selectedItems.forEach(item => {
        item.align = multiAlign.value;
      });
    });

    multiLinkTo.addEventListener('change', () => {
      this.selectedLinkTo = multiLinkTo.value;
      this.selectedItems.forEach(item => {
        item.linkTo = multiLinkTo.value;
      });
    });

    multiSize.addEventListener('change', () => {
      this.selectedSize = multiSize.value;
      this.selectedItems.forEach(item => {
        item.size = multiSize.value;
      });
    });
  }

  async saveAttachmentDetails(item) {
    if (!item || !item.id) return;
    const ajaxUrl = this.getAjaxUrl();
    const nonce = this.getNonce();

    const formData = new FormData();
    formData.append('action', 'fed_save_attachment_details');
    formData.append('fed_nonce', nonce);
    formData.append('id', item.id);
    formData.append('alt', item.alt || '');
    formData.append('title', item.title || '');
    formData.append('caption', item.caption || '');
    formData.append('description', item.description || '');

    try {
      await fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });
    } catch (e) {
      console.warn('Could not save attachment details:', e);
    }
  }

  formatItemResult(item) {
    let selectedUrl = item.url || item.full;
    const chosenSize = item.size || this.selectedSize || 'full';

    if (item.is_image) {
      if (chosenSize === 'thumbnail' && item.thumbnail) selectedUrl = item.thumbnail;
      else if (chosenSize === 'medium' && item.medium) selectedUrl = item.medium;
      else if (chosenSize === 'large' && item.large) selectedUrl = item.large;
    }

    return {
      id: item.id,
      title: item.title || item.filename || '',
      url: selectedUrl,
      full: item.full || item.url || '',
      alt: item.alt || item.title || '',
      caption: item.caption || '',
      description: item.description || '',
      align: item.align || this.selectedAlign || 'none',
      linkTo: item.linkTo || this.selectedLinkTo || 'none',
      linkUrl: item.linkUrl || '',
      link: item.link || item.url || '',
      size: chosenSize,
      width: item.width || '',
      height: item.height || '',
      filename: item.filename || '',
      mime: item.mime || '',
      is_image: Boolean(item.is_image),
      sizes: {
        thumbnail: { url: item.thumbnail || item.url },
        medium: { url: item.medium || item.url },
        large: { url: item.large || item.url },
        full: { url: item.full || item.url }
      }
    };
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

    const controlsWrapper = el.querySelector('.fed-library-controls');
    const libraryView = el.querySelector('.fed-view-library');
    const uploadView = el.querySelector('.fed-view-upload');

    if (tab === 'library') {
      controlsWrapper.classList.remove('hidden');
      libraryView.classList.remove('hidden');
      uploadView.classList.add('hidden');
      if (!this.items.length && !this.isLoading) {
        this.fetchMedia(1);
      }
    } else {
      controlsWrapper.classList.add('hidden');
      libraryView.classList.add('hidden');
      uploadView.classList.remove('hidden');
    }
  }

  open(callback, options = {}) {
    this.callback = callback;
    this.options = options || {};
    this.isOpen = true;
    this.selectedItems.clear();
    this.selectedItem = null;
    this.selectedSize = 'full';
    this.selectedAlign = 'none';
    this.selectedLinkTo = 'none';
    this.selectedLinkUrl = '';

    const el = this.el;
    el.style.setProperty('display', 'flex', 'important');
    document.body.style.overflow = 'hidden';

    // Set modal title
    if (this.options.title) {
      el.querySelector('.fed-modal-title').textContent = this.options.title;
    } else {
      el.querySelector('.fed-modal-title').textContent = 'Select Media';
    }

    // Set insert button text
    if (this.options.button && this.options.button.text) {
      el.querySelector('.fed-media-insert-btn').textContent = this.options.button.text;
    } else {
      el.querySelector('.fed-media-insert-btn').textContent = 'Insert into Post';
    }

    // File input accept & filter options
    const fileInput = el.querySelector('.fed-file-input');
    const filterSelect = el.querySelector('.fed-media-type-filter');
    const formatsText = el.querySelector('.fed-upload-formats-text');

    if (this.options.mime === 'image') {
      this.mimeFilter = 'image';
      fileInput.accept = 'image/*';
      filterSelect.value = 'image';
      filterSelect.classList.add('hidden');
      formatsText.textContent = 'Supported formats: JPG, PNG, GIF, WEBP, SVG';
    } else {
      this.mimeFilter = this.options.mime || 'all';
      fileInput.removeAttribute('accept');
      filterSelect.value = this.mimeFilter;
      filterSelect.classList.remove('hidden');
      formatsText.textContent = 'Supported formats: Images, Documents, Audio, Video, Archives (PDF, DOCX, ZIP, etc.)';
    }

    this.updateSelectionUI();
    this.setLoading(true);
    this.switchTab('library');
    this.fetchMedia(1);
  }

  close() {
    this.isOpen = false;
    const el = this.el;
    el.style.setProperty('display', 'none', 'important');
    document.body.style.overflow = '';
    this.selectedItems.clear();
    this.selectedItem = null;
    this.updateSelectionUI();

    if (this.currentAbortController) {
      this.currentAbortController.abort();
    }
  }

  async fetchMedia(page = 1, append = false) {
    if (this.currentAbortController) {
      this.currentAbortController.abort();
    }
    const abortController = new AbortController();
    this.currentAbortController = abortController;

    this.currentPage = page;

    const loadMoreBtn = this.el.querySelector('.fed-load-more-btn');
    const loadMoreSpinner = this.el.querySelector('.fed-load-more-spinner');
    const loadMoreText = this.el.querySelector('.fed-load-more-text');

    if (append) {
      if (loadMoreBtn) loadMoreBtn.disabled = true;
      if (loadMoreSpinner) loadMoreSpinner.classList.remove('hidden');
      if (loadMoreText) loadMoreText.textContent = 'Loading more...';
    } else {
      this.setLoading(true);
      const emptyState = this.el.querySelector('.fed-media-empty');
      if (emptyState) emptyState.classList.add('hidden');
      const grid = this.el.querySelector('.fed-media-grid');
      if (grid) grid.innerHTML = '';
    }

    const ajaxUrl = this.getAjaxUrl();
    const nonce = this.getNonce();

    const formData = new FormData();
    formData.append('action', 'fed_get_media_library');
    formData.append('fed_nonce', nonce);
    formData.append('search', this.searchQuery);
    formData.append('page', page);
    if (this.mimeFilter && this.mimeFilter !== 'all') {
      formData.append('mime', this.mimeFilter);
    }

    try {
      const res = await fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        signal: abortController.signal
      });

      if (abortController.signal.aborted) return;

      const data = await res.json();
      if (abortController.signal.aborted) return;

      if (data.success && data.data && data.data.items) {
        if (append) {
          const existingIds = new Set(this.items.map(i => i.id));
          const newItems = data.data.items.filter(i => !existingIds.has(i.id));
          this.items = this.items.concat(newItems);
        } else {
          this.items = data.data.items;
        }
        this.totalPages = data.data.total_pages || 1;
      } else {
        if (!append) {
          this.items = [];
        }
      }
    } catch (err) {
      if (err.name !== 'AbortError') {
        console.error('Error fetching media library:', err);
        if (!append) {
          this.items = [];
        }
      }
    } finally {
      if (!abortController.signal.aborted) {
        if (append) {
          if (loadMoreBtn) loadMoreBtn.disabled = false;
          if (loadMoreSpinner) loadMoreSpinner.classList.add('hidden');
          if (loadMoreText) loadMoreText.textContent = 'Load More Media';
        } else {
          this.setLoading(false);
        }
        this.renderGrid();
      }
    }
  }

  renderGrid() {
    const el = this.el;
    const grid = el.querySelector('.fed-media-grid');
    const emptyState = el.querySelector('.fed-media-empty');
    const loadMoreContainer = el.querySelector('.fed-load-more-container');

    grid.innerHTML = '';

    // Filter items client-side strictly by current filter
    const activeFilter = this.mimeFilter || 'all';
    const displayItems = this.items.filter(item => {
      const isImg = Boolean(item.is_image) || (item.mime && item.mime.toLowerCase().startsWith('image/'));
      const isAud = Boolean(item.mime && item.mime.toLowerCase().startsWith('audio/'));
      const isVid = Boolean(item.mime && item.mime.toLowerCase().startsWith('video/'));
      const isDoc = !isImg && !isAud && !isVid;

      if (activeFilter === 'image') return isImg;
      if (activeFilter === 'document') return isDoc;
      if (activeFilter === 'audio') return isAud;
      if (activeFilter === 'video') return isVid;
      return true;
    });

    if (!displayItems.length) {
      if (!this.isLoading) {
        emptyState.classList.remove('hidden');
        const titleEl = emptyState.querySelector('.fed-empty-title');
        const descEl = emptyState.querySelector('.fed-empty-desc');
        const btnEl = emptyState.querySelector('.fed-switch-upload-btn');

        if (titleEl && descEl && btnEl) {
          if (this.searchQuery) {
            titleEl.textContent = `No results found for "${this.searchQuery}"`;
            descEl.textContent = 'Try checking for spelling errors or searching with a different term.';
            btnEl.textContent = 'Upload Files';
          } else if (activeFilter === 'audio') {
            titleEl.textContent = 'No audio files found';
            descEl.textContent = 'Upload audio tracks or recordings (MP3, WAV, OGG, M4A) to get started.';
            btnEl.textContent = 'Upload Audio';
          } else if (activeFilter === 'video') {
            titleEl.textContent = 'No video files found';
            descEl.textContent = 'Upload video recordings or clips (MP4, MOV, WEBM) to get started.';
            btnEl.textContent = 'Upload Video';
          } else if (activeFilter === 'document') {
            titleEl.textContent = 'No documents or PDFs found';
            descEl.textContent = 'Upload PDF documents, Word files, spreadsheets, or text files.';
            btnEl.textContent = 'Upload Documents';
          } else if (activeFilter === 'image') {
            titleEl.textContent = 'No image files found';
            descEl.textContent = 'Upload JPG, PNG, GIF, or WEBP images to get started.';
            btnEl.textContent = 'Upload Images';
          } else {
            titleEl.textContent = 'No media files found';
            descEl.textContent = 'Upload files to add them to your media library.';
            btnEl.textContent = 'Upload Files';
          }
        }
      } else {
        emptyState.classList.add('hidden');
      }
      loadMoreContainer.classList.add('hidden');
      return;
    }
    emptyState.classList.add('hidden');

    if (this.currentPage < this.totalPages) {
      loadMoreContainer.classList.remove('hidden');
    } else {
      loadMoreContainer.classList.add('hidden');
    }

    displayItems.forEach(item => {
      const card = document.createElement('div');
      const isSelected = this.selectedItems.has(item.id);
      const isImage = Boolean(item.is_image) || (item.mime && item.mime.toLowerCase().startsWith('image/'));

      card.className = `fed-media-card group relative aspect-square rounded-2xl overflow-hidden border-2 cursor-pointer transition-all duration-150 select-none ${
        isSelected 
          ? 'border-indigo-600 ring-4 ring-indigo-500/20 shadow-md bg-indigo-50/20' 
          : 'border-slate-200/90 hover:border-indigo-300 hover:shadow-xs bg-slate-50'
      }`;

      let mediaPreviewHtml = '';
      if (isImage) {
        mediaPreviewHtml = `
          <img src="${item.thumbnail || item.url}" alt="${escapeAttr(item.title)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-slate-900/75 via-slate-900/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-2 text-white">
            <span class="text-[10px] font-bold truncate leading-tight">${escapeHtml(item.title || item.filename)}</span>
            <span class="text-[9px] text-slate-300">${item.filesize || ''}</span>
          </div>
        `;
      } else {
        const fileExt = item.ext || (item.filename ? item.filename.split('.').pop().toUpperCase() : 'FILE');
        const iconConfig = this.getFileIconConfig(fileExt, item.mime);

        mediaPreviewHtml = `
          <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center bg-slate-50/80 group-hover:bg-slate-100/70 transition-colors">
            <div class="w-12 h-12 rounded-2xl ${iconConfig.bgClass} ${iconConfig.textClass} flex flex-col items-center justify-center shadow-2xs border ${iconConfig.borderClass} mb-2 group-hover:scale-110 transition-transform duration-150">
              ${iconConfig.svg}
            </div>
            <span class="text-[10px] font-bold text-slate-700 truncate w-full px-1 leading-tight" title="${escapeAttr(item.filename || item.title)}">
              ${escapeHtml(item.filename || item.title)}
            </span>
            <div class="flex items-center gap-1 mt-1">
              <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded-md ${iconConfig.badgeBg} ${iconConfig.badgeText}">
                ${fileExt}
              </span>
              ${item.filesize ? `<span class="text-[9px] text-slate-400">${item.filesize}</span>` : ''}
            </div>
          </div>
        `;
      }

      card.innerHTML = `
        ${mediaPreviewHtml}
        ${
          isSelected 
            ? `<div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shadow-sm animate-in zoom-in-50 z-10"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg></div>` 
            : ''
        }
      `;

      card.addEventListener('click', () => {
        this.selectItem(item);
      });

      grid.appendChild(card);
    });
  }

  selectItem(item, forceSelect = false) {
    if (this.isMultiple) {
      if (this.selectedItems.has(item.id) && !forceSelect) {
        this.selectedItems.delete(item.id);
        const values = Array.from(this.selectedItems.values());
        this.selectedItem = values.length ? values[values.length - 1] : null;
      } else {
        this.selectedItems.set(item.id, item);
        this.selectedItem = item;
      }
    } else {
      if (this.selectedItem && this.selectedItem.id === item.id && !forceSelect) {
        this.selectedItem = null;
        this.selectedItems.clear();
      } else {
        this.selectedItem = item;
        this.selectedItems.clear();
        this.selectedItems.set(item.id, item);
      }
    }

    this.renderGrid();
    this.updateSelectionUI();
  }

  updateSelectionUI() {
    const el = this.el;
    const insertBtn = el.querySelector('.fed-media-insert-btn');
    const counter = el.querySelector('.fed-selection-counter');
    const count = this.selectedItems.size;

    if (count > 0) {
      insertBtn.disabled = false;
      counter.innerHTML = count === 1 
        ? `<span class="font-bold text-slate-700">1</span> item selected`
        : `<span class="font-bold text-slate-700">${count}</span> items selected`;
    } else {
      insertBtn.disabled = true;
      counter.textContent = this.isMultiple ? 'No items selected' : 'No item selected';
    }

    this.renderSidebar();
  }

  renderSidebar() {
    const el = this.el;
    const sidebar = el.querySelector('.fed-media-sidebar');
    const multiState = el.querySelector('.fed-sidebar-multi');
    const singleState = el.querySelector('.fed-sidebar-single');
    const count = this.selectedItems.size;

    if (count === 0) {
      if (sidebar) sidebar.classList.add('hidden');
      if (multiState) multiState.classList.add('hidden');
      if (singleState) singleState.classList.add('hidden');
      return;
    }

    if (sidebar) sidebar.classList.remove('hidden');

    if (count > 1) {
      if (singleState) singleState.classList.add('hidden');
      if (multiState) multiState.classList.remove('hidden');

      el.querySelector('.fed-multi-count-title').textContent = `${count} Items Selected`;
      const thumbsContainer = el.querySelector('.fed-multi-thumbnails');
      thumbsContainer.innerHTML = '';

      this.selectedItems.forEach(item => {
        const thumb = document.createElement('div');
        thumb.className = 'w-9 h-9 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 shrink-0';
        const isImage = Boolean(item.is_image) || (item.mime && item.mime.startsWith('image/'));
        if (isImage) {
          thumb.innerHTML = `<img src="${item.thumbnail || item.url}" class="w-full h-full object-cover" />`;
        } else {
          const fileExt = item.ext || 'FILE';
          thumb.innerHTML = `<div class="w-full h-full flex items-center justify-center font-bold text-[9px] text-slate-600">${fileExt}</div>`;
        }
        thumbsContainer.appendChild(thumb);
      });

      return;
    }

    // Exactly 1 item selected: render full detail editing form
    if (multiState) multiState.classList.add('hidden');
    if (singleState) singleState.classList.remove('hidden');

    const item = this.selectedItem;
    if (!item) return;

    const isImage = Boolean(item.is_image) || (item.mime && item.mime.startsWith('image/'));
    const fileExt = item.ext || (item.filename ? item.filename.split('.').pop().toUpperCase() : 'FILE');

    // Thumbnail / Icon
    const thumbWrap = el.querySelector('.fed-single-thumb-wrap');
    if (isImage) {
      thumbWrap.innerHTML = `<img src="${item.thumbnail || item.url}" alt="${escapeAttr(item.title)}" class="w-full h-full object-cover" />`;
    } else {
      const iconConfig = this.getFileIconConfig(fileExt, item.mime);
      thumbWrap.innerHTML = `<div class="w-full h-full ${iconConfig.bgClass} ${iconConfig.textClass} flex items-center justify-center font-extrabold text-xs uppercase">${fileExt}</div>`;
    }

    // Filename & Meta
    const nameEl = el.querySelector('.fed-single-filename');
    const metaEl = el.querySelector('.fed-single-meta');
    const dateEl = el.querySelector('.fed-single-date');

    nameEl.textContent = item.filename || item.title || '';
    nameEl.title = item.filename || item.title || '';

    const metaParts = [];
    if (isImage && (item.dimensions || (item.width && item.height))) {
      metaParts.push(item.dimensions || `${item.width} × ${item.height}`);
    } else if (fileExt) {
      metaParts.push(fileExt);
    }
    if (item.filesize) metaParts.push(item.filesize);
    metaEl.textContent = metaParts.join(' • ');

    dateEl.textContent = item.date ? `Uploaded on ${item.date}` : '';

    // Populate Fields
    const altWrap = el.querySelector('.fed-field-alt-wrap');
    const altInput = el.querySelector('.fed-single-alt');
    const titleInput = el.querySelector('.fed-single-title');
    const captionInput = el.querySelector('.fed-single-caption');
    const descInput = el.querySelector('.fed-single-description');
    const alignSelect = el.querySelector('.fed-single-align');
    const linkToSelect = el.querySelector('.fed-single-link-to');
    const customUrlWrap = el.querySelector('.fed-single-custom-url-wrap');
    const customUrlInput = el.querySelector('.fed-single-custom-url');
    const sizeWrap = el.querySelector('.fed-field-size-wrap');
    const sizeSelect = el.querySelector('.fed-single-size');

    // Toggle image-specific fields
    altWrap.classList.toggle('hidden', !isImage);
    sizeWrap.classList.toggle('hidden', !isImage);

    altInput.value = item.alt || '';
    titleInput.value = item.title || '';
    captionInput.value = item.caption || '';
    descInput.value = item.description || '';

    alignSelect.value = item.align || this.selectedAlign || 'none';
    linkToSelect.value = item.linkTo || this.selectedLinkTo || 'none';
    customUrlInput.value = item.linkUrl || '';
    customUrlWrap.classList.toggle('hidden', linkToSelect.value !== 'custom');
    sizeSelect.value = item.size || this.selectedSize || 'full';
  }

  formatBytes(bytes) {
    if (!bytes) return '';
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return `${parseFloat((bytes / Math.pow(1024, i)).toFixed(1))} ${sizes[i]}`;
  }

  async uploadFiles(fileList) {
    const el = this.el;
    const queueContainer = el.querySelector('.fed-upload-queue-container');
    const queueList = el.querySelector('.fed-upload-queue-list');
    const queueSummary = el.querySelector('.fed-upload-queue-summary');

    const totalFiles = fileList.length;
    if (!totalFiles) return;

    queueContainer.classList.remove('hidden');
    queueList.innerHTML = '';
    queueSummary.textContent = `0 of ${totalFiles} uploaded`;

    const queueItems = [];

    // Create individual UI progress rows for each file
    fileList.forEach((file, index) => {
      const ext = file.name.split('.').pop().toUpperCase();
      const iconConfig = this.getFileIconConfig(ext, file.type);
      const rowId = `fed-upload-row-${Date.now()}-${index}`;

      const row = document.createElement('div');
      row.id = rowId;
      row.className = 'fed-upload-item bg-white border border-slate-200/90 rounded-2xl p-3 flex flex-col gap-2 shadow-2xs transition-all';
      row.innerHTML = `
        <div class="flex items-center justify-between gap-3">
          <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-xl ${iconConfig.bgClass} ${iconConfig.textClass} flex items-center justify-center shrink-0 border ${iconConfig.borderClass}">
              <span class="font-extrabold text-[9px] uppercase">${ext}</span>
            </div>
            <div class="min-w-0">
              <div class="text-xs font-bold text-slate-800 truncate" title="${escapeAttr(file.name)}">${escapeHtml(file.name)}</div>
              <div class="text-[10px] text-slate-400">${this.formatBytes(file.size)}</div>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="fed-row-status text-[11px] font-semibold text-slate-400">Waiting...</span>
            <span class="fed-row-percent text-[11px] font-bold text-indigo-600">0%</span>
            <div class="fed-row-icon w-4 h-4 flex items-center justify-center">
              <div class="w-3.5 h-3.5 rounded-full border-2 border-slate-300"></div>
            </div>
          </div>
        </div>
        <!-- Individual Progress Bar -->
        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
          <div class="fed-row-bar h-full bg-indigo-600 rounded-full transition-all duration-150" style="width: 0%"></div>
        </div>
      `;

      queueList.appendChild(row);
      queueItems.push({ file, row, rowId });
    });

    const ajaxUrl = this.getAjaxUrl();
    const nonce = this.getNonce();
    let completedCount = 0;

    // Upload helper with genuine per-file XHR progress tracking
    const uploadSingleFile = (itemObj) => {
      return new Promise((resolve) => {
        const { file, row } = itemObj;
        const statusEl = row.querySelector('.fed-row-status');
        const percentEl = row.querySelector('.fed-row-percent');
        const barEl = row.querySelector('.fed-row-bar');
        const iconEl = row.querySelector('.fed-row-icon');

        statusEl.textContent = 'Uploading...';
        statusEl.className = 'fed-row-status text-[11px] font-semibold text-indigo-600';
        iconEl.innerHTML = `<div class="w-3.5 h-3.5 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>`;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', ajaxUrl, true);
        xhr.withCredentials = true;

        xhr.upload.addEventListener('progress', (e) => {
          if (e.lengthComputable) {
            const percent = Math.min(99, Math.round((e.loaded / e.total) * 100));
            barEl.style.width = `${percent}%`;
            percentEl.textContent = `${percent}%`;
          }
        });

        xhr.onload = () => {
          try {
            const data = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && data.success && data.data) {
              barEl.style.width = '100%';
              barEl.classList.remove('bg-indigo-600');
              barEl.classList.add('bg-emerald-500');
              percentEl.textContent = '100%';
              percentEl.className = 'fed-row-percent text-[11px] font-bold text-emerald-600';
              statusEl.textContent = 'Completed';
              statusEl.className = 'fed-row-status text-[11px] font-semibold text-emerald-600';
              iconEl.innerHTML = `<svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`;

              const newItem = data.data;
              this.items.unshift(newItem);
              if (this.isMultiple) {
                this.selectItem(newItem, true);
              } else {
                this.selectItem(newItem, false);
              }

              completedCount++;
              queueSummary.textContent = `${completedCount} of ${totalFiles} uploaded`;
              resolve(true);
            } else {
              const msg = data.data && data.data.message ? data.data.message : 'Failed';
              statusEl.textContent = msg;
              statusEl.className = 'fed-row-status text-[11px] font-semibold text-rose-600';
              percentEl.textContent = 'Error';
              percentEl.className = 'fed-row-percent text-[11px] font-bold text-rose-600';
              barEl.classList.add('bg-rose-500');
              iconEl.innerHTML = `<svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>`;
              resolve(false);
            }
          } catch (e) {
            statusEl.textContent = 'Error';
            resolve(false);
          }
        };

        xhr.onerror = () => {
          statusEl.textContent = 'Network Error';
          resolve(false);
        };

        const formData = new FormData();
        formData.append('action', 'fed_upload_media_file');
        formData.append('fed_nonce', nonce);
        formData.append('file', file);
        xhr.send(formData);
      });
    };

    // Upload with concurrency of 2 parallel files
    const concurrency = 2;
    const pool = [...queueItems];
    const workers = Array(Math.min(concurrency, pool.length)).fill(0).map(async () => {
      while (pool.length > 0) {
        const item = pool.shift();
        await uploadSingleFile(item);
      }
    });

    await Promise.all(workers);

    // After all files finish, auto-switch to library after short delay
    setTimeout(() => {
      this.switchTab('library');
      this.renderGrid();
      this.updateSelectionUI();
    }, 600);
  }

  setLoading(val) {
    this.isLoading = val;
    const loader = this.el.querySelector('.fed-media-loader');
    const emptyState = this.el.querySelector('.fed-media-empty');
    if (loader) loader.classList.toggle('hidden', !val);
    if (val && emptyState) {
      emptyState.classList.add('hidden');
    }
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
