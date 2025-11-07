@extends('layouts.admin')

@section('title', 'CMCU | Renseigner un dossier patient')

@section('content')
<div class="se-pre-con"></div>
<div class="wrapper">
    @include('partials.side_bar')
    @include('partials.header')

    <div class="container">
        <h1 class="text-center">RENSEIGNER LE DOSSIER DE {{ $patient->name }} {{ $patient->prenom }}</h1>
        <hr>
        <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-success float-end" title="Retour à la liste des patients">
            <i class="fas fa-arrow-left"></i> Retour au dossier patient
        </a>

        @include('partials.flash_form')

        <form class="row mt-4" method="post" action="{{ route('dossiers.store') }}">
            @csrf

            <div class="col-md-6 pb-3">
                <label><strong>Sexe :</strong></label>
                <div class="mb-3 small">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sexe" value="Masculin" id="sexe_m">
                        <label class="form-check-label" for="sexe_m">Masculin</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sexe" value="Féminin" id="sexe_f">
                        <label class="form-check-label" for="sexe_f">Féminin</label>
                    </div>
                </div>
            </div>

            <input type="hidden" name="patient_id" value="{{ $patient->id }}">

            <div class="col-md-6 pb-3">
                <label for="date_naissance"><strong>Date de naissance :</strong></label>
                <input type="date" class="form-control" name="date_naissance" value="{{ old('date_naissance') }}" placeholder="Date de naissance">
            </div>

            <div class="col-md-6 pb-3">
                <label for="lieu_naissance"><strong>Lieu de naissance :</strong></label>
                <input type="text" class="form-control" name="lieu_naissance" value="{{ old('lieu_naissance') }}" placeholder="Lieu de naissance">
            </div>

            <div class="col-md-6 pb-3">
                <label for="portable_1"><strong>Portable :</strong></label>
                <input type="number" class="form-control" name="portable_1" value="{{ old('portable_1') }}" placeholder="Portable">
            </div>

            <div class="col-md-6 pb-3">
                <label for="portable_2"><strong>Portable 2 :</strong></label>
                <input type="number" class="form-control" name="portable_2" value="{{ old('portable_2') }}" placeholder="Portable 2">
            </div>

            <div class="col-md-6 pb-3">
                <label for="fax"><strong>Fax :</strong></label>
                <input type="text" class="form-control" name="fax" value="{{ old('fax') }}" placeholder="Fax">
            </div>

            <div class="col-md-6 pb-3">
                <label for="email"><strong>Email :</strong></label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Adresse email du patient">
            </div>

            <div class="col-md-6 pb-3">
                <label for="profession"><strong>Profession :</strong></label>
                <input type="text" class="form-control" name="profession" value="{{ old('profession') }}" placeholder="Profession du patient">
            </div>

            <div class="col-md-6 pb-3">
                <label for="adresse"><strong>Adresse :</strong></label>
                <input type="text" class="form-control" name="adresse" value="{{ old('adresse') }}" placeholder="Adresse du patient">
            </div>

            <div class="col-md-6 pb-3">
                <label for="personne_confiance"><strong>Personne de confiance :</strong></label>
                <input type="text" class="form-control" name="personne_confiance" value="{{ old('personne_confiance') }}" placeholder="Personne de confiance">
            </div>

            <div class="col-md-6 pb-3">
                <label for="tel_personne_confiance"><strong>Téléphone personne de confiance :</strong></label>
                <input type="number" class="form-control" name="tel_personne_confiance" value="{{ old('tel_personne_confiance') }}" placeholder="Téléphone personne de confiance">
            </div>

            <div class="col-md-6 pb-3">
                <label for="personne_contact"><strong>Personne à contacter :</strong></label>
                <input type="text" class="form-control" name="personne_contact" value="{{ old('personne_contact') }}" placeholder="Personne à contacter">
            </div>

            <div class="col-md-6 pb-3">
                <label for="tel_personne_contact"><strong>Téléphone personne à contacter :</strong></label>
                <input type="number" class="form-control" name="tel_personne_contact" value="{{ old('tel_personne_contact') }}" placeholder="Téléphone personne à contacter">
            </div>

            <div class="col-md-10 d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary w-50">
                    <i class="fas fa-check-circle"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
