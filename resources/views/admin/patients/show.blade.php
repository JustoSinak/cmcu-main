@extends('layouts.admin')
@section('title', 'CMCU | dossier patient')
@section('content')

<style>
    :root {
        --primary-blue: #4169E1;
        --secondary-blue: #6495ED;
        --danger-red: #DC3545;
        --success-green: #28A745;
        --light-gray: #F8F9FA;
        --border-light: #E9ECEF;
    }

    body {
        background-color: var(--light-gray);
    }

    .patient-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(65, 105, 225, 0.3);
    }

    .patient-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        justify-content: center;
    }

    .action-buttons .btn {
        border-radius: 25px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.875rem;
    }

    .action-buttons .btn i {
        margin-right: 0.5rem;
    }

    .btn-secondary:hover,
    .btn-info:hover,
    .btn-dark:hover,
    .btn-success:hover,
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .main-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        border: none;
        overflow: hidden;
    }

    .main-card .card-body {
        padding: 2rem;
    }

    .card-title {
        color: var(--danger-red) !important;
        font-weight: 700 !important;
        margin-bottom: 1.5rem !important;
        font-size: 1.5rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    .table-user-information {
        background-color: white;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        overflow: hidden;
    }

    .table-user-information tr {
        border-bottom: 1px solid var(--border-light);
    }

    .table-user-information tr:last-child {
        border-bottom: none;
    }

    .table-user-information td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .table-user-information tr:hover {
        background-color: #F8F9FA;
    }

    /* Sidebar Styles */
    .sidebar-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: none;
        position: sticky;
        top: 1rem;
    }

    .sidebar-card .card-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: white;
        border: none;
        padding: 1rem 1.25rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sidebar-card .card-body {
        padding: 1.25rem;
    }

    .sidebar-card .btn {
        width: 100%;
        margin-bottom: 0.75rem;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        text-align: left;
        border: none;
        display: flex;
        align-items: center;
    }

    .sidebar-card .btn:last-child {
        margin-bottom: 0;
    }

    .sidebar-card .btn i {
        margin-right: 0.625rem;
        width: 1.125rem;
        flex-shrink: 0;
    }

    .sidebar-card .btn:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .return-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background-color: var(--success-green);
        color: white;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 0.875rem;
    }

    .return-button:hover {
        background-color: #218838;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .grid-container {
        display: grid;
        grid-gap: 2rem;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        padding: 0.625rem;
    }

    .grid-item {
        background-color: white;
        border: 1px solid var(--border-light);
        padding: 1rem;
        font-size: 0.875rem;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .grid-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .section-title {
        color: var(--secondary-blue);
        font-size: 1.25rem;
        font-weight: 600;
        margin: 1.5rem 0 1rem 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 3px solid var(--primary-blue);
        padding-bottom: 0.625rem;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .sidebar-card {
            position: static;
            margin-top: 2rem;
        }
        
        .patient-header h2 {
            font-size: 1.5rem;
        }
        
        .card-title {
            font-size: 1.25rem !important;
        }
    }

    @media (max-width: 575.98px) {
        .action-buttons {
            flex-direction: column;
        }
        
        .action-buttons .btn {
            width: 100%;
        }
        
        .patient-header {
            padding: 1.5rem;
        }
        
        .main-card .card-body {
            padding: 1.25rem;
        }
    }
</style>

<div class="wrapper">
    @include('partials.side_bar')
    @include('partials.header')
    
    @can('show', \App\Models\User::class)
    <div class="container-fluid px-3 px-lg-4">
        @include('partials.flash')
        
        <div class="row mb-3">
            <div class="col-12">
                @include('admin.patients.partials.menu')
                <a href="{{ route('patients.index') }}" class="return-button float-end" title="Retour à la liste des patients">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Main Content Area --}}
            @if(auth()->user()->role_id == 1)
                <div class="col-lg-8 col-xl-9">
            @endif
            @if(auth()->user()->role_id == 6)
                <div class="col-lg-8 col-xl-9">
            @endif
            @if(auth()->user()->role_id == 2)
                <div class="col-lg-9 col-xl-10">
            @endif
            @if(auth()->user()->role_id == 4)
                <div class="col-lg-8 col-xl-9">
            @endif
                        <div class="main-card">
                            <div class="card-body">
                                <h2 class="card-title text-center">DOSSIER PATIENT {{ $patient->name}} {{$patient->prenom}}</h2>
                            
                                <div class="action-buttons text-center">
                                    <button class="btn btn-secondary" title="Cacher / Afficher les données personnelles du patient" onclick="ShowDetailsPatient()">
                                        <i class="fas fa-eye"></i> Détails Personnels
                                    </button>
                                    
                                    @can('infirmier_secretaire', \App\Models\Patient::class)
                                    <a href="{{ route('dossiers.create', $patient->id) }}" class="btn btn-info">
                                        <i class="fas fa-clipboard-list"></i> Compléter le dossier
                                    </a>
                                    @endcan
                                    
                                    @can('secretaire', \App\Models\Patient::class)
                                    <button class="btn btn-secondary" title="Modifier le motif et le montant" onclick="ShoweditMotif_montant()">
                                        <i class="fas fa-edit"></i> Éditer
                                    </button>
                                    @endcan
                                    
                                    @can('med_inf_anes', \App\Models\Patient::class)
                                    <a class="btn btn-dark" href="{{ route('fiche.prescription_medicale.index', $patient) }}" title="Prescriptions médicales">
                                        <i class="fas fa-book-medical"></i> Prescriptions Médicales
                                    </a>
                                    @endcan
                                    
                                    @can('infirmier', \App\Models\Patient::class)
                                        @isset($dossiers)
                                        <a class="btn btn-secondary" href="{{ route('consultations.create', $patient->id) }}" title="Nouvelle consultation du patient pour la prise des paramètres">
                                            <i class="fas fa-heartbeat"></i> Fiche De Paramètres
                                        </a>
                                        @endisset
                                        @empty($dossiers)
                                        <a class="btn btn-secondary" href="#" data-placement="top" data-toggle="popover" data-trigger="focus" data-content="Vous devez d'abord compléter le dossier patient !" title="Fiche de prise des paramètres">
                                            <i class="fas fa-heartbeat"></i> Fiche De Paramètres
                                        </a>
                                        @endempty
                                    @endcan
                                    
                                    @can('medecin_secretaire', \App\Models\Patient::class)
                                    <button class="btn btn-secondary" title="Gérer les images scannées des examens" onclick="Showexamen_scannes()">
                                        <i class="fas fa-image"></i> Images Scannées
                                    </button>
                                    @endcan
                                </div>
                            
                                <table class="table table-user-information table-hover mt-3">
                                    @include('admin.consultations.partials.detail_patient')
                                    @include('admin.consultations.show_consultation')
                                    @include('admin.consultations.partials.motif_et_montant')
                                </table>
                            
                                @include('admin.patients.partials.examens_scannes')
                            </div>
                        </div>
                    </div>

            {{-- Sidebar Area (only for roles 1, 2, 4, and 6) --}}
            @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 4 || auth()->user()->role_id == 6)
                    <div class="col-lg-{{ auth()->user()->role_id == 2 ? '3' : '4' }} col-xl-{{ auth()->user()->role_id == 2 ? '2' : '3' }}">
                        @can('med_inf_anes', \App\Models\Patient::class)
                        <div class="sidebar-card">
                            <div class="card-header">
                                <small>DÉTAILS ACTION</small>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-primary" title="Liste des ordonnances pour ce patient" data-toggle="modal" data-target="#ordonanceAll" data-whatever="@mdo">
                                    <i class="fas fa-file-prescription"></i> Ordonnances
                                </button>
                                        
                                <button type="button" class="btn btn-primary" title="Liste des examens pour ce patient" data-toggle="modal" data-target="#biologieAll" data-whatever="@mdo">
                                    <i class="fas fa-flask"></i> Examens Biologiques
                                </button>

                                @can('anesthesiste', \App\Models\Patient::class)
                                <a href="{{ route('surveillance_rapproche.index', $patient->id) }}" title="Surveillance rapprochée des paramètres" class="btn btn-primary">
                                    <i class="fas fa-heartbeat"></i> Surveillance Rapprochée
                                </a>
                                @endcan
                                        
                                @can('chirurgien', \App\Models\Patient::class)
                                <a href="{{ route('consultations.index_anesthesiste', $patient->id) }}" class="btn btn-primary">
                                    <i class="fas fa-user-md"></i> Consultations Anesthésistes
                                </a>
                                        
                                <a href="{{ route('surveillance_rapproche.index', $patient->id) }}" title="Surveillance rapprochée des paramètres" class="btn btn-primary">
                                    <i class="fas fa-heartbeat"></i> Surveillance Rapprochée
                                </a>
                                @endcan
                                        
                                @can('infirmier', \App\Models\Patient::class)
                                <a href="{{ route('surveillance_rapproche.index', $patient->id) }}" title="Surveillance rapprochée des paramètres" class="btn btn-primary">
                                    <i class="fas fa-heartbeat"></i> Surveillance Rapprochée
                                </a>
                                        
                                <a href="{{ route('consultations.index_anesthesiste', $patient->id) }}" class="btn btn-primary">
                                    <i class="fas fa-user-md"></i> Consultations anesthésistes
                                </a>
                                        
                                <a href="{{ route('surveillance_post_anesthesise.index', $patient->id) }}" class="btn btn-primary" title="Détails surveillance post-anesthésiste">
                                    <i class="fas fa-hospital-user"></i> Surveillance Post-Anesthésique
                                </a>
                                        
                                <button type="button" class="btn btn-primary" title="Fiches d'intervention" data-toggle="modal" data-target="#FicheInterventionAll" data-whatever="@mdo">
                                    <i class="fas fa-tasks"></i> Fiche d'Intervention
                                </button>
                                        
                                <a href="{{ route('dossiers.create', $patient->id) }}" class="btn btn-info">
                                    Compléter Le Dossier
                                </a>
                                        
                                @if (count($patient->consultations))
                                    @can('medecin', \App\Models\Patient::class)
                                    <a class="btn btn-success" title="Imprimer la lettre de sortie" href="{{ route('print.sortie', $patient->id) }}">
                                        <i class="fas fa-print"></i> Lettre De Consultation
                                    </a>
                                            
                                    <button type="button" class="btn btn-primary" title="Liste de fiches pour ce patient" data-toggle="modal" data-target="#ficheSuiviAll" data-whatever="@mdo">
                                        <i class="fas fa-file-alt"></i> Fiche De Suivi
                                    </button>
                                    @endcan
                                @endif
                                @endcan
                            </div>
                        </div>
                        @endcan
                    </div>
                    @endif
                </div>

                {{-- MODALS --}}
                @include('admin.modal.feuille_precription_examen')
                @include('admin.modal.detail_premedication_preparation')
                @include('admin.modal.ordonance_show')
                @include('admin.modal.consultation_show')
                @include('admin.modal.index_examen_biologie')
                @include('admin.modal.index_examen_imagerie')
                @include('admin.modal.fiche_intervention_show')
                @include('admin.modal.fiche_intervention')
                @include('admin.modal.fiche_intervention_anesthesiste')
                @include('admin.modal.visite_preanesthesique')
                @include('admin.modal.surveillance_post_a')
                @include('admin.modal.fichede_suivi')
            </div>
            @endcan
        </div>
    </div>
