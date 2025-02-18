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
                            <a wire:navigate class="dropdown-item" href="{{ route('company.index') }}">Company</a>
                        </li>
                        {{-- <li>
                            <a wire:navigate class="dropdown-item" href="{{ route('bank.index') }}">Bank</a>
                        </li> --}}
                        <li>
                            <a wire:navigate class="dropdown-item" href="{{ route('coa.index') }}">Chart of Account</a>
                        </li>
                        <li>
                            <a wire:navigate class="dropdown-item" href="{{ route('contact.index') }}">Contact</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Transaction
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a wire:navigate class="dropdown-item" href="{{ route("transaction.cash-bank.index") }}">Cash &amp; Bank</a>
                        </li>

                        <li>
                            <a wire:navigate class="dropdown-item" href="{{ route("transaction.contract.index") }}">Contract</a>
                        </li>
                        <li>
                            <a wire:navigate class="dropdown-item" href="{{ route('transaction.billing.index') }}">Billing</a>
                        </li>
                        <li>
                            <a wire:navigate class="dropdown-item" href="{{ route('transaction.credit-note.index') }}">Credit Note</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a wire:navigate class="dropdown-item" href="{{ route("transaction.cash-transaction.index") }}">Cash In / Cash Out</a>
                        </li>
                        <li>
                            <a wire:navigate class="dropdown-item" href="{{ route('transaction.payment-allocation.index') }}">Payment Allocation</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>