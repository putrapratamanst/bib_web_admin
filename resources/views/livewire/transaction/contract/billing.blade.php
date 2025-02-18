<div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <span>Detail Billing</span>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-circle" data-bs-toggle="modal" data-bs-target="#createFormGenerateModal">
                            Generate Billing
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="createFormGenerateModal" tabindex="-1" aria-labelledby="createFormGenerateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFormGenerateModalLabel">
                        Generate Billing
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="generate" class="btn btn-sm btn-circle btn-primary">Generate</button>
                    <button type="button" class="btn btn-sm btn-circle btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>