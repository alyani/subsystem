@extends('subsystem::layouts.app')

@section('content')
    @if(view()->hasSection('formFields'))
        <div class="admin-soft-table-card mb-4">
            <div class="admin-table-title">
                <i class="fas fa-filter"></i> {{ st('Filter') }}
            </div>
            {!! html()->form('GET', url()->current())->class('form')->open() !!}
            <div class="row">
                @yield('formFields')
            </div>
            <div class="text-right">
                {{ html()->submit('<i class="fa fa-search"></i>')->class('btn btn-primary') }}
                {{ html()->a(url()->current(), '<i class="fas fa-trash-alt"></i>')->class('btn btn-secondary ml-2') }}
            </div>
            {!! html()->form()->close() !!}
        </div>
    @endif

    @if(view()->hasSection('btn'))
        <div class="admin-soft-table-card mb-2">
            <div class="d-flex justify-content-start">
                <div class="w-100 mr-2">
                    @yield('btn')
                </div>
            </div>
        </div>
    @endif

    <div class="admin-soft-table-card">
        <div class="table-responsive">
            {{ $dataTable->table(['class' => 'table soft-table']) }}
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                theme: 'bootstrap4',
                allowClear: true
            });
        });

        function setDataLabels() {
            document.querySelectorAll('.soft-table').forEach(function (table) {
                const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText.trim());
                table.querySelectorAll('tbody tr').forEach(function (row) {
                    row.querySelectorAll('td').forEach(function (td, index) {
                        if (!td.hasAttribute('data-label') && headers[index]) {
                            td.setAttribute('data-label', headers[index]);
                        }
                    });
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(setDataLabels, 300);
        });
        $(document).on('draw.dt', function () {
            setDataLabels();
        });
    </script>
    {{ $dataTable->scripts() }}
@endpush
