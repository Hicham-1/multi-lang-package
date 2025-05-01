@if (config('h1ch4m_config.layout'))
    @extends(config('h1ch4m_config.layout'))
    @section('title', __('Models Translate'))

    @section(config('h1ch4m_config.custom_content'))

        <table class="table">
            <thead>
                <tr>
                    <td>{{ __('Model') }}</td>
                    <td>{{ __('Action') }}</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($models as $model => $custom_name)
                    <tr>
                        <td>{{ $custom_name }}</td>
                        <td>
                            <form
                                action="{{ route(config('h1ch4m_config.custom_route') . 'languages.records', ['model' => $model]) }}"
                                method="GET">
                                <button class="btn btn-primary">
                                    {{ __('Open') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endsection

    @section(config('h1ch4m_config.custom_javascript'))

    @endsection
@endif
