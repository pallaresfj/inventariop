<div class="flex justify-center">
    @if (filled($imageUrl))
        <img
            src="{{ $imageUrl }}"
            alt="{{ $alt ?? 'Imagen' }}"
            class="max-h-[70vh] w-auto rounded-lg object-contain"
        >
    @endif
</div>
