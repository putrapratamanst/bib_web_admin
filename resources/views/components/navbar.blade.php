<nav class="navbar fixed-top navbar-expand-lg shadow-sm bg-body-tertiary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('logo.png') }}" alt="" width="120" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">Home</x-nav-link>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Master Data
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('master.chart-of-accounts.index') }}">Chart of Account</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('master.contact-groups.index') }}">Contact Group</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('master.contacts.index') }}">Contact</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Transaction
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.contracts.index') }}">Contract</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.debit-notes.index') }}">Debit Note</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.credit-notes.index') }}">Credit Note</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.journal-entries.index') }}">Journal Entry</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.cash-banks.index') }}">Cash &amp; Bank</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.payment-allocations.index') }}">Payment Allocation</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Report
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('report.console.index') }}">Console Report</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('report.balance-sheet.index') }}">Balance Sheet</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route("report.balance.index") }}">Balance</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route("report.profitandloss.index") }}">Profit & Loss</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route("report.cashflow.index") }}">Cash Flow</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route("report.piutang.index") }}">Report Piutang <span class="badge bg-danger">new</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>