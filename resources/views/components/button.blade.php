<form method="POST" action="{{ $action }}" class="inline-block my-1 align-middle" @if($confirm) data-papers-confirm="{{ $confirm }}" @endif>
    @csrf
    @foreach($hidden as $name => $value)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
    @endforeach
    <button type="submit" class="papers-button papers-button-{{ $style }}">{{ $label }}</button>
</form>
