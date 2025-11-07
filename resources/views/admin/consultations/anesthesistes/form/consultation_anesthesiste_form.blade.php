@if($consultation_anesthesiste->id)
    {{ Html::model($consultation_anesthesiste, ['route' => ['consultation_anesthesiste.update', $consultation_anesthesiste->id], 'method' => 'put', 'class'=>'form-horizontal form-label-left']) }}
@else
    {{ Html::open(['route' => 'consultation_anesthesiste.store', 'method' => 'post', 'class'=>'form-horizontal form-label-left']) }}
@endif
@csrf
<tr>
    <td>
        <h5 class="text-primary"><strong>CONSULTATION</strong></h5>
    </td>
    <td></td>
</tr>
<tr>
    <td><b>{{ Html::label('Specialité :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::text('specialite', null, ['class' => 'form-control', 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Médecin traitant :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::text('medecin_traitant', null, ['class' => 'form-control', 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Opérateur :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::text('operateur', null, ['class' => 'form-control', 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Date d\'intervention :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::date('date_intervention', null, ['class' => 'form-control col-md-6', 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Motif d\'admission :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::text('motif_admission', null, ['class' => 'form-control', 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Mémo :') }}</b> </td>
    <td>{{ Html::textarea('memo', null, ['class' => 'form-control splitLines', 'rows' => 4]) }}</td>
</tr>
<tr>
    <td><b>Anesthésie en salle d'opération :</b> <span class="text-danger">*</span></td>
    <td class="form-group small">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="anesthesi_salle[]" value="Ambulatoire" {{old( 'anesthesi_salle', $consultation_anesthesiste->anesthesi_salle)=='Ambulatoire' ? 'checked' : '' }}> Ambulatoire
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="anesthesi_salle[]" value="Urgence" {{old( 'anesthesi_salle', $consultation_anesthesiste->anesthesi_salle)=='Urgence' ? 'checked' : '' }}> Urgence
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="anesthesi_salle[]" value="Entrée le jour de l'intervention" {{old( 'anesthesi_salle', $consultation_anesthesiste->anesthesi_salle)=='Entrée le jour de l\'intervention' ? 'checked' : '' }}> Entrée le jour de l'intervention </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="anesthesi_salle[]" value="Hospit < 10 jours" {{old( 'anesthesi_salle', $consultation_anesthesiste->anesthesi_salle)=='Hospit < 10 jours' ? 'checked' : '' }}> Hospit < 10 jours
        </div>
    </td>
</tr>
<tr>
    <td><b>{{ Html::label('Date d\'hospitalisation :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::date('date_hospitalisation', null, ['class' => 'form-control col-md-6']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Service :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::text('service', null, ['class' => 'form-control', 'placeholder' => "Ex: Urologie", 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Classe ASA :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::text('classe_asa', null, ['class' => 'form-control', 'placeholder' => "Classe ASA", 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Antécédents / Traitements :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::textarea('antecedent_traitement', null, ['class' => 'form-control splitLines', 'rows' => 5, 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Examens cliniques :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::textarea('examen_clinique', null, ['class' => 'form-control splitLines', 'rows' => 5, 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Allergies :') }}</b> </td>
    <td>{{ Html::textarea('allergie', null, ['class' => 'form-control splitLines', 'rows' => 5]) }}</td>
</tr>
<tr>
    <td></td>
    <td>
        <b>{{ Html::label('Intubation :') }}</b>
        {{ Html::text('intubation', null, ['class' => 'form-control']) }}

        <b>{{ Html::label('Mallampati :') }}</b>
        {{ Html::text('mallampati', null, ['class' => 'form-control']) }}

        <b>{{ Html::label('Distance-interincisive :') }}</b>
        {{ Html::text('distance_interincisive', null, ['class' => 'form-control']) }}

        <b>{{ Html::label('Distance thyromentonière :') }}</b>
        {{ Html::text('distance_thyromentoniere', null, ['class' => 'form-control']) }}

        <b>{{ Html::label('Mobilité cervicale :') }}</b>
    {{ Html::text('mobilite_servicale', null, ['class' => 'form-control']) }}
    <td>
