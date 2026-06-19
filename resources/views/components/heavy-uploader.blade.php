@props([
    'name' => '',
    'label' => '',
    'modelName' => '',
    'maxSize' => 128,
    'acceptMimes' => '*',
    'fileUrl' => old('url') ?? null,
    'SID' => old($name . 'SID') ?? null,
])

@php
    $hasFile = $fileUrl || $SID;
@endphp

<div class="">
    {{ html()->label($label ?: ucfirst($name) . ' *', $name . 'SID')->class('control-label') }}
    <input type="file" name="{{ $name }}" class="form-control" id="file-input" accept="{{ $acceptMimes }}" style="{{ $hasFile ? 'display: none;' : '' }}">
</div>

{{ html()->hidden($name . 'SID', $SID)->id('sid-input') }}

<!-- Progress bar -->
<div class="progress mt-2" style="height: 20px; display: none;">
    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
         role="progressbar"
         style="width: 0%;"
         aria-valuemin="0"
         aria-valuemax="100"
         aria-valuenow="0"
    >
        0%
    </div>
</div>

<!-- Actions -->
<div id="upload-actions" class="mt-2" style="{{ $hasFile ? '' : 'display: none;' }}">
    <button type="button" id="delete-btn" class="btn btn-danger btn-sm me-2">{{ st('Heavy uploader delete button') }}</button>
    <a target="_blank" rel="noopener" id="show-btn" class="btn btn-primary btn-sm" href="{{ $fileUrl ?? '#' }}">{{ st('Heavy uploader show button') }}</a>
</div>

@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const fileInput = document.getElementById('file-input');
            const sidInput = document.getElementById('sid-input');
            const progressBarContainer = document.querySelector('.progress');
            const progressBar = progressBarContainer.querySelector('.progress-bar');
            const deleteBtn = document.getElementById('delete-btn');
            const showBtn = document.getElementById('show-btn');
            const uploadActions = document.getElementById('upload-actions');

            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    const sid = sidInput.value;
                    if (!sid) return;

                    const confirmed = confirm( `{{ st('Heavy uploader deletion confirm') }}` );
                    if (!confirmed) return;

                    const formData = new FormData();
                    formData.append('SID', sid);

                    fetch("{{ route('heavy.delete') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert(`{{ st('Heavy uploader deletion message') }}`);
                                sidInput.value = '';
                                uploadActions.style.display = 'none';
                                fileInput.value = '';
                                fileInput.style.display = 'block';
                            } else if (data.error) {
                                alert(`{{ st('Heavy uploader custom deletion error') }}` + data.error);
                            }
                        })
                        .catch(() => alert(`{{ st('Heavy uploader deletion error') }}`));
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    const file = fileInput.files[0];
                    if (!file) return;

                    const maxSizeBytes = {{ $maxSize }} * 1024 * 1024;
                    if (file.size > maxSizeBytes) {
                        alert(`{{ st('Heavy uploader size error', ['size' => $maxSize]) }}`);
                        fileInput.value = '';
                        return;
                    }

                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('modelName', '{{ $modelName }}');

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', "{{ route('heavy.upload') }}", true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                    progressBarContainer.style.display = 'block';
                    progressBar.classList.remove('bg-danger');
                    progressBar.classList.add('bg-success');
                    progressBar.style.width = '0%';
                    progressBar.setAttribute('aria-valuenow', 0);
                    progressBar.textContent = '0%';

                    xhr.upload.onprogress = function (e) {
                        if (e.lengthComputable) {
                            let percent = (e.loaded / e.total) * 100;
                            percent = percent >= 99 ? 99 : percent;

                            progressBar.style.width = percent + '%';
                            progressBar.setAttribute('aria-valuenow', percent.toFixed(0));
                            progressBar.textContent = percent.toFixed(0) + '%';
                        }
                    };

                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            try {
                                const data = JSON.parse(xhr.responseText);

                                if (data.error) {
                                    progressBar.classList.remove('bg-success');
                                    progressBar.classList.add('bg-danger');
                                    progressBar.style.width = '100%';
                                    progressBar.setAttribute('aria-valuenow', 100);
                                    progressBar.textContent = 'Error';
                                    alert(`{{ st('Heavy uploader upload error') }}` + data.error);
                                    return;
                                }

                                if (data.SID && data.url) {
                                    sidInput.value = data.SID;
                                    progressBar.style.width = '100%';
                                    progressBar.setAttribute('aria-valuenow', 100);
                                    progressBar.textContent = '100%';
                                    alert(`{{ st('Heavy uploader success message') }}`);

                                    fileInput.style.display = 'none';
                                    uploadActions.style.display = 'block';
                                    showBtn.href = data.url;
                                } else {
                                    alert(`{{ st('Heavy uploader SID error') }}`);
                                }
                            } catch (e) {
                                alert(`{{ st('Heavy uploader invalid response') }}`);
                            }
                        } else {
                            alert(`{{ st('Heavy uploader status error') }}` + xhr.status);
                        }

                        setTimeout(() => {
                            progressBar.style.width = '0%';
                            progressBar.setAttribute('aria-valuenow', 0);
                            progressBar.textContent = '0%';
                            progressBarContainer.style.display = 'none';
                        }, 1500);
                    };

                    xhr.onerror = function () {
                        alert(`{{ st('Heavy uploader error accorded') }}`);
                        progressBar.classList.remove('bg-success');
                        progressBar.classList.add('bg-danger');
                        progressBar.style.width = '100%';
                        progressBar.textContent = 'Failed';
                        progressBar.setAttribute('aria-valuenow', 100);
                        setTimeout(() => {
                            progressBar.style.width = '0%';
                            progressBar.setAttribute('aria-valuenow', 0);
                            progressBar.textContent = '0%';
                            progressBarContainer.style.display = 'none';
                        }, 1500);
                    };

                    xhr.send(formData);
                });
            }
        });
    </script>
@endpush
