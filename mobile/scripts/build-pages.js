// Static web build for GitHub Pages: `npm run build:pages` → dist-pages/
// The site is served under /AmicaleLFK/, and Pages has no SPA fallback, so index.html is also
// written as 404.html (deep links such as /AmicaleLFK/annuaire still open the app).
// .nojekyll keeps Pages from dropping files whose names start with "_".
const { execFileSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const out = path.join(root, 'dist-pages');
execFileSync(process.execPath, [require.resolve('expo/bin/cli', { paths: [root] }), 'export', '-p', 'web', '--output-dir', 'dist-pages'], {
  cwd: root,
  stdio: 'inherit',
  env: { ...process.env, GH_PAGES_BASE: process.env.GH_PAGES_BASE || '/AmicaleLFK' },
});
fs.copyFileSync(path.join(out, 'index.html'), path.join(out, '404.html'));
fs.writeFileSync(path.join(out, '.nojekyll'), '');
console.log('GitHub Pages build ready in dist-pages/');
