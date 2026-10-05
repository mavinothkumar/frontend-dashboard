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
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'assets/js/main.js'),
        style: resolve(__dirname, 'assets/css/main.css')
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
