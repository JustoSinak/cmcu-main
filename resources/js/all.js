import $ from 'jquery';
window.$ = window.jQuery = $;

// Import DataTables to attach to jQuery
import 'datatables.net';
import 'datatables.net-bs5';

// Import FroalaEditor and make it global
import FroalaEditor from 'froala-editor';
window.FroalaEditor = FroalaEditor;

import 'typeahead.js';
import './admin/script.js';
import './admin/main.js';
