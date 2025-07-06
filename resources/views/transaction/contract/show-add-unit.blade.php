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
    $(document).ready(function () {
        const count = parseInt($('#covered_item').val() || 0);
        generateUnitForms(count);

        $('#formAddUnits').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "{{ route('transaction.contracts.store-automobile-units', $contract->id) }}",
                method: "POST",
                data: formData,
                success: function (response) {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success',
                    }).then(() => {
                        window.location.href = "{{ route('transaction.contracts.index') }}";
                    });
                },
                error: function (xhr) {
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

    function generateUnitForms(count) {
        const container = $('#unit-forms-container');
        container.empty();

        for (let i = 0; i < count; i++) {
            container.append(`
                <div class="card mb-3">
                    <div class="card-header">Unit #${i + 1}</div>
                    <div class="card-body row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">No Polisi <sup class="text-danger">*</sup></label>
                                <input type="text" name="units[${i}][no_polisi]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Merk / Tahun <sup class="text-danger">*</sup></label>
                                <input type="text" name="units[${i}][merk_tahun]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">No Rangka / Mesin <sup class="text-danger">*</sup></label>
                                <input type="text" name="units[${i}][no_rangka_mesin]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Penggunaan <sup class="text-danger">*</sup></label>
                                <input type="text" name="units[${i}][penggunaan]" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }
</script>
@endpush
