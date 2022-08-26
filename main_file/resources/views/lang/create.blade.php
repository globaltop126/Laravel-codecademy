<form class="pl-3 pr-3" method="POST" action="{{ route('lang.store') }}">
    @csrf
    <div class="form-group">
        <label for="code">{{ __('Language Code') }}</label>
        <input class="form-control" type="text" id="code" name="code" required="" placeholder="{{ __('Language Code') }}">
    </div>
    <div class="form-group">
        <button class="btn btn-sm btn-primary rounded-pill" type="submit">{{ __('Create') }}</button>
    </div>
</form>
