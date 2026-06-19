@props([
    'name' => '',
    'label' => '',
    'filePath' => null,  // File path for the edit page (null in create page)
])

<div class="mb-3">
    <label for="{{ $name }}" class="control-label">{{ $label ?? st('Choose a file') }}</label>
    <input type="file" class="form-control" id="{{ $name }}_input" name="{{ $name }}" accept="*">
</div>

<!-- Preview Container -->
<div class="mb-3" id="{{ $name }}_previewContainer" style="display: none;">
    <strong>{{ st('Selected File') }}</strong>
    <div id="{{ $name }}_previewContent">
        @if($filePath && $filePath !== '-' && !str_contains($filePath, '/-'))
            @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $filePath))
                <img id="{{ $name }}_previewImage" src="{{ asset($filePath) }}" alt="{{ st('Image preview') }}"
                     class="img-thumbnail" width="200">
            @elseif(preg_match('/\.(mp4|avi)$/i', $filePath))
                <video id="{{ $name }}_previewVideo" controls width="200">
                    <source src="{{ asset($filePath) }}" type="video/mp4">
                    {{ st('Your browser does not support video playback') }}
                </video>
            @elseif(preg_match('/\.pdf$/i', $filePath))
                <object id="{{ $name }}_previewPdf" data="{{ asset($filePath) }}" type="application/pdf" width="200"
                        height="200">
                    {{ st('Your browser does not support PDF files') }}
                </object>
            @elseif(preg_match('/\.(mp3|wav)$/i', $filePath))
                <audio id="{{ $name }}_previewAudio" controls>
                    <source src="{{ asset($filePath) }}" type="audio/mp3">
                    {{ st('Your browser does not support audio playback') }}
                </audio>
            @else
                <div id="{{ $name }}_fileErrorMessage">
                    {{ st('This file type cannot be displayed') }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- File Button (Disabled if File Doesn't Exist) -->
@if(!$filePath || $filePath === '-' || str_contains($filePath, '/-'))
    <button id="{{ $name }}_fileNotExistBtn" class="btn btn-primary" disabled>
        {{ st('File does not exist') }}
    </button>
@else
    <a id="{{ $name }}_viewFileBtn" href="{{ asset($filePath) }}" target="_blank" class="btn btn-primary">
        {{ st('Show file') }}
    </a>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let input = document.getElementById("{{ $name }}_input");
        let previewContainer = document.getElementById("{{ $name }}_previewContainer");
        let previewContent = document.getElementById("{{ $name }}_previewContent");
        let fileNotExistBtn = document.getElementById("{{ $name }}_fileNotExistBtn");
        let viewFileBtn = document.getElementById("{{ $name }}_viewFileBtn");

        // Show existing file preview if available
        if (previewContent.children.length > 0) {
            previewContainer.style.display = "block";
        }

        input.addEventListener("change", function (event) {
            let file = event.target.files[0];

            if (!file) {
                previewContainer.style.display = "none";
                if (fileNotExistBtn) fileNotExistBtn.style.display = "block";
                if (viewFileBtn) viewFileBtn.style.display = "none";
                return;
            }

            let reader = new FileReader();
            previewContent.innerHTML = ""; // Clear previous preview

            if (file.type.startsWith("image/")) {
                reader.onload = function (e) {
                    previewContent.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" width="200">`;
                    previewContainer.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith("video/")) {
                let videoURL = URL.createObjectURL(file);
                previewContent.innerHTML = `<video controls width="200"><source src="${videoURL}" type="video/mp4"></video>`;
                previewContainer.style.display = "block";
            } else if (file.type === "application/pdf") {
                let pdfURL = URL.createObjectURL(file);
                previewContent.innerHTML = `<object data="${pdfURL}" type="application/pdf" width="200" height="200"></object>`;
                previewContainer.style.display = "block";
            } else if (file.type.startsWith("audio/")) {
                let audioURL = URL.createObjectURL(file);
                previewContent.innerHTML = `<audio controls><source src="${audioURL}" type="audio/mp3"></audio>`;
                previewContainer.style.display = "block";
            } else {
                previewContent.innerHTML = `<div>{{ st('This file type cannot be displayed.') }}</div>`;
                previewContainer.style.display = "block";
            }

            // Disable "File does not exist" button and hide "Show file" button
            if (fileNotExistBtn) fileNotExistBtn.style.display = "none";
            if (viewFileBtn) viewFileBtn.style.display = "none";
        });
    });
</script>
