@if($errors->has($field))
    <p class="mt-1 text-sm text-red-600" role="alert">{{ $errors->first($field) }}</p>
@endif