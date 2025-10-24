<div class="modal fade" id="ordonanceAll" tabindex="-1" role="dialog" aria-labelledby="ordonanceAll" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-size:14px;" id="exampleModalLabel">LISTE DES ORDONANCES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span style="font-size:14px;" aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size:14px;">
                @if (count($patient->ordonances))

                    <h3>Ordonances médicales</h3>
                    <br>
                    <div class="table-responsive">
                        <table id="myTable" class="table table-bordred table-striped">
                            <thead>
                            <th>MEDICAMENT</th>
                            <th>QUANTITE</th>
                            <th>POSOLOGIE</th>
                            <th>DATE</th>
                            <th>IMPPRIMER</th>
                            </thead>
                            <tbody>

                            @foreach($patient->ordonances as $ordonance)

                                <tr>
                                    <td>{{ $ordonance->medicament }}</td>
                                    <td>{{ $ordonance->quantite }}</td>
                                    <td>{{ $ordonance->description }}</td>
                                    <td>{{ $ordonance->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        <a class="btn btn-success btn-sm" title="Imprimer l'ordonance" href="{{ route('ordonance.pdf', $ordonance->id) }}">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr></tr>
                            @endforeach


                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                        {{ $ordonances->links() }}

                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

