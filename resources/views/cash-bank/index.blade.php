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
    </div>
</x-layouts.app>