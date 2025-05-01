@if (config('h1ch4m_config.layout'))
    @extends(config('h1ch4m_config.layout'))

    @section('title', __('Languages Setting'))

    @section(config('h1ch4m_config.custom_style'))
        <style>
            #language-container {
                margin-bottom: 20px;
            }

            #language-dropdown {
                padding: 8px;
                margin-right: 10px;
            }

            .pill {
                display: inline-block;
                padding: 8px 15px;
                margin: 5px;
                background-color: #007bff;
                color: white;
                border-radius: 25px;
                font-size: 14px;
                cursor: pointer;
            }

            .pill.selected {
                background-color: #199e08 !important;
            }

            .pill .delete-btn {
                margin-left: 10px;
                font-weight: bold;
                cursor: pointer;
            }

            #add-button,
            #save-button {
                padding: 10px 15px;
                font-size: 14px;
                cursor: pointer;
            }

            .pill-container {
                margin-bottom: 20px;
            }
        </style>
    @endsection

    @section(config('h1ch4m_config.custom_content'))
        <form method="POST" action="{{ route(config('h1ch4m_config.custom_route') . 'languages.store_setting') }}">
            @csrf
            <div id="language-container" class="d-flex">
                <select id="language-dropdown" class="form-control">
                    @foreach ($languages as $lang => $language)
                        <option value="{{ $lang }}" data-name="{{ $language['name'] }}">{{ $language['name'] }}
                        </option>
                    @endforeach
                </select>
                <button id="add-button" type="button" class="btn btn-primary">Add</button>
            </div>
            <input class="d-none" name="default" id="default_language">

            <div class="pill-container" id="pill-container">

            </div>

            <button id="save-button" class="btn btn-primary" type="submit">Save</button>
        </form>
    @endsection

    @section(config('h1ch4m_config.custom_javascript'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @foreach ($saved_data as $item)
                    @if ($item->is_default)
                        addPill('{{ $item->language }}', '{{ $languages[$item->language]['name'] }}',
                            {{ $item->is_default }});
                    @else
                        addPill('{{ $item->language }}', '{{ $languages[$item->language]['name'] }}');
                    @endif
                @endforeach
            });
        </script>

        <script>
            const addButton = document.getElementById('add-button');
            const languageDropdown = document.getElementById('language-dropdown');
            const pillContainer = document.getElementById('pill-container');

            addButton.addEventListener('click', addPill);


            function addPill(default_lang = null, full_language = null, is_default = null) {
                default_lang = ((typeof default_lang) == 'string') ? default_lang : null;
                full_language = ((typeof full_language) == 'string') ? full_language : null;
                is_default = ((typeof is_default) == 'number') ? is_default : null;

                const value = default_lang ?? languageDropdown.value;

                const existingInputs = document.getElementsByName('languages[]') ?? [];
                for (let i = 0; i < existingInputs.length; i++) {
                    if (existingInputs[i].value === value) {
                        return;
                    }
                }

                let default_language = document.getElementById('default_language');

                var selectedOption = languageDropdown.options[languageDropdown.selectedIndex];
                const selectedLanguage = full_language ?? selectedOption.getAttribute('data-name');

                const inputVal = document.createElement('input');
                inputVal.name = 'languages[]';
                inputVal.value = value;
                inputVal.type = 'hidden';

                const pill = document.createElement('div');
                pill.classList.add('pill');
                console.log(default_lang, full_language, is_default);

                if ((existingInputs.length == 0 && default_lang == null) || is_default) {
                    pill.classList.add('selected');
                    default_language.value = value;
                }
                pill.textContent = selectedLanguage;
                pill.setAttribute('data-value', value);

                const deleteButton = document.createElement('span');
                deleteButton.textContent = '×';
                deleteButton.classList.add('delete-btn');
                deleteButton.addEventListener('click', function() {
                    pill.remove();
                });

                pill.addEventListener('click', function() {
                    if (!pill.classList.contains('selected')) {
                        let pill_value = pill.getAttribute('data-value');
                        const allPills = document.querySelectorAll('.pill');
                        allPills.forEach(function(otherPill) {
                            if (otherPill.getAttribute('data-value') !== pill_value) {
                                otherPill.classList.remove('selected');
                            }
                        });

                        default_language.value = pill_value;
                        pill.classList.add('selected');
                        pill.appendChild(inputVal);
                    }
                });

                pill.appendChild(deleteButton);
                pill.appendChild(inputVal);
                pillContainer.appendChild(pill);
            }
        </script>
    @endsection
@endif
