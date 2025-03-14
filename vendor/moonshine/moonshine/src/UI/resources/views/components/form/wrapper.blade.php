@props([
    'label' => '',
    'formName' => '',
    'fieldErrors' => [],
    'beforeLabel' => false,
    'insideLabel' => false,
    'before',
    'after',
])
<div {{ $attributes->merge(['class' => 'form-group moonshine-field'])->except('required') }}
     x-id="['input-wrapper', 'field-{{ $formName }}']"
     :id="$id('input-wrapper')"
     data-validation-wrapper
>
    {{ $beforeLabel && !$insideLabel ? $slot : '' }}

    @if($label)
        <x-moonshine::form.label
            :required="$attributes->get('required', false)"
            ::for="$id('field-{{ $formName }}')"
        >
            {{ $beforeLabel && $insideLabel ? $slot : '' }}
            {!! $label !!}
            {{ !$beforeLabel && $insideLabel ? $slot : '' }}
        </x-moonshine::form.label>
    @endif

    <div data-validation-wrapper>
        {{ $before ?? '' }}

        {{ !$beforeLabel && !$insideLabel ? $slot : '' }}

        {{ $after ?? '' }}

        @foreach($fieldErrors as $error)
            <x-moonshine::form.input-error>
                {{ $error }}
            </x-moonshine::form.input-error>
        @endforeach
    </div>
</div>
