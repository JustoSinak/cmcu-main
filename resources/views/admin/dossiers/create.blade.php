@extends('layouts.admin')

@section('title', 'CMCU | Renseigner un dossier patient')

@section('content')

    <body>
    <div class="se-pre-con"></div>
    <div class="wrapper">
    @include('partials.side_bar')

    <!-- Page Content Holder -->
    @include('partials.header')
    <!--// top-bar -->
        <div class="container">
            <h1 class="text-center">RENSEIGNER LE DOSSIER DE {{ $patient->name }} {{$patient->prenom}}</h1>
            <hr>
                    <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-success float-end"
                       title="Retour à la liste des patients">
                        <i class="fas fa-arrow-left"></i> Retour au dossier patient
                    </a>
            @include('partials.flash_form')
            <form class="row g-3 mt-4" method="post" action="{{ route('dossiers.store') }}">
                @csrf

                <div class="col-md-6">
                    <label for="sexe" class="form-label"><b>Sexe :</b></label>
                    <div class="small">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sexe" id="sexe_masculin" value="Masculin">
                            <label class="form-check-label" for="sexe_masculin">
                                Masculin
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="sexe" id="sexe_feminin" value="Féminin">
                            <label class="form-check-label" for="sexe_feminin">
                                Féminin
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <input class="form-control" type="hidden" name="patient_id" value="{{ $patient->id }}">
                </div>

                <div class="col-sm-4">
                    <label for="date_naissance" class="form-label"><b>Date de naissance :</b></label>
                    <input type="date" class="form-control" value="{{ old('date_naissance') }}" name="date_naissance" placeholder="Date de naissance">
                </div>

                <div class="col-sm-6">
                    <label for="lieu_naissance" class="form-label"><b>Lieu de naissance :</b></label>
                    <input type="text" class="form-control" value="{{ old('lieu_naissance') }}" name="lieu_naissance" placeholder="Lieu de naissance">
                </div>

                <div class="col-sm-4">
                    <label for="portable_1" class="form-label"><b>Portable :</b></label>
                    <input type="number" value="{{ old('portable_1') }}" class="form-control" name="portable_1" placeholder="Portable">
                </div>

                <div class="col-sm-4 ms-md-auto">
                    <label for="portable_2" class="form-label"><b>Portable :</b></label>
                    <input type="number" value="{{ old('portable_2') }}" class="form-control" name="portable_2" placeholder="Portable 2">
                </div>

                <div class="col-sm-4">
                    <label for="fax" class="form-label"><b>Fax :</b></label>
                    <input type="text" value="{{ old('fax') }}" class="form-control" name="fax" placeholder="Fax">
                </div>

                <div class="col-sm-4 ms-md-auto">
                    <label for="email" class="form-label"><b>Email :</b></label>
                    <input type="email" value="{{ old('email') }}" class="form-control" name="email" placeholder="Adresse email du patient">
                </div>

                <div class="col-sm-6">
                    <label for="profession" class="form-label"><b>Profession :</b></label>
                    <input type="text" value="{{ old('profession') }}" class="form-control" name="profession" placeholder="Profession du patient">
                </div>

                <div class="col-sm-4">
                    <label for="adresse" class="form-label"><b>Adresse :</b></label>
                    <input type="text" class="form-control" value="{{ old('adresse') }}" name="adresse" placeholder="Adresse du patient">
                </div>

                <div class="col-sm-6">
                    <label for="personne_confiance" class="form-label"><b>Personne de confiance :</b></label>
                    <input type="text" class="form-control" value="{{ old('personne_confiance') }}" name="personne_confiance" placeholder="Personne de confiance">
                </div>

                <div class="col-sm-4">
                    <label for="tel_personne_confiance" class="form-label"><b>Téléphone personne de confiance :</b></label>
                    <input type="number" class="form-control" value="{{ old('tel_personne_confiance') }}" name="tel_personne_confiance" placeholder="Téléphone personne de confiance">
                </div>

                <div class="col-sm-6">
                    <label for="personne_contact" class="form-label"><b>Personne à contacter :</b></label>
                    <input type="text" class="form-control" value="{{ old('personne_contact') }}" name="personne_contact" placeholder="Personne à contacter">
                </div>

                <div class="col-sm-4">
                    <label for="tel_personne_contact" class="form-label"><b>Téléphone personne à contacter :</b></label>
                    <input type="number" class="form-control" value="{{ old('tel_personne_contact') }}" name="tel_personne_contact" placeholder="Téléphone personne à contacter">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    </body>

@stop