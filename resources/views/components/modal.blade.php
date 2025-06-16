@props([
    'id' => 'modal',
    'size' => 'md', // sm, md, lg, xl, full
    'closable' => true,
    'backdrop' => true,
    'title' => '',
    'showHeader' => true,
    'showFooter' => false
])

@php
    $sizeClasses = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'full' => 'max-w-full mx-4'
    ];
@endphp

<!-- Modal Overlay -->
<div id="{{ $id }}" 
     class="fixed inset-0 z-50 hidden overflow-y-auto" 
     aria-labelledby="{{ $id }}-title" 
     role="dialog" 
     aria-modal="true">
     
    <!-- Background overlay -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay backdrop -->
        @if($backdrop)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"
                 onclick="closeModal('{{ $id }}')"></div>
        @endif
        
        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $sizeClasses[$size] }} sm:w-full">
            
            @if($showHeader)
                <!-- Modal Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900" id="{{ $id }}-title">
                            {{ $title }}
                        </h3>
                        
                        @if($closable)
                            <button type="button" 
                                    class="text-gray-400 hover:text-gray-600 transition duration-300"
                                    onclick="closeModal('{{ $id }}')">
                                <span class="sr-only">Fermer</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Modal Body -->
            <div class="bg-white px-6 py-4">
                {{ $slot }}
            </div>
            
            @if($showFooter)
                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $footer ?? '' }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Animation
            const modalPanel = modal.querySelector('.inline-block');
            modalPanel.style.opacity = '0';
            modalPanel.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                modalPanel.style.transition = 'opacity 300ms, transform 300ms';
                modalPanel.style.opacity = '1';
                modalPanel.style.transform = 'scale(1)';
            }, 10);
        }
    }
    
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const modalPanel = modal.querySelector('.inline-block');
            modalPanel.style.opacity = '0';
            modalPanel.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const visibleModals = document.querySelectorAll('[id*="modal"]:not(.hidden)');
            visibleModals.forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
</script>
@endpush 