@props(['type' => 'success', 'message' => '', 'duration' => 5000])

@php
$colors = match($type) {
    'success' => ['bg' => 'bg-white', 'border' => 'border-green-500', 'text' => 'text-gray-800', 'icon' => 'fa-check-circle', 'iconColor' => 'text-green-500', 'accent' => 'bg-green-500'],
    'error' => ['bg' => 'bg-white', 'border' => 'border-red-500', 'text' => 'text-gray-800', 'icon' => 'fa-exclamation-circle', 'iconColor' => 'text-red-500', 'accent' => 'bg-red-500'],
    'warning' => ['bg' => 'bg-white', 'border' => 'border-yellow-500', 'text' => 'text-gray-800', 'icon' => 'fa-exclamation-triangle', 'iconColor' => 'text-yellow-500', 'accent' => 'bg-yellow-500'],
    'info' => ['bg' => 'bg-white', 'border' => 'border-blue-500', 'text' => 'text-gray-800', 'icon' => 'fa-info-circle', 'iconColor' => 'text-blue-500', 'accent' => 'bg-blue-500'],
    default => ['bg' => 'bg-white', 'border' => 'border-gray-500', 'text' => 'text-gray-800', 'icon' => 'fa-bell', 'iconColor' => 'text-gray-500', 'accent' => 'bg-gray-500'],
};
@endphp

<div
    x-data="{ show: true, progress: 100 }"
    x-init="
        let interval = setInterval(() => {
            progress -= 100 / ({{ $duration }} / 50);
            if (progress <= 0) {
                clearInterval(interval);
                show = false;
            }
        }, 50);
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-10"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="{{ $colors['bg'] }} border-l-4 {{ $colors['border'] }} {{ $colors['text'] }} p-4 rounded-2xl mb-3 flex items-center gap-4 shadow-xl pointer-events-auto relative overflow-hidden group min-w-[300px]"
    role="alert"
>
    <div class="absolute bottom-0 left-0 h-1 {{ $colors['accent'] }} transition-all duration-75" :style="'width: ' + progress + '%'"></div>
    
    <div class="w-10 h-10 {{ $colors['accent'] }} bg-opacity-10 rounded-xl flex items-center justify-center shrink-0">
        <i class="fas {{ $colors['icon'] }} {{ $colors['iconColor'] }} text-xl"></i>
    </div>
    
    <div class="flex-1">
        <p class="text-xs font-black uppercase text-gray-400 tracking-widest mb-0.5">{{ $type }}</p>
        <span class="font-bold text-sm leading-tight">{{ $message }}</span>
    </div>

    <button @@click="show = false" class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition flex items-center justify-center">
        <i class="fas fa-times text-xs"></i>
    </button>
</div>