</div>    
<script>
    function ShowDetailsPatient() {
        var x = document.getElementById("myDIV");
        var y = document.getElementById("editMotifMontform");
        var z = document.getElementById("examens_scannes_form");
        if (y.style.display === "contents") {
            y.style.display = "none";
        }
        if (z.style.display === "contents") {
            z.style.display = "none";
        }
        if (x.style.display === "none") {
            x.style.display = "contents";
        } else {
            x.style.display = "none";
        }
    }

    function ShoweditMotif_montant() {
        var x = document.getElementById("editMotifMontform");
        var y = document.getElementById("myDIV");
        var z = document.getElementById("examens_scannes_form");
        if (y.style.display === "contents") {
            y.style.display = "none";
        }
        if (z.style.display === "contents") {
            z.style.display = "none";
        }
        if (x.style.display === "none") {
            x.style.display = "contents";
        } else {
            x.style.display = "none";
        }
    }

    function Showexamen_scannes() {
        var x = document.getElementById("editMotifMontform");
        var y = document.getElementById("myDIV");
        var z = document.getElementById("examens_scannes_form");
        var t = document.getElementById("show_consultation");
        if (y.style.display === "contents") {
            y.style.display = "none";
        }
        if (x.style.display === "contents") {
            x.style.display = "none";
        }
        if (t.style.display === "contents") {
            t.style.display = "none";
        }
        if (z.style.display === "none") {
            z.style.display = "contents";
        } else {
            z.style.display = "none";
            t.style.display = "contents";
        }
    }

    // File input display
    $(".form-control").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".form-label").addClass("selected").html(fileName);
    });

    // Image preview handler
    function handleFiles(files) {
        var imageType = /^image\//;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (!imageType.test(file.type)) {
                alert("Veuillez sélectionner une image");
            } else {
                let form_parent = document.getElementById('preview');
                let img1 = document.getElementById("img1");
                let clone_img = img1.cloneNode(false);
                clone_img.file = file;
                clone_img.classList.add("obj");
                form_parent.replaceChild(clone_img, img1);
                var reader = new FileReader();
                reader.onload = (function(aImg) {
                    return function(e) {
                        aImg.src = e.target.result;
                    };
                })(clone_img);
                reader.readAsDataURL(file);
            }
        }
    }
     // Initialize Bootstrap 5 popovers
        // var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        // var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        //     return new bootstrap.Popover(popoverTriggerEl);
        // });
</script>

@stop