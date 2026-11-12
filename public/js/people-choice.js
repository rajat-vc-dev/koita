(() => {
    const ballot = document.querySelector('#poll-ballot');
    const choices = [...ballot.querySelectorAll('input[name="choices[]"]')];
    const submit = document.querySelector('#poll-submit');
    const count = document.querySelector('#poll-count');
    const min = Number(ballot.dataset.min);
    const max = Number(ballot.dataset.max);
    let ranked = choices.filter(choice => choice.checked).sort((a, b) => a.dataset.rank - b.dataset.rank).slice(0, max);

    function renderChoices() {
        choices.forEach(choice => {
            const rank = ranked.indexOf(choice) + 1;
            choice.checked = rank > 0;
            choice.disabled = !submit || (rank === 0 && ranked.length >= max);
            choice.nextElementSibling.textContent = rank ? 'Choice ' + rank + ' · Remove' : 'Vote';
        });

        if (submit) {
            count.textContent = ranked.length + ' of ' + max + ' selected · minimum ' + min + ' required';
            submit.disabled = ranked.length < min;
        }
    }

    choices.forEach(choice => {
        choice.removeAttribute('name');
        choice.addEventListener('change', () => {
            ranked = choice.checked ? [...ranked, choice] : ranked.filter(selected => selected !== choice);
            renderChoices();
        });
    });

    ballot.addEventListener('submit', event => {
        if (ranked.length < min || ranked.length > max) {
            event.preventDefault();
            return;
        }

        ballot.querySelectorAll('input[name="choices[]"]').forEach(input => input.remove());
        ranked.forEach(choice => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'choices[]';
            input.value = choice.value;
            ballot.appendChild(input);
        });
    });

    document.querySelectorAll('[data-dialog]').forEach(button => {
        const dialog = document.getElementById(button.dataset.dialog);
        if (typeof dialog.showModal === 'function') {
            button.hidden = false;
            button.addEventListener('click', () => dialog.showModal());
        }
    });

    renderChoices();
})();
