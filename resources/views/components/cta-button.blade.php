@props([
    'href' => '#',
    'type' => 'primary', // primary, secondary, accent, outline
    'size' => 'md', // sm, md, lg, xl
    'icon' => null,
    'iconPosition' => 'left', // left, right
    'target' => '_self',
    'disabled' => false,
    'loading' => false,
    'fullWidth' => false
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    $sizeClasses = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-base',
        'lg' => 'px-8 py-4 text-lg',
        'xl' => 'px-10 py-5 text-xl'
    ];
    
    $typeClasses = [
        'primary' => 'bg-astuce-500 hover:bg-astuce-600 text-white shadow-astuce hover:shadow-astuce-lg focus:ring-astuce-500',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white shadow-lg hover:shadow-xl focus:ring-gray-500',
        'accent' => 'bg-accent-500 hover:bg-accent-600 text-white shadow-lg hover:shadow-xl focus:ring-accent-500',
        'outline' => 'border-2 border-astuce-500 text-astuce-500 hover:bg-astuce-500 hover:text-white focus:ring-astuce-500',
        'success' => 'bg-success-500 hover:bg-success-600 text-white shadow-lg hover:shadow-xl focus:ring-success-500',
        'gradient' => 'gradient-astuce text-white shadow-astuce hover:shadow-astuce-lg focus:ring-astuce-500',
    ];
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size] . ' ' . $typeClasses[$type];
    
    if ($fullWidth) {
        $classes .= ' w-full';
    }
    
    if ($disabled) {
        $classes .= ' opacity-50 cursor-not-allowed';
    }
@endphp

<a href="{{ $disabled ? '#' : $href }}" 
   target="{{ $target }}"
   class="{{ $classes }}"
   @if($disabled) onclick="return false;" @endif>
   
    @if($loading)
        <!-- Loading Spinner -->
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Chargement...
    @else
        @if($icon && $iconPosition === 'left')
            {!! $icon !!}
        @endif
        
        <span @if($icon) class="{{ $iconPosition === 'left' ? 'ml-2' : 'mr-2' }}" @endif>
            {{ $slot }}
        </span>
        
        @if($icon && $iconPosition === 'right')
            {!! $icon !!}
        @endif
    @endif
</a> 