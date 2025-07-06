<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog @if($size) modal-{{ $size }} @endif">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                {{ $slot }} {{-- Ini adalah slot default untuk konten modal --}}
            </div>
            {{-- Jika Anda ingin footer modal yang bisa dikustomisasi, Anda bisa menambahkan slot bernama:

            --}}

            {{-- ONLY RENDER THE COMPONENT'S MODAL-FOOTER IF THE $footer SLOT IS PROVIDED --}}
            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endisset
            {{-- Using @isset($footer) is better than @if($footer ?? '') because it checks if the variable exists --}}
        </div>
    </div>
</div>