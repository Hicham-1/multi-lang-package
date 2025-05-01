@if (config('h1ch4m_config.layout'))
    @extends(config('h1ch4m_config.layout'))

    @section(config('h1ch4m_config.custom_content'))
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table border-0">
            <tbody>
                <tr>
                    <td>
                        <h2>
                            {{ $item->getTranslation($item->default_title, $default_language) }}
                        </h2>
                    </td>
                    <td>
                        <div class="d-flex justify-content-end">
                            @foreach ($valid_languages as $languageLocal)
                                <form action="{{ route(config('h1ch4m_config.custom_route') . 'languages.edit') }}"
                                    method="GET">
                                    <input type="hidden" name="model" value="{{ $model_name }}">
                                    <input type="hidden" name="language" value="{{ $languageLocal }}">
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <button class="btn btn-primary me-3">
                                        {{ $languageLocal }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div>
            <div class="card mb-3">
                <div class="card-body">
                    <div>{{ $full_default_language }}</div>
                    <br>
                    @foreach ($translatable as $key => $column)
                        <label class="mt-3 fw-bold">{{ ucfirst(str_replace('_', ' ', $column)) }}</label>

                        @if ('array' == $input_type[$key])
                            @php
                                $array_data = $item->getTranslation($column, $default_language, false);
                            @endphp

                            @if (is_array($array_data))
                                @foreach ($array_data as $key => $data)
                                    <div class="mt-3">
                                        <strong>Title:</strong> {{ $data['title'] ?? '' }}
                                        @if ($column === 'programs')
                                            <br>
                                            <strong>Body:</strong> {{ $data['body'] ?? '' }}
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        @else
                            <div>
                                {!! $item->getTranslation($column, $default_language, false) !!}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <form action="{{ route(config('h1ch4m_config.custom_route') . 'languages.store') }}" method="POST">
            @csrf
            <input name="language" value="{{ $language }}" hidden>
            <input name="model" value="{{ $model_name }}" hidden>
            <input name="id" value="{{ $item->id }}" hidden>

            <div class="card mb-3">
                <div class="card-body">
                    <div>{{ $full_language }}</div>
                    <br>
                    @foreach ($translatable as $key => $column)
                        <label for="{{ $column }}"
                            class="mt-3">{{ ucfirst(str_replace('_', ' ', $column)) }}</label>

                        @if ('text' == $input_type[$key])
                            <input class="form-control" id="{{ $column }}"
                                name="data[{{ $column }}][{{ $item->id }}]"
                                value="{{ $item->getTranslation($column, $language, false) }}">
                        @elseif ('textarea' == $input_type[$key])
                            <textarea name="data[{{ $column }}][{{ $item->id }}]" class="form-control" placeholder="Content of body">{{ $item->getTranslation($column, $language, false) }}</textarea>
                        @elseif ('editor' == $input_type[$key])
                            <textarea id="pc-tinymce" name="data[{{ $column }}][{{ $item->id }}]" class="form-control textarea"
                                placeholder="Content of body">{{ $item->getTranslation($column, $language, false) }}</textarea>
                        @elseif ('array' == $input_type[$key])
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-light-primary"
                                        onclick="addItem('{{ $item->id }}', '{{ $column }}')"> Add
                                        {{ ucfirst($column) }}
                                    </button>
                                </div>
                                <div class="card-body"></div>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th> Title </th>
                                            @if ($column == 'programs')
                                                <th> Body </th>
                                            @endif
                                            <th width="10%"> Action </th>
                                        </tr>
                                    </thead>
                                    <tbody id="area-{{ $column }}-{{ $item->id }}">
                                        @if (is_array($item->getTranslation($column, $language, false)))
                                            @foreach ($item->getTranslation($column, $language, false) as $key => $data)
                                                @php $id = $item->id . ($key + 1); @endphp
                                                <tr id="{{ $column }}-{{ $id }}">
                                                    <td>
                                                        <input type="text"
                                                            name="data[{{ $column }}][{{ $item->id }}][][title]"
                                                            class="form-control form-control-sm" placeholder="Title"
                                                            value="{{ $data['title'] ?? '' }}" required>
                                                    </td>
                                                    @if ($column == 'programs')
                                                        <td>
                                                            <textarea name="data[{{ $column }}][{{ $item->id }}][][body]" class="form-control textarea"
                                                                placeholder="Body">{{ $data['body'] ?? '' }}</textarea>
                                                        </td>
                                                    @endif
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-link text-danger"
                                                            onclick="removeItem('{{ $column }}', {{ $id }})">
                                                            <i class="fas fa-trash fa-2x"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endforeach

                    <div class="d-flex justify-content-between mt-3">
                        <div></div>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
        </form>
    @endsection

    @section(config('h1ch4m_config.custom_javascript'))
        <script>
            function addItem(itemId, column) {
                let id = Date.now();
                let area = document.getElementById(`area-${column}-${itemId}`);

                let html = `<tr id="${column}-${id}">
                            <td>
                                <input type="text" name="data[${column}][${itemId}][][title]" class="form-control form-control-sm"
                                    placeholder="Title" required>
                            </td>`;

                if (column === "programs") {
                    html += `<td>
                            <textarea name="data[${column}][${itemId}][][body]" class="form-control textarea"
                                id="editor-${column}-${id}" placeholder="Body"></textarea>
                        </td>`;
                }

                html += `<td>
                        <button type="button" class="btn btn-sm btn-link text-danger"
                            onclick="removeItem('${column}', ${id})">
                            <i class="fas fa-trash fa-2x"></i>
                        </button>
                    </td>
                </tr>`;

                area.insertAdjacentHTML("beforeend", html);

                if (column === "programs") {
                    setTimeout(() => {
                        tinymce.init({
                            selector: `#editor-${column}-${id}`,
                            height: 400,
                            menubar: false,
                            plugins: 'advlist autolink link image lists charmap print preview code',
                            toolbar: [
                                'styleselect fontselect fontsizeselect',
                                'undo redo | cut copy paste | bold italic | link  | alignleft aligncenter alignright alignjustify',
                                'bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists | code'
                            ],
                            content_style: 'body { font-family: "Inter", sans-serif; }',
                        });
                    }, 100);
                }
            }

            function removeItem(column, id) {
                let row = document.getElementById(`${column}-${id}`);
                if (row) {
                    row.remove();
                }
            }
        </script>
    @endsection
@endif
