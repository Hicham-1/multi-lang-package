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
    deleteButton.addEventListener('click', function () {
        pill.remove();
    });

    pill.addEventListener('click', function () {
        if (!pill.classList.contains('selected')) {
            let pill_value = pill.getAttribute('data-value');
            const allPills = document.querySelectorAll('.pill');
            allPills.forEach(function (otherPill) {
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