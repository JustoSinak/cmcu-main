@extends('layouts.admin')

@section('title', 'CMCU | Liste des devis')

@section('content')
<div class="wrapper">
    @include('partials.side_bar')
    @include('partials.header')
    
    @can('create', \App\Models\Patient::class)
    <div class="container">
        <h1 class="text-center fs-2">LISTE DES DEVIS</h1>
    </div>
    <hr>
    <div class="container pt-3">
        <div class="row">
            <div class="col-sm-12 panneau_d_affichage">
                <div class="table-responsive">
                    @include('partials.flash')
                    <table id="devisTable" class="table table-bordered table-hover w-100">
                        <thead>
                            <tr>
                                <th>NOM</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devis as $devi)
                            <tr>
                                <td>{{ $devi->nom }}</td>
                                <td>
                                    @can('print', \App\Models\Devi::class)
                                    <button type="button" 
                                            data-devi='@json($devi)' 
                                            data-champ_patient="" 
                                            data-bs-toggle="modal" 
                                            data-title="Impression devis ..." 
                                            data-texte="Vous pouvez effectuez des modifications si nécessaire." 
                                            data-target="#imprimer_devis" 
                                            class="btn btn-sm btn-info me-1" 
                                            title="Attribuer le devis à un patient">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('create', \App\Models\Devi::class)
    <div class="text-center table_link_right">
        <button type="button" 
                data-bs-toggle="modal" 
                data-title="Nouveau devis ..." 
                data-texte="" 
                data-target="#imprimer_devis" 
                class="btn btn-primary me-1" 
                title="Vous allez ajouter un nouveau devis" 
                data-champ_patient="d-none">
            Nouveau
        </button>
    </div>
    @endcan
</div>

<!-- Modal -->
<div class="modal fade" id="imprimer_devis">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <p class="text-success description my-2"></p>
                    <form id="devis_form" action="" method="POST">
                        @csrf
                        <!-- Form content here -->
                    </form>
                </div>
            </div>
            <div class="modal-footer px-0">
                <div class="col-12">
                    @can('update', \App\Models\Devi::class)
                    <button type="submit" class="btn btn-info devis_save" data-bs-dismiss="modal">Enregistrer</button>
                    @endcan
                    <button type="button" class="btn btn-danger float-end" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary float-end mx-3 devis_export" data-bs-dismiss="modal">Exporter</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('admin/js/devis/convert_chiffre_lettre.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable with proper options
    const table = $('#devisTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']],
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        initComplete: function() {
            // Any initialization complete code
        }
    });

    // Your existing modal show handler
    $("#imprimer_devis").on('show.bs.modal', function(e) {
        $(".ligne").remove(); // Remove previously loaded lines
        $('.ajouter_ligne').find('button').removeClass('d-none');
        $(this).find('.description').text($(e.relatedTarget).data('texte'));
        $(this).find('.modal-title').text($(e.relatedTarget).data('title'));
        $(this).find('.champ_patient').parent().addClass($(e.relatedTarget).data('champ_patient'));
        
        const devi = $(e.relatedTarget).data('devi');
        // Rest of your modal handling code
    });
    
    // Rest of your existing JavaScript code
});
</script>
@endsection
