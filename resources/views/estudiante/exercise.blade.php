<x-app-layout>
  @section('main')
    <!-- Join to exercise -->
    <x-estudiante.joinToExercise />
    <x-layouts.dashboard>
      <div class="app-wrapper">
        <div class="app-content pt-3 p-md-3 p-lg-4">
          <div class="container-xl">
            <div class="row g-3 mb-4 align-items-center justify-content-between">
              <div class="d-flex justify-content-between">
                <h1 class="app-page-title mb-0">Ejercicio: {{ $exercise->titulo }}
                </h1>
                @if (\Auth::user()->role == 3)
                  <!-- Mostrar botón enviar para estudiantes -->
                  @if ($exercise->asignaciones->sent)
                    <x-primary-button :custom="true"
                      class="btn border border-success disabled"><i
                        class="fa-regular fa-circle-check"></i>
                      Enviado</x-primary-button>
                  @else
                    @if ($asientosContables->count() > 0)
                      <x-primary-button data-bs-toggle="modal"
                        data-bs-target="#sendExercise">Enviar</x-primary-button>

                      <x-modal :name="__('sendExercise')" :size="__('md')"
                        :show="false" :title="__('Enviar ejercicio al docente')" :form="true"
                        :form_method="__('get')" :form_action="route('estudiante.send_exercise', [
                            'id' => $exercise->id,
                        ])">
                        <div class="row">
                          <div class="col-12 mb-3">
                            <p>Vas a enviar este ejercicio al docente.</p>
                            <p>¿Estás seguro de que quieres enviar el ejercicio?
                            </p>
                          </div>
                        </div>
                        <x-slot:footer>
                          <x-primary-button data-bs-dismiss="modal"
                            class="btn btn-secondary">Cancelar</x-primary-button>
                          <x-primary-button type="submit" form="sendExerciseForm"
                            class="btn btn-success">Confirmar
                            Envío</x-primary-button>
                        </x-slot:footer>
                      </x-modal>
                    @else
                      <x-primary-button class="disabled" data-bs-toggle="modal"
                        data-bs-target="#sendExercise">Enviar</x-primary-button>
                    @endif
                  @endif
                @endif
              </div>
            </div>

            <div class="row my-3 justify-content-between">
              <!-- Detalles de ejercicio -->
              <details class="card col-lg-9" open>
                <summary class="card-header">Detalles</summary>
                <div class="card-body">
                  <p class="card-text">
                    {{ $exercise->desc }}
                  </p>
                </div>
              </details>

              <div class="card col-lg-3">
                <div class="card-header">Resultados</div>
                <div class="card-body">
                  <div class="d-flex flex-column gap-3">

                    <a href="{{ route('estudiante.libro_diario', ['id' => $exercise->id]) }}"
                      class="btn btn-warning d-flex align-items-center justify-content-around w-100">
                      <i class="fa-solid fa-file-invoice-dollar fs-4 me-2"></i>
                      <span>Libro Diario</span>
                    </a>
                    <x-primary-button data-bs-toggle="modal"
                      data-bs-target="#asientoModal" custom="true"
                      class="btn-info d-flex align-items-center justify-content-around w-100">
                      <i class="fa-solid fa-book fs-4 me-2"></i>
                      <span>Libro Mayor</span>
                    </x-primary-button>
                  </div>
                </div>
              </div>
            </div>
                    <div class="row mt-4">
                        <div class="card col-lg-12">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6>Asientos Contables</h6>
                                    @if ( \Auth::user()->role == 3 )
                                        <x-primary-button data-bs-toggle="modal" data-bs-target="#asientoModal">Nuevo Asiento</x-primary-button>
                                        <x-estudiante.new-asiento-contable-modal :exercise="$exercise" />
                                        <x-estudiante.update-asiento-contable-modal :exercise="$exercise"/>
                                        <x-estudiante.delete-asiento-contable-modal :exercise="$exercise"/>
                                        @push('scripts')
                                            <script>
                                                function formatInputDate(date) {
                                                    return moment(date).format('YYYY-MM-DD');
                                                }

                                                function accountIconFormat(signo) {
                                                    var icon = '';
                                                    switch (signo) {
                                                        case 'P':
                                                            icon = '<i class="fas fa-solid fa-circle-plus text-success"></i>';
                                                            break;
                                                        case 'N':
                                                            icon = '<i class="fas fa-solid fa-circle-minus text-danger"></i>';
                                                            break;
                                                        case 'D':
                                                            icon = '<i class="fas fa-solid fa-circle-dot text-warning"></i>';
                                                            break;
                                                        default:
                                                            icon = '';
                                                            break;
                                                    }
                                                    return icon;
                                                }

                                                function initializeSelect2ForRow(index, update=false) {
                                                    const $element = `#${update ? 'update-' : ''}cuentas_${index}_account_id`;
                                                    const $dropdownParent = `#${update ? 'updateAsientoModal' : 'asientoModal'}`;

                                                    $($element).select2({
                                                        theme: 'bootstrap-5',
                                                        dropdownParent: $($dropdownParent),
                                                        minimumInputLength: 1,
                                                        ajax: {
                                                            url: "{{ route('plancuentas.search') }}",
                                                            dataType: 'json',
                                                            delay: 250,
                                                            data: function (params) {
                                                                return {
                                                                    q: params.term, // término de búsqueda
                                                                    page: params.page || 1
                                                                };
                                                            },
                                                            processResults: function (data, params) {
                                                                params.page = params.page || 1;

                                                                return {
                                                                    results: data.results.map(function (item) {
                                                                        return {
                                                                            id: item.id,
                                                                            text: item.cuenta + ' - ' + item.text,
                                                                            signo: item.signo,
                                                                            tipoCuenta: item.tipoCuenta,
                                                                            disabled: item.tipoCuenta === 'T'
                                                                        };
                                                                    }),
                                                                    pagination: {
                                                                        more: (params.page * 30) < data.total_count
                                                                    }
                                                                };
                                                            },
                                                            cache: true
                                                        },
                                                        escapeMarkup: function (markup) { return markup; },
                                                        templateResult: function (data) {
                                                            if (data.loading) {
                                                                return data.text;
                                                            }

                                                            var icon = accountIconFormat(data.signo);

                                                            // Markup para la lista de resultados
                                                            var markup = "<div class='select2-result-repository clearfix'>";
                                                            if (data.tipoCuenta === 'T') {
                                                                markup += "<div class='select2-result-repository__title font-weight-bold'>" + icon + data.text + "</div>";
                                                            } else {
                                                                markup += "<div class='select2-result-repository__title'>" + icon + data.text + "</div>";
                                                            }
                                                            markup += "</div>";

                                                            return markup;
                                                        },
                                                        templateSelection: function (data) {
                                                            if (!data.id) return data.text;

                                                            var icon = accountIconFormat(data.signo);

                                                            return icon + data.text;
                                                        },
                                                        matcher: function (params, data) {
                                                            // No permitir selección de cuentas de tipo 'T'
                                                            if (data.tipoCuenta === 'T') {
                                                                return null;
                                                            }

                                                            // Lógica de búsqueda por defecto
                                                            if ($.trim(params.term) === '') {
                                                                return data;
                                                            }

                                                            if (typeof data.text === 'undefined') {
                                                                return null;
                                                            }

                                                            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                                                                return data;
                                                            }

                                                            return null;
                                                        }
                                                    });
                                                }
                                            </script>
                                        @endpush
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="entries">
                                    <!-- Los asientos contables se agregarán aquí dinámicamente -->
                                    @foreach ($asientosContables as $asiento)
                                        <x-estudiante.asiento-contable :asiento="$asiento" />
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </x-layouts.dashboard>

  @endsection
</x-app-layout>