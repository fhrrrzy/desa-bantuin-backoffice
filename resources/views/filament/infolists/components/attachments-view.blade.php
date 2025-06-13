@php
    $attachments = $getState();
@endphp

@if(!$attachments)
    <div class="text-gray-500 text-sm">
        <x-heroicon-o-paper-clip class="w-4 h-4 inline mr-1" />
        Tidak ada lampiran
    </div>
@else
    <div class="space-y-3">
        @foreach($attachments as $attachment)
            @php
                $fileName = basename($attachment);
                $fileUrl = asset('storage/' . $attachment);
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            @endphp
            
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                <div class="flex items-center space-x-3">
                    @if($isImage)
                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                            <img 
                                src="{{ $fileUrl }}" 
                                alt="{{ $fileName }}"
                                class="w-full h-full object-cover cursor-pointer hover:opacity-80 transition-opacity"
                                onclick="openLightbox('{{ $fileUrl }}', '{{ $fileName }}')"
                            />
                        </div>
                    @else
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-heroicon-o-document class="w-6 h-6 text-blue-600" />
                        </div>
                    @endif
                    
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $fileName }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ strtoupper($fileExtension) }} File
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    @if($isImage)
                        <button
                            type="button"
                            onclick="openLightbox('{{ $fileUrl }}', '{{ $fileName }}')"
                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <x-heroicon-o-eye class="w-3 h-3 mr-1" />
                            Lihat
                        </button>
                    @endif
                    
                    <a
                        href="{{ $fileUrl }}"
                        download="{{ $fileName }}"
                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                        <x-heroicon-o-arrow-down-tray class="w-3 h-3 mr-1" />
                        Download
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button
                onclick="closeLightbox()"
                class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2"
            >
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
            
            <div class="text-center">
                <img id="lightbox-image" src="" alt="" class="max-w-full max-h-[80vh] object-contain mx-auto" />
                <p id="lightbox-filename" class="text-white mt-4 text-lg font-medium"></p>
            </div>
            
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2">
                <a
                    id="lightbox-download"
                    href=""
                    download=""
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                    Download
                </a>
            </div>
        </div>
    </div>

    <script>
        function openLightbox(imageUrl, filename) {
            const lightbox = document.getElementById('lightbox');
            const lightboxImage = document.getElementById('lightbox-image');
            const lightboxFilename = document.getElementById('lightbox-filename');
            const lightboxDownload = document.getElementById('lightbox-download');
            
            lightboxImage.src = imageUrl;
            lightboxImage.alt = filename;
            lightboxFilename.textContent = filename;
            lightboxDownload.href = imageUrl;
            lightboxDownload.download = filename;
            
            lightbox.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Close lightbox when clicking outside the image
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
        
        // Close lightbox with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
    </script>
@endif 