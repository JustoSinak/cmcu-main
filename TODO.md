
# Laravel Package Migration TODO

## Completed Tasks
- [x] Remove old packages: maddhatter/laravel-fullcalendar, barryvdh/laravel-dompdf, mercury-series/flashy, unisharp/laravel-ckeditor, laravelcollective/html, intervention/image
- [x] Install new packages: laracasts/flash, spatie/laravel-google-calendar, zanysoft/laravel-pdf, spatie/laravel-html, spatie/laravel-image-optimizer, barryvdh/laravel-ide-helper
- [x] Update config/app.php: Comment out Intervention\Image service provider and alias; add Spatie\Html and ZanySoft\LaravelPDF aliases
- [x] Update controllers: Comment out Barryvdh\DomPDF imports; add ZanySoft\LaravelPDF imports in DevisController.php, OrdonancesController.php, ProduitsController.php, PrescriptionController.php; MercurySeries\Flashy commented out in AdminController.php; Intervention\Image commented out in PatientimageController.php
- [x] Reinstall CKEditor with legacy peer deps
- [x] Regenerate IDE helpers

## Pending Tasks
- [ ] Replace Form:: usages in blade templates with native HTML or Spatie\Html equivalents
- [ ] Replace Html:: usages in blade templates with Spatie\Html equivalents
- [ ] Update flash message handling if needed (currently using @include('flash::message'))
- [ ] Test PDF generation with ZanySoft\LaravelPDF
- [ ] Test image handling without Intervention\Image (if needed, implement alternative)
- [ ] Test calendar functionality with Spatie\GoogleCalendar (requires credentials setup)
- [ ] Test form submissions and validations
- [ ] Run application and check for errors

## Notes
- Intervention\Image was removed but may need replacement for image processing
- Spatie\LaravelImageOptimizer is installed but not yet configured or used
- Google Calendar integration requires service account credentials
- Form:: and Html:: from Collective are still in use; need to migrate to Spatie\Html or native HTML
- Flash messages are handled by laracasts/flash via @include('flash::message')
