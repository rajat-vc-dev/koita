$(function () {
    function clearFieldError($field) {
        $field.removeClass('poll-field-invalid').removeAttr('aria-invalid aria-errormessage');
        $field.closest('.poll-field').find('.poll-error, .recaptcha-error').prop('hidden', true);
    }

    function setFieldError($field, message) {
        const $group = $field.closest('.poll-field');
        const errorId = $field.attr('id') + '-client-error';
        let $error = $group.children('.poll-client-error');

        if (!$error.length) {
            $error = $('<p>', { id: errorId, class: 'poll-error poll-client-error', role: 'alert' }).appendTo($group);
        }

        $field.addClass('poll-field-invalid').attr({ 'aria-invalid': 'true', 'aria-errormessage': errorId });
        $error.prop('hidden', false).text(message);
    }

    function submitFeedback($form, label) {
        $form.find(':submit').prop('disabled', true).text(label);
    }

    const $registration = $('#poll-register');
    const $verification = $('#poll-verify');
    const $authForms = $registration.add($verification);

    $authForms.prop('noValidate', true).on('input change', '.poll-field input', function () {
        clearFieldError($(this));
    });

    $registration.on('submit', function (event) {
        const $name = $('#poll-name');
        const $email = $('#poll-email');
        const $captcha = $('#poll-captcha-widget');
        const $fields = $name.add($email).add($captcha);
        let $firstInvalid = $();

        function fail($field, message) {
            setFieldError($field, message);
            if (!$firstInvalid.length) {
                $firstInvalid = $field;
            }
        }

        $fields.each(function () { clearFieldError($(this)); });

        if (!$.trim($name.val())) {
            fail($name, 'Please enter your full name.');
        }

        if (!$.trim($email.val())) {
            fail($email, 'Please enter your email address.');
        } else if ($email[0].validity.typeMismatch) {
            fail($email, 'Please enter a valid email address.');
        }

        if ($captcha.find('.g-recaptcha').length && (typeof grecaptcha === 'undefined' || !grecaptcha.getResponse())) {
            fail($captcha, 'Please complete the reCAPTCHA verification.');
        }

        if ($firstInvalid.length) {
            event.preventDefault();
            $firstInvalid.trigger('focus');
            return;
        }

        submitFeedback($registration, 'Sending code…');
    });

    $verification.on('submit', function (event) {
        const $code = $('#poll-code');
        const code = $.trim($code.val());
        clearFieldError($code);

        if (!/^[0-9]{6}$/.test(code)) {
            event.preventDefault();
            setFieldError($code, 'Please enter the six-digit verification code.');
            $code.trigger('focus');
            return;
        }

        $code.val(code);
        submitFeedback($verification, 'Verifying…');
    });

    const $ballot = $('#poll-ballot');

    if (!$ballot.length) {
        return;
    }

    const $choices = $ballot.find('input[name="choices[]"]');
    const $submit = $('#poll-submit');
    const $count = $('#poll-count');
    const min = Number($ballot.data('min'));
    const max = Number($ballot.data('max'));
    let ranked = $choices.filter(':checked').get()
        .sort((left, right) => left.dataset.rank - right.dataset.rank).slice(0, max);

    const $rankedList = $('#poll-ranked-choices');
    const $trayHint = $('.poll-tray-hint');
    const ordinals = ['1st', '2nd', '3rd', '4th', '5th'];

    function renderChoices() {
        $choices.each(function () {
            const rank = ranked.indexOf(this);
            const selected = rank >= 0;
            const title = this.dataset.title;
            const $choice = $(this);
            const $card = $choice.closest('.poll-card');

            $choice.prop({
                checked: selected,
                disabled: !$submit.length || (!selected && ranked.length >= max),
            }).attr('aria-label', selected
                ? title + ', ' + ordinals[rank] + ' choice. Uncheck to remove.'
                : 'Vote for ' + title);
            $choice.next().text(selected ? ordinals[rank] + ' choice ✓' : 'Vote');
            $card.toggleClass('poll-card-selected', selected)
                .find('.poll-card-rank').prop('hidden', !selected).text(selected ? ordinals[rank] : '');
        });

        $rankedList.empty().prop('hidden', !ranked.length);
        $trayHint.prop('hidden', ranked.length > 0);

        ranked.forEach((choice, index) => {
            const title = choice.dataset.title;
            const $item = $('<li>', { class: 'poll-ranked-item' }).appendTo($rankedList);
            $('<span>', { class: 'poll-ranked-number', 'aria-hidden': 'true' }).text(index + 1).appendTo($item);
            $('<span>', { class: 'poll-ranked-title' }).text(title).appendTo($item);

            [
                ['up', '▲', 'Move ' + title + ' up', index === 0],
                ['down', '▼', 'Move ' + title + ' down', index === ranked.length - 1],
                ['remove', '×', 'Remove ' + title, false],
            ].forEach(([action, symbol, label, disabled]) => {
                $('<button>', {
                    type: 'button',
                    'data-action': action,
                    'aria-label': label,
                    disabled,
                }).text(symbol).appendTo($item);
            });
        });

        $count.text(ranked.length + ' of ' + max + ' selected · minimum ' + min + ' required');
        $submit.prop('disabled', ranked.length < min);
    }

    $rankedList.on('click', 'button', function () {
        const index = $(this).closest('li').index();
        const action = this.dataset.action;
        const choice = ranked[index];

        if (action === 'remove') {
            ranked.splice(index, 1);
            renderChoices();
            const $next = $rankedList.children().eq(Math.min(index, ranked.length - 1));
            ($next.find('[data-action="remove"]')[0] || choice).focus({ preventScroll: true });
            return;
        }

        const target = index + (action === 'up' ? -1 : 1);
        [ranked[index], ranked[target]] = [ranked[target], ranked[index]];
        renderChoices();
        const $item = $rankedList.children().eq(target);
        const button = $item.find('[data-action="' + action + '"]:enabled')[0] || $item.find('button:enabled')[0];
        button.focus({ preventScroll: true });
    });

    $choices.removeAttr('name').on('change', function () {
        ranked = this.checked ? [...ranked, this] : ranked.filter(selected => selected !== this);
        renderChoices();
    });

    $ballot.on('submit', function (event) {
        if (ranked.length < min || ranked.length > max) {
            event.preventDefault();
            return;
        }

        $ballot.find('input[name="choices[]"]').remove();
        ranked.forEach(choice => {
            $('<input>', { type: 'hidden', name: 'choices[]', value: choice.value }).appendTo($ballot);
        });

        submitFeedback($ballot, 'Submitting votes…');
    });

    $('[data-dialog]').each(function () {
        const dialog = document.getElementById(this.dataset.dialog);
        if (typeof dialog.showModal === 'function') {
            $(this).prop('hidden', false).on('click', () => dialog.showModal());
        }
    });

    renderChoices();
});
