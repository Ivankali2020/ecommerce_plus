<div class="space-y-4">
    @foreach($mediaItems as $mediaItem)
        <div class="border rounded-lg overflow-hidden ">
            <div class="p-4  border-b">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-medium text-gray-900">
                        {{ $mediaItem->name }}
                    </h3>
                    <span class="text-xs text-gray-500">
                        {{ $mediaItem->human_readable_size }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ __('subscription.uploaded') }}: {{ $mediaItem->created_at->format('M d, Y H:i') }}
                </p>
            </div>
            
            <div class="p-4 text-center">
                <img 
                    src="{{ $mediaItem->getUrl() }}" 
                    alt="Payment Slip"
                    class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg cursor-pointer"
                    onclick="openImageModal('{{ $mediaItem->getUrl() }}', '{{ $mediaItem->name }}')"
                >
            </div>
            
            <div class="p-4  border-t">
                <a 
                    href="{{ $mediaItem->getUrl() }}" 
                    target="_blank"
                    class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    {{ __('subscription.view_full_size') }}
                </a>
            </div>
        </div>
    @endforeach
</div>

<!-- Modal for full-size image view -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center" onclick="closeImageModal()">
    <div class="max-w-4xl max-h-full p-4">
        <div class="rounded-lg overflow-hidden">
            <div class="p-4  border-b flex justify-between items-center">
                <h3 id="modalImageName" class="text-lg font-medium text-gray-900"></h3>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 text-center">
                <img id="modalImage" src="" alt="Payment Slip" class="max-w-full max-h-[70vh] mx-auto">
            </div>
        </div>
    </div>
</div>

<script>
function openImageModal(imageUrl, imageName) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalImageName').textContent = imageName;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
</script>
