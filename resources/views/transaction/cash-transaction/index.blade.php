<x-layouts.app>
    <div class="container">
        @if(flash()->message)
            <div class="row">
                <div class="col">
                    <div class="alert alert-{{ flash()->class ?? "success" }}" role="alert">
                        {{ flash()->message }}
                    </div>
                </div>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <span>List Cash In / Cash Out</span>
                    <a href="{{ route("transaction.cash-transaction.create") }}" class="btn btn-outline-primary">Create</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-auto">
                        <input type="text" class="form-control" id="search" autocomplete="off">
                    </div>
                    <div class="col-auto">
                        <button type="button" id="btnSearch" class="btn btn-primary mb-3">Search</button>
                    </div>
                </div>

                <table class="table table-sm table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Client</th>
                            <td width="15%">Date</td>
                            <td width="10%">Type</td>
                            <th width="15%">Amount</th>
                            <th width="10%">Option</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function fetchData(page = 1, pageSize = 10, search = '') {
            $.ajax({
                url: '{{ route("api.transaction.cash-transaction.index") }}',
                data: {
                    page: page,
                    pageSize: pageSize,
                    search: search
                },
                beforeSend: function() {
                    $('#tbody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
                },
                success: function(response) {
                    let rows = '';
                    response.data.forEach(function(item) {
                        rows += '<tr>';
                        rows += '<td>' + item.number + '</td>';
                        rows += '<td>' + item.client + '</td>';
                        rows += '<td>' + item.date + '</td>';
                        rows += '<td>';
                        if (item.type == "Cash In") {
                            rows += '<span class="badge bg-success fw-normal">Cash In</span>';
                        } else {
                            rows += '<span class="badge bg-danger fw-normal">Cash Out</span>';
                        }
                        rows += '</td>';
                        rows += '<td class="text-end">' + item.amount + '</td>';
                        rows += '<td class="text-center">';
                        rows += '<a href="' + item.detail_url + '" class="btn btn-outline-primary btn-sm">Detail</a>';
                        rows += '</td>';
                        rows += '</tr>';
                    });

                    $('#tbody').html(rows);

                    generatePagination(response);
                }
            });
        }

        function generatePagination(response) {
            let paginationLinks = '';

            const currentPage = response.current_page;
            const lastPage = response.last_page;

            if (response.total > 0) {
                paginationLinks += '<nav class="d-flex justify-items-center justify-content-between">';
                paginationLinks += '<div class="d-flex justify-content-between flex-fill d-sm-none">';
                paginationLinks += '<ul class="pagination">';
                
                if (response.current_page === 1) {
                    paginationLinks += '<li class="page-item disabled" aria-disabled="true">';
                    paginationLinks += '<span class="page-link">Previous</span>';
                    paginationLinks += '</li>';
                } else {
                    paginationLinks += '<li class="page-item">';
                    paginationLinks += `<a class="page-link" href="javascript:void()" data-page="${currentPage - 1}" rel="prev">Previous</a>`;
                    paginationLinks += '</li>';
                }

                if (response.current_page === response.last_page) {
                    paginationLinks += '<li class="page-item disabled" aria-disabled="true">';
                    paginationLinks += '<span class="page-link text-sm">Next</span>';
                    paginationLinks += '</li>';
                } else {
                    paginationLinks += '<li class="page-item">';
                    paginationLinks += `<a class="page-link text-sm" href="javascript:void()" data-page="${currentPage + 1}" rel="next">Next</a>`;
                    paginationLinks += '</li>';
                }
                
                paginationLinks += '</ul>';
                paginationLinks += '</div>';

                // desktop
                paginationLinks += '<div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between gap-2">';

                paginationLinks += '<div>';
                paginationLinks += '<p class="small text-muted mb-0">Showing<span class="fw-semibold">' + response.from + '</span> to <span class="fw-semibold">' + response.to + '</span> of <span class="fw-semibold">' + response.total + '</span> results';
                paginationLinks += '</p>';
                paginationLinks += '</div>';

                // pagination
                paginationLinks += '<div>';
                paginationLinks += '<ul class="pagination">';
                if (response.current_page === 1) {
                    paginationLinks += '<li class="page-item disabled" aria-disabled="true"><span class="page-link">Previous</span></li>';
                } else {
                    paginationLinks += '<li class="page-item"><a class="page-link" href="#" data-page="' + (response.current_page - 1) + '">Previous</a></li>';
                }

                // elements
                if (currentPage > 2) {
                    paginationLinks += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                    if (currentPage > 3) {
                        paginationLinks += `<li class="page-item"><span class="page-link">...</span></li>`;
                    }
                }

                for (let i = Math.max(1, currentPage - 1); i <= Math.min(lastPage, currentPage + 1); i++) {
                    paginationLinks += `<li class="page-item ${currentPage === i ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }

                if (currentPage < lastPage - 1) {
                    if (currentPage < lastPage - 2) {
                        paginationLinks += `<li class="page-item"><span class="page-link">...</span></li>`;
                    }
                    paginationLinks += `<li class="page-item"><a class="page-link" href="#" data-page="${lastPage}">${lastPage}</a></li>`;
                }
                // end elements

                if (response.current_page === response.last_page) {
                    paginationLinks += '<li class="page-item disabled" aria-disabled="true"><span class="page-link">Next</span></li>';
                } else {
                    paginationLinks += '<li class="page-item"><a class="page-link" href="#" data-page="' + (response.current_page + 1) + '">Next</a></li>';
                }

                paginationLinks += '</ul>';
                paginationLinks += '</div>';
                // end pagination

                paginationLinks += '</div>';
                // end desktop

                paginationLinks += '</nav>';
            }

            $('#pagination').html(paginationLinks);

            $('.page-link').click(function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                fetchData(page);
            });
        }

        $(document).ready(function() {
            fetchData();

            $("#btnSearch").click(function() {
                const search = $("#search").val();
                fetchData(1, 10, search);
            });
        });
    </script>
    @endpush
</x-layouts.app>