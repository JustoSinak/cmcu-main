# TODO: Add Pagination to Patients List

## Steps to Complete
- [x] Modify `PatientsController@index` to fetch all patients with `->paginate(10)` and pass to view
- [x] Modify `PatientsController@search` to use `->paginate(10)` for search results
- [x] Edit `index.blade.php` to uncomment `{{ $patients->links() }}` and conditionally show search message only when `$name` is set
- [ ] Test pagination on the index page and search results
