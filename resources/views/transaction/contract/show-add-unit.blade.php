@extends('layouts.app')

@section('title', 'Add Unit - Automobile')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add Unit - Automobile
        </div>
        <form id="formAddUnits" method="POST">
            @csrf
            <input type="hidden" name="contract_id" value="{{ $contract->id }}">

            <div class="card-body">
                <div class="mb-3">
                    <label for="covered_item" class="form-label">Jumlah Unit</label>
                    <input type="number" id="covered_item" name="covered_item" class="form-control" value="{{ $contract->covered_item }}" min="1" readonly>
                </div>

                <div id="unit-forms-container">
                    {{-- Unit form fields akan dimasukkan via JS --}}
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save Units</button>
                <a href="{{ route('transaction.contracts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const units = @json($units);
    $(document).ready(function() {
        const count = parseInt($('#covered_item').val() || 0);
        const formCount = units.length > 0 ? units.length : count;

        generateUnitForms(formCount, units);

        $('#formAddUnits').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "{{ route('transaction.contracts.store-automobile-units', $contract->id) }}",
                method: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success',
                    }).then(() => {
                        window.location.href = "{{ route('transaction.contracts.index') }}";
                    });
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    Swal.fire({
                        title: 'Error',
                        text: res.message || 'Validation failed',
                        icon: 'error',
                    });
                }
            });
        });
    });

    function generateUnitForms(count, data = []) {
        const container = $('#unit-forms-container');
        container.empty();

        for (let i = 0; i < count; i++) {
            const unit = data[i] || {};

            container.append(`
            <div class="card mb-3">
                <div class="card-header">Unit #${i + 1}</div>
                <div class="card-body row">

                    <!-- No Polisi -->
                    <div class="col-md-2">
                        <label class="form-label">No Polisi <sup class="text-danger">*</sup></label>
                        <input type="text" name="units[${i}][no_polisi]" class="form-control" 
                               value="${unit.no_polisi || ''}" required>
                    </div>

                    <!-- Merk -->
                    <div class="col-md-2">
                        <label class="form-label">Merk <sup class="text-danger">*</sup></label>
                        <input type="text" name="units[${i}][merk]" class="form-control"
                               value="${unit.merk || ''}" required>
                    </div>

                    <!-- Tahun -->
                    <div class="col-md-1">
                        <label class="form-label">Tahun <sup class="text-danger">*</sup></label>
                        <input type="number" min="1900" max="2100" name="units[${i}][tahun]" 
                               class="form-control" value="${unit.tahun || ''}" required>
                    </div>

                    <!-- No Rangka -->
                    <div class="col-md-2">
                        <label class="form-label">No Rangka <sup class="text-danger">*</sup></label>
                        <input type="text" name="units[${i}][no_rangka]" class="form-control"
                               value="${unit.no_rangka || ''}" required>
                    </div>

                    <!-- No Mesin -->
                    <div class="col-md-2">
                        <label class="form-label">No Mesin <sup class="text-danger">*</sup></label>
                        <input type="text" name="units[${i}][no_mesin]" class="form-control"
                               value="${unit.no_mesin || ''}" required>
                    </div>

                    <!-- Penggunaan -->
                    <div class="col-md-3">
                        <label class="form-label">Penggunaan <sup class="text-danger">*</sup></label>
                        <input type="text" name="units[${i}][penggunaan]" class="form-control"
                               value="${unit.penggunaan || ''}" required>
                    </div>

                    <!-- Harga / Pertanggungan -->
                    <div class="col-md-4 mt-4">
                        <label class="form-label">Harga / Pertanggungan <sup class="text-danger">*</sup></label>
                        <div class="row">
                            <div class="col-5">
                                <label class="form-label small">Valuta</label>
                                <select name="units[${i}][valuta]" class="form-select" required>
                                    ${['IDR','USD','EUR','JPY','SGD','AUD','GBP'].map(v => `
                                        <option value="${v}" ${unit.valuta === v ? 'selected' : ''}>${v}</option>
                                    `).join('')}
                                </select>
                            </div>
                            <div class="col-7">
                                <label class="form-label small">Total</label>
                                <input type="text" name="units[${i}][total]" class="form-control"
                                       value="${unit.total || ''}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Cover / Discount -->
                    <div class="col-md-4 mt-4">
                        <label class="form-label">Cover / Discount</label>
                        <div class="row">
                            <div class="col-5">
                                <label class="form-label small">Cover</label>
                                <select name="units[${i}][cover]" class="form-select" required>
                                    ${['AMB','MCL','HEQ'].map(c => `
                                        <option value="${c}" ${unit.cover === c ? 'selected' : ''}>${c}</option>
                                    `).join('')}
                                </select>
                            </div>
                            <div class="col-7">
                                <label class="form-label small">Discount</label>
                                <input type="text" name="units[${i}][discount]" class="form-control"
                                       value="${unit.discount || ''}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Rate / Brokerage -->
                    <div class="col-md-4 mt-4">
                        <label class="form-label">Rate / Brokerage</label>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label small">Rate</label>
                                <input type="text" name="units[${i}][rate]" class="form-control"
                                       value="${unit.rate || ''}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Brokerage</label>
                                <input type="text" name="units[${i}][brokerage]" class="form-control"
                                       value="${unit.brokerage || ''}" required>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        `);
        }
    }
</script>
@endpush