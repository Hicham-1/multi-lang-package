@if (config('h1ch4m_config.layout'))
    @extends(config('h1ch4m_config.layout'))


    @section(config('h1ch4m_config.custom_style'))
        <style>
            .array-wrapper .array-container {
                transition: opacity 0.3s ease, transform 0.3s ease;
            }

            .array-wrapper .array-container.new {
                opacity: 0;
                transform: scale(0.95);
            }
        </style>
    @endsection

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
                    @foreach ($translatableInputs as $column => $columnInfo)
                        <label class="mt-3 fw-bold">{{ ucfirst(str_replace('_', ' ', $column)) }}</label>
                        <br>
                        @if ('array' == $columnInfo['type'])
                            @php
                                $array_data = $item->getTranslation($column, $default_language, false);
                            @endphp

                            @if (is_array($array_data))
                                @foreach ($array_data as $key => $data)
                                    @foreach ($data as $title => $value)
                                        <div class="mt-3 ms-3">
                                            <strong>{{ ucfirst($title) }}</strong>
                                            <p>{{ $value ?? '' }}</p>
                                        </div>
                                    @endforeach
                                    <hr width="50%">
                                @endforeach
                            @endif
                        @else
                            <div>
                                {!! $item->getTranslation($column, $default_language, false) ?? '---' !!}
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
                    @foreach ($translatableInputs as $column => $columnInfo)
                        <label for="{{ $column }}"
                            class="mt-3">{{ ucfirst(str_replace('_', ' ', $column)) }}</label>

                        @if ('text' == $columnInfo['type'])
                            <input class="form-control" id="{{ $column }}"
                                name="data[{{ $column }}][{{ $item->id }}]"
                                value="{{ $item->getTranslation($column, $language, false) }}">
                        @elseif ('textarea' == $columnInfo['type'])
                            <textarea name="data[{{ $column }}][{{ $item->id }}]" class="form-control" placeholder="Content of body">{{ $item->getTranslation($column, $language, false) }}</textarea>
                        @elseif ('editor' == $columnInfo['type'])
                            <textarea id="pc-tinymce" name="data[{{ $column }}][{{ $item->id }}]" class="form-control textarea"
                                placeholder="Content of body">{{ $item->getTranslation($column, $language, false) }}</textarea>
                        @elseif ('array' == $columnInfo['type'])
                            <button type="button" class="btn btn-outline-primary ms-3 my-3"
                                onclick="addItem('{{ $column }}')">
                                <i class="fas fa-plus-circle"></i> Add {{ ucfirst($column) }}
                            </button>

                            <div class="array-wrapper {{ $column }}">
                                <div class="d-none">
                                    <div class="card array-container {{ $column }} mb-4 p-3 border shadow-sm"
                                        data-column="{{ $column }}" data-item-id="{{ $item->id }}"
                                        data-template="true">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-primary">Item {{ $column }}</strong>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="removeItem(event)">
                                                <i class="fas fa-trash-alt"></i> Remove
                                            </button>
                                        </div>

                                        @foreach ($columnInfo['fields'] as $dataKey => $fieldType)
                                            <div class="mb-3">
                                                <label class="form-label">{{ ucfirst($dataKey) }}</label>
                                                @if ('text' == $fieldType)
                                                    <input type="text" class="form-control"
                                                        data-name="data[{{ $column }}][{{ $item->id }}][__KEY__][{{ $dataKey }}]"
                                                        value="">
                                                @elseif ('textarea' == $fieldType)
                                                    <textarea class="form-control"
                                                        data-name="data[{{ $column }}][{{ $item->id }}][__KEY__][{{ $dataKey }}]"></textarea>
                                                @elseif ('editor' == $fieldType)
                                                    <textarea class="form-control textarea" id="pc-tinymce"
                                                        data-name="data[{{ $column }}][{{ $item->id }}][__KEY__][{{ $dataKey }}]"></textarea>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>



                                @if (is_array($item->getTranslation($column, $language, false)))
                                    @foreach ($item->getTranslation($column, $language, false) as $key => $data)
                                        <div class="card array-container {{ $column }} mb-4 p-3 border shadow-sm"
                                            data-column="{{ $column }}" data-item-id="{{ $item->id }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong class="text-primary">Item {{ $column }}</strong>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="removeItem(event)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>

                                            @foreach ($data as $dataKey => $dataItem)
                                                <div class="mb-3">
                                                    <label class="form-label">{{ ucfirst($dataKey) }}</label>
                                                    @if ('text' == $columnInfo['fields'][$dataKey])
                                                        <input type="text" class="form-control"
                                                            name="data[{{ $column }}][{{ $item->id }}][{{ $key }}][{{ $dataKey }}]"
                                                            value="{{ $dataItem }}">
                                                    @elseif ('textarea' == $columnInfo['fields'][$dataKey])
                                                        <textarea class="form-control"
                                                            name="data[{{ $column }}][{{ $item->id }}][{{ $key }}][{{ $dataKey }}]">{{ $dataItem }}</textarea>
                                                    @elseif ('editor' == $columnInfo['fields'][$dataKey])
                                                        <textarea class="form-control textarea" id="pc-tinymce"
                                                            name="data[{{ $column }}][{{ $item->id }}][{{ $key }}][{{ $dataKey }}]">{{ $dataItem }}</textarea>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
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
            function addItem(column) {
                const wrapper = document.querySelector(`.array-wrapper.${column}`);
                const template = document.querySelector(`[data-template="true"].${column}`);
                if (!wrapper || !template) return;

                const clone = template.cloneNode(true);
                const newKey = Date.now();

                clone.removeAttribute('data-template');
                clone.classList.remove('d-none');
                clone.classList.add('new');

                clone.querySelectorAll('input, textarea').forEach(input => {
                    const dataName = input.getAttribute('data-name');
                    if (!dataName) return;

                    const newName = dataName.replace('__KEY__', newKey);
                    input.setAttribute('name', newName);
                    input.removeAttribute('data-name');
                    input.value = '';
                });


                wrapper.appendChild(clone);

                setTimeout(() => {
                    clone.classList.remove('new');
                }, 10);
            }


            function removeItem(event) {
                event.preventDefault();

                const row = event.target.closest('.array-container');
                if (row) {
                    row.remove();
                }
            }
        </script>
    @endsection
@endif
