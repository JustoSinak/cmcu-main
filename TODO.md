# Bootstrap 5 Update Plan

## Tasks
- [x] Update Bootstrap 3 attributes to Bootstrap 5 equivalents in main app views (data-toggle → data-bs-toggle, etc.)
- [x] Remove or replace references to bootstrap3.3.7.css in main app layouts/views
- [ ] Update any Bootstrap 3 specific classes if needed
- [ ] Keep etat views as they are since they are for printing and may need Bootstrap 3
- [ ] Test the build and ensure no conflicts
- [ ] Run npm install and build to ensure Bootstrap 5 works
- [ ] Test UI components for Bootstrap 5 compatibility

## Dependent Files
- resources/views/layouts/admin.blade.php (already uses data-bs-toggle)
- resources/views/layouts/calender.blade.php (updated to remove bootstrap.css reference)
- Various admin views with Bootstrap 3 references
- public/admin/css/bootstrap3.3.7.css (may need removal if not used)
