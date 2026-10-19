/**
 * Frontend Dashboard - PDF Preview Generator
 *
 * Uses Mozilla PDF.js to render a crisp page-1 thumbnail for PDF files
 * directly in the browser when server-side thumbnails are unavailable.
 */

import * as pdfjsLib from 'pdfjs-dist/build/pdf';
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.min.js?url';

if (pdfjsLib.GlobalWorkerOptions) {
  pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;
}

const previewCache = new Map();
const inFlightRequests = new Map();

/**
 * Generate or retrieve a cached thumbnail data URL for a PDF file's first page.
 *
 * @param {string} url PDF URL
 * @param {number} targetWidth Target width of the generated thumbnail (default 320)
 * @returns {Promise<string|null>} Data URL of the rendered page, or null on failure
 */
export async function getPdfPreview(url, targetWidth = 320) {
  if (!url) return null;

  if (previewCache.has(url)) {
    return previewCache.get(url);
  }

  if (inFlightRequests.has(url)) {
    return inFlightRequests.get(url);
  }

  const promise = (async () => {
    try {
      const loadingTask = pdfjsLib.getDocument({
        url: url,
        withCredentials: true,
        cMapUrl: undefined,
        cMapPacked: true
      });

      const pdf = await loadingTask.promise;
      const page = await pdf.getPage(1);

      const unscaledViewport = page.getViewport({ scale: 1 });
      const scale = targetWidth / unscaledViewport.width;
      const viewport = page.getViewport({ scale: Math.max(scale, 0.5) });

      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      canvas.width = Math.floor(viewport.width);
      canvas.height = Math.floor(viewport.height);

      await page.render({
        canvasContext: ctx,
        viewport: viewport
      }).promise;

      const dataUrl = canvas.toDataURL('image/jpeg', 0.82);
      previewCache.set(url, dataUrl);
      return dataUrl;
    } catch (err) {
      console.warn('Frontend Dashboard: PDF preview generation skipped/failed for', url, err);
      return null;
    } finally {
      inFlightRequests.delete(url);
    }
  })();

  inFlightRequests.set(url, promise);
  return promise;
}
