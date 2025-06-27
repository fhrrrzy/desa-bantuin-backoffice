@php
    $attachments = $getState();
@endphp

@if(!$attachments)
    <div class="text-gray-500 dark:text-gray-400 text-sm">
        @svg('heroicon-o-paper-clip', 'w-4 h-4 inline mr-1')
        Tidak ada lampiran
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
        @foreach($attachments as $attachment)
            @php
                $fileName = basename($attachment);
                $fileUrl = asset('storage/' . $attachment);
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                $isPdf = $fileExtension === 'pdf';
            @endphp
            
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow h-64 sm:h-80 flex flex-col">
                @if($isImage)
                    <div class="h-32 sm:h-48 bg-gray-200 dark:bg-gray-700 relative group cursor-pointer flex-shrink-0" onclick="openLightbox('{{ $fileUrl }}', '{{ $fileName }}')">
                        <img 
                            src="{{ $fileUrl }}" 
                            alt="{{ $fileName }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                        />
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                @svg('heroicon-o-eye', 'w-6 h-6 sm:w-8 sm:h-8 text-white')
                            </div>
                        </div>
                    </div>
                @else
                    <div class="h-32 sm:h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        @if($isPdf)
                            <div class="text-center">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    @svg('heroicon-o-document', 'w-6 h-6 sm:w-8 sm:h-8 text-red-600 dark:text-red-400')
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">PDF</p>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    @svg('heroicon-o-document', 'w-6 h-6 sm:w-8 sm:h-8 text-blue-600 dark:text-blue-400')
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ strtoupper($fileExtension) }}</p>
                            </div>
                        @endif
                    </div>
                @endif
                
                <div class="p-2 sm:p-3 flex-1 flex flex-col">
                    <h4 class="text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 truncate mb-2 flex-shrink-0" title="{{ $fileName }}">
                        {{ $fileName }}
                    </h4>
                    
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center sm:space-x-2 mt-auto">
                        @if($isImage)
                            <button
                                type="button"
                                onclick="openLightbox('{{ $fileUrl }}', '{{ $fileName }}')"
                                class="inline-flex items-center justify-center px-2 py-1.5 sm:py-1 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-md hover:bg-blue-100 dark:hover:bg-blue-900/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                @svg('heroicon-o-eye', 'w-3 h-3 mr-1')
                                Lihat
                            </button>
                        @elseif($isPdf)
                            <button
                                type="button"
                                onclick="openPdfModal('{{ $fileUrl }}', '{{ $fileName }}')"
                                class="inline-flex items-center justify-center px-2 py-1.5 sm:py-1 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-md hover:bg-red-100 dark:hover:bg-red-900/40 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                            >
                                @svg('heroicon-o-eye', 'w-3 h-3 mr-1')
                                Lihat
                            </button>
                        @endif
                        
                        <a
                            href="{{ $fileUrl }}"
                            download="{{ $fileName }}"
                            class="inline-flex items-center justify-center px-2 py-1.5 sm:py-1 text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 rounded-md hover:bg-green-100 dark:hover:bg-green-900/40 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                        >
                            @svg('heroicon-o-arrow-down-tray', 'w-3 h-3 mr-1')
                            Download
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Lightbox Modal for Images -->
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-2 sm:p-4">
        <div class="relative w-full max-w-4xl max-h-full">
            <button
                onclick="closeLightbox()"
                class="absolute top-2 right-2 sm:top-4 sm:right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2 transition-colors"
            >
                @svg('heroicon-o-x-mark', 'w-5 h-5 sm:w-6 sm:h-6')
            </button>
            
            <div class="text-center">
                <img id="lightbox-image" src="" alt="" class="max-w-full max-h-[60vh] sm:max-h-[80vh] object-contain mx-auto" />
                <p id="lightbox-filename" class="text-white mt-2 sm:mt-4 text-sm sm:text-lg font-medium"></p>
            </div>
            
            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 sm:bottom-4">
                <a
                    id="lightbox-download"
                    href=""
                    download=""
                    class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                >
                    @svg('heroicon-o-arrow-down-tray', 'w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2')
                    Download
                </a>
            </div>
        </div>
    </div>

    <!-- PDF Modal -->
    <div id="pdf-modal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-2 sm:p-4">
        <div class="relative w-full h-full max-w-6xl max-h-[95vh] sm:max-h-[90vh] bg-white dark:bg-gray-900 rounded-lg shadow-xl flex flex-col">
            <div class="flex items-center justify-between p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <h3 id="pdf-modal-title" class="text-sm sm:text-lg font-medium text-gray-900 dark:text-gray-100 truncate"></h3>
                <button
                    onclick="closePdfModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors flex-shrink-0 ml-2"
                >
                    @svg('heroicon-o-x-mark', 'w-5 h-5 sm:w-6 sm:h-6')
                </button>
            </div>
            
            <div class="flex-1 p-2 sm:p-4 min-h-0">
                <iframe 
                    id="pdf-iframe" 
                    src="" 
                    class="w-full h-full border-0 rounded"
                    style="min-height: 60vh;"
                ></iframe>
            </div>
            
            <div class="flex items-center justify-between p-3 sm:p-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    PDF Viewer
                </div>
                <a
                    id="pdf-modal-download"
                    href=""
                    download=""
                    class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                >
                    @svg('heroicon-o-arrow-down-tray', 'w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2')
                    Download PDF
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

        function openPdfModal(pdfUrl, filename) {
            const pdfModal = document.getElementById('pdf-modal');
            const pdfIframe = document.getElementById('pdf-iframe');
            const pdfModalTitle = document.getElementById('pdf-modal-title');
            const pdfModalDownload = document.getElementById('pdf-modal-download');
            
            // Set PDF URL with viewer
            pdfIframe.src = pdfUrl + '#toolbar=1&navpanes=1&scrollbar=1';
            pdfModalTitle.textContent = filename;
            pdfModalDownload.href = pdfUrl;
            pdfModalDownload.download = filename;
            
            pdfModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closePdfModal() {
            const pdfModal = document.getElementById('pdf-modal');
            const pdfIframe = document.getElementById('pdf-iframe');
            
            pdfModal.classList.add('hidden');
            pdfIframe.src = '';
            document.body.style.overflow = 'auto';
        }
        
        // Close lightbox when clicking outside the image
        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Close PDF modal when clicking outside
        document.getElementById('pdf-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePdfModal();
            }
        });
        
        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
                closePdfModal();
            }
        });
    </script>
@endif 