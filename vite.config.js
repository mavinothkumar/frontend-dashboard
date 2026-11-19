import { defineConfig } from 'vite';
import { resolve } from 'path';
import { fileURLToPath } from 'url';

const __dirname = fileURLToPath(new URL('.', import.meta.url));

export default defineConfig({
  plugins: [],
  base: process.env.NODE_ENV === 'production' ? '/wp-content/plugins/frontend-dashboard/assets/dist/' : '/',
  build: {
    outDir: resolve(__dirname, 'assets/dist'),
    emptyOutDir: true,
    manifest: true,
    chunkSizeWarningLimit: 1200,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'assets/js/main.js'),
        style: resolve(__dirname, 'assets/css/main.css')
      },
      onwarn(warning, warn) {
        // Suppress benign eval warning from pdfjs-dist's Node.js fallback check
        if (
          warning.code === 'EVAL' &&
          (
            (warning.id && warning.id.includes('pdfjs-dist')) ||
            (warning.loc && warning.loc.file && warning.loc.file.includes('pdfjs-dist')) ||
            (warning.message && warning.message.includes('pdfjs-dist'))
          )
        ) {
          return;
        }
        warn(warning);
      }
    }
  },
  server: {
    cors: true,
    strictPort: true,
    port: 3000,
    hmr: {
      protocol: 'ws',
      host: 'localhost'
    }
  }
});
