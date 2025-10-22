<!DOCTYPE html>
<html lang="fr">

<head>
    <title>
        @yield('title', 'CMCU')
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/assets/sass/app.scss', 'resources/css/all.scss'])
    <link rel="stylesheet" href="{{ asset('admin/datatables/css/dataTables.bootstrap4.css') }}" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('admin/images/faviconlogo.ico') }}" />

    <link href="//fonts.googleapis.com/css?family=Poiret+One" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css">
    
    <!-- Load jQuery first, before Vite -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    @yield('link')
    <script>
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>

</head>
<body>
<!-- Modal content here -->
<div id="myModal" data-backdrop="static" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <img src="{{ asset('admin/images/licence_image.jpg') }}" class="offset-4">
            </div>
            <div class="modal-body">
                @include('partials.flash_form')
                <h1 class="text-center text-danger">VOTRE LICENCE A EXPIRE</h1>
                <br>
                <br>
                <form action="{{ route('active_licence_key') }}" method="POST" class="form-group">
                    @csrf
                    <label for=""><b>Veuillez saisir la clé de licence reçu par mail ici</b></label>
                    <textarea name="license_key" cols="30" rows="5" class="form-control" required></textarea>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fas fa-check text-danger"></i> Valider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@yield('content')

<!-- Load Vite bundles -->
@vite(['resources/js/all.js', 'resources/assets/js/app.js', 'resources/js/typehead.js'])

<!-- Load jQuery-dependent scripts AFTER Vite -->
<script src="{{ asset('admin/datatables/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('admin/datatables/js/dataTables.bootstrap4.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.js"></script>
<script src="{{ asset('vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>

<!-- Wrap all jQuery code in document ready -->
<script>
    $(document).ready(function() {
        // DataTables initialization
        $('#myTable').DataTable({
            "dom": '<"top"i <"d-flex justify-content-between"l<"toolbar">f>>rt<"bottom d-flex justify-content-between mt-3"p><"clear">',
            scrollX: true,
            processing: true,
            info: false,
            ordering: false,
            initComplete: function() {
                this.api().column('#statut').every(function() {
                    var column = this;
                    var select = $('<select><option value="" selected>Tout</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );

                            column
                                .search(val ? '^' + val + '$' : '', true, false)
                                .draw();
                        });

                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d + '</option>')
                    });
                });
            },
            language: {
                processing: "Traitement en cours...",
                search: "Rechercher&nbsp;:",
                lengthMenu: "Afficher _MENU_ &eacute;l&eacute;ments",
                info: "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
                infoEmpty: "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
                infoFiltered: "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
                infoPostFix: "",
                loadingRecords: "Chargement en cours...",
                zeroRecords: "Aucun &eacute;l&eacute;ment &agrave; afficher",
                emptyTable: "Aucune donnée disponible dans le tableau",
                paginate: {
                    first: "Premier",
                    previous: "Pr&eacute;c&eacute;dent",
                    next: "Suivant",
                    last: "Dernier"
                },
                aria: {
                    sortAscending: ": activer pour trier la colonne par ordre croissant",
                    sortDescending: ": activer pour trier la colonne par ordre décroissant"
                }
            }
        });
        
        $("div.toolbar").html($('.table_info'));
        $("div.bottom").prepend($('.table_link_right'));
        
        $('.filter-select').change(function() {
            table.column($(this).data('column'))
                .search($(this).val())
                .draw();
        });

        // Typeahead
        var path = "{{ route('autocomplete') }}";
        $('#search').typeahead({
            minLength: 1,
            hint: true,
            highlight: true,
            source: function(query, process) {
                return $.get(path, {
                    query: query
                }, function(data) {
                    return process(data);
                });
            },
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Table toggler
        $(".tbtn").click(function() {
            $(this).parents(".custom-table").find(".toggler1").removeClass("toggler1");
            $(this).parents("tbody").find(".toggler").addClass("toggler1");
            $(this).parents(".custom-table").find(".fa-minus-circle").removeClass("fa-minus-circle");
            $(this).parents("tbody").find(".fa-plus-circle").addClass("fa-minus-circle");
        });

        // Popover
        $('[data-toggle="popover"]').popover();

        // Mode paiement change handler
        $('#mode_paiement').change(function(event){
            if($(this).val() == 'chèque' && !$('._cheque').length){
                $('.m_paiement').after(
                    '<div class="form-group _cheque">'+
                        '<label for="num_cheque" class="col-form-label text-md-right" >Numéro du chèque </label>'+
                        '<input name="num_cheque" id="num_cheque" class="form-control" value="" type="text"  >'+
                    '</div>'+
                    '<div class="form-group _cheque">'+
                        '<label for="emetteur_cheque" class="col-form-label text-md-right" >Emetteur du chèque </label>'+
                        '<input name="emetteur_cheque" id="emetteur_cheque" class="form-control" value="" type="text" >'+
                    '</div>'+
                    '<div class="form-group _cheque">'+
                        '<label for="banque_cheque" class="col-form-label text-md-right">Banque </label>'+
                        '<input name="banque_cheque" id="banque_cheque" class="form-control" value="" type="text" >'+
                    '</div>'
                );
            }
            else{
                if($('._cheque').length){
                    $("._cheque").remove();
                }
            }
            
            if($(this).val() == 'bon de prise en charge' && !$('._bpc').length){
                $('.m_paiement').after(
                    '<div class="form-group _bpc">'+
                        '<label for="emetteur_bpc" class="col-form-label text-md-right" title="Somme des précédents versements du client">Emetteur </label>'+
                        '<input name="emetteur_bpc" id="emetteur_bpc" class="form-control" value="" type="text" >'+
                    '</div>'
                );
            }
            else{
                if($('._bpc').length){
                    $("._bpc").remove();
                }
            }
        });
    });
</script>

@yield('script')

@php
$licence = \App\Models\Licence::where('client', 'cmcuapp')->first();
@endphp
<!-- 
@if ($licence && $licence->expire_date <= \Carbon\Carbon::now())
<script type="text/javascript">
    $(window).on('load', function(){
        $('#myModal').modal('show');
    });
</script>
@endif -->


@unless(env('BYPASS_LICENSE'))
    @if ($licence && $licence->expire_date <= \Carbon\Carbon::now())
    <script type="text/javascript">
        waitForjQuery(function() {
            $(window).on('load',function(){
                $('#myModal').modal('show');
            });
        });
    </script>
    @endif
@endunless


@include('flash::message')

</body>
</html>