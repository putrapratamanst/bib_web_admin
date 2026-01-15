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
                <li class="nav-item">
                    <a class="nav-link{{ request()->routeIs('home') ? ' active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>

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
                            <a class="dropdown-item" href="{{ route('transaction.contracts.index') }}">Placing</a>
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
                            <a class="dropdown-item" href="{{ route('transaction.advances.index') }}">
                                <i class="bi bi-cash-coin me-1"></i>
                                Advance Payment
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.refunds.index') }}">
                                <i class="bi bi-arrow-return-left me-1"></i>
                                Refund
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.payment-allocations.index') }}">Payment Allocation</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transaction.cashouts.index') }}">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                Hutang Asuransi <span class="badge bg-success">new</span>
                            </a>
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
                            <a class="dropdown-item" href="{{ route("report.piutang.index") }}">A/R Aging</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('report.account-statement.index') }}">
                                <i class="fas fa-book me-1"></i>
                                Account Statement <span class="badge bg-info">new</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('report.debit-notes.index') }}">
                                <i class="fas fa-file-invoice me-1"></i>
                                Debit Note Report <span class="badge bg-primary">new</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('report.cashout.index') }}">
                                <i class="fas fa-money-bill-wave me-1"></i>
                                Laporan Hutang Asuransi <span class="badge bg-success">new</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                        <!-- @if(auth()->user()->role)
                            <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'primary' : 'success' }} ms-1">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        @endif -->
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text">
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>