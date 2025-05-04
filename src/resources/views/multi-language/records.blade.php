@if (config('h1ch4m_config.layout'))
    @extends(config('h1ch4m_config.layout'))

    {{-- @section('title', $model_instance->custom_name . __(' Translate')) --}}

    @section(config('h1ch4m_config.custom_content'))
        <div class="d-flex align-items-center mb-3">
            <a class="btn btn-primary me-3" href="{{ route(config('h1ch4m_config.custom_route') . 'languages.models') }}">
                {{ __('Go back') }}
            </a>
            <h2 class="m-0">
                {{ $model_instance->custom_name . __(' Translate') }}
            </h2>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <td>{{ __('Record') }}</td>
                    <td>{{ __('Languages') }}</td>
                </tr>
            </thead>
            @if ($model_instance->parent_method)
                <tbody>
                    @foreach ($data as $group_name => $records)
                        @php $group_index = $loop->index; @endphp
                        <tr>
                            <td colspan="2">
                                <button class="btn btn-secondary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#tourGroup{{ $group_index }}" aria-expanded="false"
                                    aria-controls="tourGroup{{ $group_index }}">
                                    {{ $group_name }}
                                </button>
                            </td>
                        </tr>
                        @foreach ($records as $record)
                            <tr class="collapse {{ $group_index == 0 ? 'show' : '' }}" id="tourGroup{{ $group_index }}">
                                <td>{{ $record->getTranslation($record->default_title, $default_language) }}</td>
                                <td>
                                    <div class="d-flex">
                                        @foreach ($valid_languages as $language)
                                            <form
                                                action="{{ route(config('h1ch4m_config.custom_route') . 'languages.edit') }}"
                                                method="GET">
                                                <input type="hidden" name="model" value="{{ $model_name }}">
                                                <input type="hidden" name="language" value="{{ $language }}">
                                                <input type="hidden" name="id" value="{{ $record->id }}">
                                                <button class="btn btn-primary me-3">
                                                    {{ $language }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            @else
                <tbody>
                    @foreach ($data as $record)
                        <tr>
                            <td>{{ $record->getTranslation($record->default_title, $default_language) }}</td>
                            <td>
                                <div class="d-flex">
                                    @foreach ($valid_languages as $language)
                                        <form action="{{ route(config('h1ch4m_config.custom_route') . 'languages.edit') }}"
                                            method="GET">
                                            <input type="hidden" name="model" value="{{ $model_name }}">
                                            <input type="hidden" name="language" value="{{ $language }}">
                                            <input type="hidden" name="id" value="{{ $record->id }}">
                                            <button class="btn btn-primary me-3">
                                                {{ $language }}
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            @endif
        </table>
    @endsection

    @section(config('h1ch4m_config.custom_javascript'))
    @endsection
@endif
