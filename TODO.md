# Migration from Laravel Mix to Vite

- [x] Delete webpack.mix.js
- [x] Create vite.config.js with Laravel plugin and entry points
- [x] Create resources/css/all.scss importing admin styles
- [x] Create resources/js/all.js importing admin scripts
- [x] Convert require() to import in resources/assets/js/app.js
- [x] Convert require() to import in resources/assets/js/bootstrap.js
- [x] Update Blade templates to use @vite() instead of mix()
- [x] Run npm install if needed
- [x] Test npm run dev and npm run build
- [x] Fix build errors by moving scripts to resources/js/admin/ and using npm packages
