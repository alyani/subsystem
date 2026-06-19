@props([
    'name' => '',
    'modelName' => '',
    'value' => '',
    'label' => '',
    'options' => [],
    'isImageAllowed' => false,
    'isFontAllowed' => true,
])
<div class="form-group">
    @if($label)
        {{ html()->label($label, $name)->class('control-label') }}
    @endif
    {{ html()->textarea($name, old($name, $value))->rows(20)->class(['form-control', 'tinymce'])->placeholder(trim($label, '*')) }}
</div>

@once
    <script src="{{ asset('vendor/subsystem/js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const plugins = [
                'advlist', 'autolink', 'lists', 'charmap', 'preview', 'anchor', 'link',
                'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'table', 'wordcount', 'emoticons', 'insertdatetime', 'codesample', 'directionality'
                @if($isImageAllowed), 'image' @endif
            ];

            const toolbar = 'undo redo | styleselect | ' +
                    @if($isFontAllowed)'fontfamily fontsize |' + @endif +
                    ' bold italic underline strikethrough | ' +
                'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | ' +
                    @if($isImageAllowed)'image | ' + @endif
                    'link charmap emoticons anchor | forecolor backcolor | ' +
                'searchreplace visualblocks code fullscreen | ' +
                'table tabledelete | tableprops tablerowprops tablecellprops | ' +
                'tableinsertrowbefore tableinsertrowafter tabledeleterow | ' +
                'tableinsertcolbefore tableinsertcolafter tabledeletecol | ' +
                'insertdatetime codesample ltr rtl | preview';

            tinymce.init({
                selector: '.tinymce',
                license_key: 'gpl',
                language: '{{ env('APP_LOCALE') }}',
                plugins: plugins,
                toolbar: toolbar,
                menubar: true,
                branding: false,
                promotion: false,
                forced_root_block: false,
                entity_encoding: 'raw',

                @if($isImageAllowed)
                images_upload_url: '{{ route('tinymce.upload') }}',
                images_upload_credentials: true,
                images_upload_handler: function (blobInfo) {
                    return new Promise((resolve, reject) => {
                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        formData.append('type', 'image');
                        formData.append('modelName', '{{ $modelName }}');

                        fetch('{{ route('tinymce.upload') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                if (!response.ok) throw new Error('Upload failed');
                                return response.json();
                            })
                            .then(data => {
                                if (data.location) {
                                    resolve(data.location);
                                } else {
                                    reject(data.error || 'Invalid server response');
                                }
                            })
                            .catch(error => {
                                reject(error.message);
                            });
                    });
                },
                @endif

                        @foreach($options as $key => $value)
                '{{ $key }}': @json($value),
                @endforeach

                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                    editor.on('init', function () {
                        editor.getContainer().style.transition = 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out';
                    });
                },
                init_instance_callback: function (editor) {
                    editor.on('focus', function () {
                        editor.getContainer().style.borderColor = '#80bdff';
                        editor.getContainer().style.boxShadow = '0 0 0 0.2rem rgba(0, 123, 255, 0.25)';
                    });
                    editor.on('blur', function () {
                        editor.getContainer().style.borderColor = '';
                        editor.getContainer().style.boxShadow = '';
                    });
                }
            });
        });
    </script>
@endonce