</tr>
<tr>
    <td><b>{{ Html::label('Traitement en cours :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::textarea('traitement_en_cours', null, ['class' => 'form-control splitLines', 'rows' => 5, 'required' => 'required']) }}</td>
</tr>
<tr>
    <td><b>{{ Html::label('Rique(s) :') }}</b> <span class="text-danger">*</span></td>
    <td>{{ Html::textarea('risque', null, ['class' => 'form-control splitLines', 'rows' => 5, 'required' => 'required']) }}</td>
</tr>
<tr>
    <td>
        <h5 class="text-primary"><strong>DECISON / PRESCRIPTIONS</strong></h5>
    </td>
    <td></td>
</tr>
<tr>
    <td><b>Informations données au patient :</b> </td>
    <td>
        <b>{{ Html::label('Technique d\'anesthésie :') }}</b> <span class="text-danger">*</span>
        {{ Html::text('technique_anesthesie1', null, ['class' => 'form-control', 'required' => 'required']) }}

        <b>{{ Html::label('Bénéfice / Risque :') }}</b> <span class="text-danger">*</span>
        {{ Html::textarea('benefice_risque', null, ['class' => 'form-control splitLines', 'rows' => 5, 'required' => 'required']) }}

        <b>{{ Html::label('Jeune préopératoire :') }}</b> <span class="text-danger">*</span>
        <div class="form-check">
            <p>Solides : {{ Html::text('solide', null, ['class' => 'offset-2 mb-1 ml-10', 'placeholder' => ' H-']) }}</p>
        </div>
        <div class="form-check">
            <p>Liquides clairs : {{ Html::text('liquide', null, ['class' => 'offset-1 ml-4', 'placeholder' => ' H-']) }}</p>
        </div>

        <b>{{ Html::label('Adaptation au traitement personnel :') }}</b>
        {{ Html::textarea('adaptation_traitement', null, ['class' => 'form-control splitLines', 'rows' => 3]) }}

        <b>{{ Html::label('Autre :') }}</b>
        {{ Html::textarea('autre1', null, ['class' => 'form-control splitLines', 'rows' => 3]) }}
    </td>
</tr>
<tr>
    <td><b>Technique d'anesthésie envisagée :</b> <span class="text-danger">*</span></td>
    <td class="form-group small">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="technique_anesthesie[]" value="Anesthésie générale" {{old( 'technique_anesthesie', $consultation_anesthesiste->technique_anesthesie)=='Anesthésie générale,' ? 'checked' : '' }}> Anesthésie générale
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="technique_anesthesie[]" value="Sédation" {{old( 'technique_anesthesie', $consultation_anesthesiste->technique_anesthesie)=='Sédation,' ? 'checked' : '' }}> Sédation
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="technique_anesthesie[]" value="Rachidienne" {{old( 'technique_anesthesie', $consultation_anesthesiste->technique_anesthesie)=='Rachidienne,' ? 'checked' : '' }}> Rachidienne
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="technique_anesthesie[]" value="Péridurale" {{old( 'technique_anesthesie', $consultation_anesthesiste->technique_anesthesie)=='Péridurale,' ? 'checked' : '' }}> Péridurale
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="technique_anesthesie[]" value="ALR" {{old( 'technique_anesthesie', $consultation_anesthesiste->technique_anesthesie)=='ALR,' ? 'checked' : '' }}> ALR
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="technique_anesthesie[]" value="Locale" {{old( 'technique_anesthesie', $consultation_anesthesiste->technique_anesthesie)=='Locale,' ? 'checked' : '' }}> Locale
        </div>
        <label for="autre2">Autres :</label>
        <input type="text" class="form-control" value="{{ old('technique_anesthesie') }}" name="technique_anesthesie[]">
    </td>
</tr>
<tr>
    <td><b>Antibioprophylaxie :</b> </td>
    <td>{{ Html::text('antibiotique', null, ['class' => 'form-control']) }}</td>
</tr>
<tr>
    <td><b>Synthèse pré-opératoire :</b> <span class="text-danger">*</span></td>
    <td>
        {{ Html::textarea('synthese_preop', null, ['class' => 'form-control splitLines', 'rows' => 3, 'required' => 'required']) }}
    </td>
</tr>
<tr>
    <td><b>Examens paracliniques :</b> </td>
    <td class="form-group small">
        @if(!empty($prescriptions->hematologie))
            <label for="autre">Gr / Rh :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
            <label for="autre">NFS :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
        @endif
        @if(!empty($prescriptions->hemostase))
            <label for="autre">TCK :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
            <label for="autre">TP / INR :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
        @endif
        @if(!empty($prescriptions->biochimie))
            <label for="autre">Créatinemie :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
            <label for="autre">Ionograme :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
            <label for="autre">Urée :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
            <label for="autre">Glycémie :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
        @endif
        @if(!empty($prescriptions->urines))
            <label for="autre">ECBU :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
        @endif
        @if(!empty($prescriptions->serologie))
            <label for="autre">VIH :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
        @endif
        @if(!empty($prescriptions->examen))
            <label for="autre">E.C.G :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
            <label for="autre">Echographie cardiaque :</label>
            <input type="text" class="form-control" value="{{ old('examen_paraclinique') }}" name="examen_paraclinique[]">
        @endif

        <label for="autre">Autres :</label>
        {{ Html::text('examen_paraclinique[]', null, ['class' => 'form-control']) }}
    </td>
</tr>
<tr>
    <input name="patient_id" value="{{ $patient->id }}" type="hidden">
</tr>
<tr>
    <td>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </td>
    <td></td>
</tr>
{{ Html::close() }}
