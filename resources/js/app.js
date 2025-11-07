import '../assets/js/bootstrap';
import '../assets/sass/app.scss';

// Import Font Awesome
import '@fortawesome/fontawesome-free/js/all';

// Import DataTables
import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';

// Define waitForjQuery globally
window.waitForjQuery = function(callback) {
    if (window.jQuery) {
        callback();
    } else {
        setTimeout(() => waitForjQuery(callback), 50);
    }
};

// Initialize DataTables
$(document).ready(function() {
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            pageLength: 25,
            responsive: true
        });
    }
});
