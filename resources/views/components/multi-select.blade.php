@php use Illuminate\Support\Collection; @endphp
@props([
    'name' => '',
    'options' => [],
    'label' => '',
    'selected' => []
])
<div class="form-group">
    {{ html()->label($label, $name)->class('control-label') }}
    <div class="d-flex gap-2">
        {{ html()->select($name . '_temp', $options instanceof Collection ? $options->toArray() : $options, null)
            ->placeholder(st('Select the options'))
            ->class('form-control')
            ->id($name . 'Select')
        }}
        <button type="button" class="btn btn-success" onclick="addMultiSelectItem('{{ $name }}')">+</button>
    </div>
    <ul id="{{ $name }}List" class="mt-2 list-group">
        @php
            // Convert selected to array if it's a Collection
            $selected = $selected instanceof Collection ? $selected->toArray() : $selected;
            // Ensure selectedValues is always an array
            $selectedValues = (array)old($name, (!is_array($selected)) ? [$selected] : $selected);
        @endphp
        @if(!empty($selectedValues))
            @foreach($selectedValues as $key)
                @php
                    // Handle both array and Collection options
                    $optionValue = $options instanceof Collection
                        ? $options->get($key)
                        : ($options[$key] ?? null);
                @endphp
                @if(!is_null($optionValue))
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $key }}">
                        {{ $optionValue }}
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeMultiSelectItem(this)">-
                        </button>
                        <input type="hidden" name="{{ $name }}[]" value="{{ $key }}">
                    </li>
                @endif
            @endforeach
        @endif
    </ul>
</div>

<script>
    function addMultiSelectItem(fieldName) {
        let select = document.getElementById(fieldName + 'Select');
        let selectedValue = select.value;
        let selectedText = select.options[select.selectedIndex]?.text || "";

        if (!selectedValue || selectedValue === "null" || selectedValue === "" || selectedText.trim() === "") {
            return;
        }

        let list = document.getElementById(fieldName + 'List');

        if (document.querySelector(`#${fieldName}List li[data-id="${selectedValue}"]`)) {
            return;
        }

        let li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.setAttribute('data-id', selectedValue);
        li.innerHTML = `
            ${selectedText}
            <button type="button" class="btn btn-danger btn-sm" onclick="removeMultiSelectItem(this)">-</button>
            <input type="hidden" name="${fieldName}[]" value="${selectedValue}">
        `;

        list.appendChild(li);
        select.selectedIndex = 0;
    }

    function removeMultiSelectItem(btn) {
        btn.parentElement.remove();
    }

    function ensureMultiSelectOnSubmit(fieldName) {
        let select = document.getElementById(fieldName + 'Select');
        let list = document.getElementById(fieldName + 'List');
        let val = select.value;

        if (val && !document.querySelector(`#${fieldName}List li[data-id="${val}"]`)) {
            let hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = fieldName + '[]';
            hidden.value = val;
            list.appendChild(hidden);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('select[id$="Select"]').forEach(function (sel) {
            let fieldName = sel.id.replace(/Select$/, '');
            let form = sel.closest('form');
            if (form) {
                form.addEventListener('submit', function () {
                    ensureMultiSelectOnSubmit(fieldName);
                });
            }
        });
    });
</script>
