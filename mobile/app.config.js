// GitHub Pages serves the web build under /<repo-name>/ (see `npm run build:pages`);
// local development and native builds keep the root path.
module.exports = ({ config }) => ({
  ...config,
  experiments: {
    ...config.experiments,
    ...(process.env.GH_PAGES_BASE ? { baseUrl: process.env.GH_PAGES_BASE } : {}),
  },
});
