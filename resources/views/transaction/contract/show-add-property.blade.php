@extends('layouts.app')

@section('title', 'Add Unit - Property')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Add Unit - Property
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
                url: "{{ route('transaction.contracts.store-property-units', $contract->id) }}",
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

                    <div class="col-md-2">
                        <label class="form-label">Lokasi</label>
                        <textarea name="units[${i}][location]" class="form-control" rows="3" required>${unit.location || ''}</textarea>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Jenis Resiko</label>
                        <input type="text" name="units[${i}][risk_type]" class="form-control" value="${unit.risk_type || ''}" required>
                    </div>

                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input"
                                name="units[${i}][reinstallment_value_clause]"
                                value="1" id="reinstallment_value_clause_${i}"
                                ${unit.reinstallment_value_clause ? 'checked' : ''}>
                            <label class="form-check-label" for="reinstallment_value_clause_${i}">
                                Reinstatement Value Clause
                            </label>
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input"
                                name="units[${i}][nominated_loss_adjuster]"
                                value="1" id="nominated_loss_adjuster_${i}"
                                ${unit.nominated_loss_adjuster ? 'checked' : ''}>
                            <label class="form-check-label" for="nominated_loss_adjuster_${i}">
                                Nominated Loss Adjuster
                            </label>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Discount</label>
                        <input type="text" name="units[${i}][discount]" class="form-control" value="${unit.discount || ''}" required>
                    </div>
                </div>
            </div>
                        

            `);
        }
    }
</script>
@endpush