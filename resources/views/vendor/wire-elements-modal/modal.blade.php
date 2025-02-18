<div>
    @isset($jsPath)
        <script>{!! file_get_contents($jsPath) !!}</script>
    @endisset
    @isset($cssPath)
        <style>{!! file_get_contents($cssPath) !!}</style>
    @endisset

    <div
            x-data="LivewireUIModal()"
            x-on:close.stop="setShowPropertyTo(false)"
            x-on:keydown.escape.window="closeModalOnEscape()"
            x-show="show"
            class="newmodal"
            style="display: none;"
    >
        <div class="newmodal-item">
            <div
                    x-show="show"
                    x-on:click="closeModalOnClickAway()"
                    x-transition:enter="transition-enter"
                    x-transition:enter-start="transition-enter-start"
                    x-transition:enter-end="transition-enter-end"
                    x-transition:leave="transition-leave"
                    x-transition:leave-start="transition-leave-start"
                    x-transition:leave-end="transition-leave-end"
                    class="newmodal-overlay-transition"
            >
                <div class="newmodal-overlay"></div>
            </div>

            {{-- <span class="newmodal-close" aria-hidden="true">&#8203;</span> --}}

            <div
                    x-show="show && showActiveComponent"
                    x-transition:enter="transition-enter"
                    x-transition:enter-start="transition-enter-start-2"
                    x-transition:enter-end="transition-enter-end-2"
                    x-transition:leave="transition-leave"
                    x-transition:leave-start=".transition-leave-start-2"
                    x-transition:leave-end="transition-leave-end-2"
                    x-bind:class="modalWidth"
                    class="newmodal-content"
                    id="modal-container"
                    x-trap.noscroll.inert="show && showActiveComponent"
                    aria-modal="true"
            >
                @forelse($components as $id => $component)
                    <div x-show.immediate="activeComponent == '{{ $id }}'" x-ref="{{ $id }}" wire:key="{{ $id }}">
                        @livewire($component['name'], $component['arguments'], key($id))
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
</div>
