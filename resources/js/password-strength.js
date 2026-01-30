document.addEventListener('DOMContentLoaded', () => {
    const RULES = [
        { key: 'length', test: v => v.length >= 8, text: 'At least 8 characters' },
        { key: 'upper', test: v => /[A-Z]/.test(v), text: 'At least one uppercase letter' },
        { key: 'number', test: v => /[0-9]/.test(v), text: 'At least one number' },
        { key: 'special', test: v => /[^A-Za-z0-9]/.test(v), text: 'At least one special character' },
    ];

    function createRequirementsNode() {
        const wrapper = document.createElement('div');
        wrapper.className = 'password-requirements mt-2 text-sm';
        const ul = document.createElement('ul');
        ul.className = 'space-y-1';
        RULES.forEach(r => {
            const li = document.createElement('li');
            li.dataset.rule = r.key;
            li.className = 'text-red-600 flex items-center gap-2';
            li.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><span>${r.text}</span>`;
            ul.appendChild(li);
        });
        wrapper.appendChild(ul);
        return wrapper;
    }

    function updateRequirements(node, value) {
        RULES.forEach(r => {
            const li = node.querySelector(`[data-rule="${r.key}"]`);
            if (!li) return;
            const ok = r.test(value);
            li.classList.toggle('text-green-600', ok);
            li.classList.toggle('text-red-600', !ok);
            if (ok) li.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
            else li.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        });
    }

    function addEyeToggle(input) {
        if (input._eyeAttached) return;
        input._eyeAttached = true;

        // ensure input uses modal style for unity
        input.classList.add('modal-input', 'pr-10');

        // wrap if necessary
        const parent = input.parentElement;
        let wrapper = parent;
        if (!parent.classList.contains('relative')) {
            wrapper = document.createElement('div');
            wrapper.className = 'relative';
            parent.replaceChild(wrapper, input);
            wrapper.appendChild(input);
        }

        // create toggle button
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800';
        btn.setAttribute('aria-label', 'Toggle password visibility');
        btn.innerHTML = `<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
        wrapper.appendChild(btn);

        btn.addEventListener('click', () => {
            const isPwd = input.type === 'password';
            input.type = isPwd ? 'text' : 'password';
            // toggle icon
            btn.innerHTML = isPwd
                ? '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.223-3.338M6.1 6.1A9.955 9.955 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.99 9.99 0 01-4.2 5.4M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
                : '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        });
    }

    function formHasInvalidPassword(form) {
        const pwd = form.querySelector('input[type="password"][name="password"]');
        if (!pwd) return false;
        const validRules = RULES.every(r => r.test(pwd.value));
        const conf = form.querySelector('input[type="password"][name="password_confirmation"]');
        const match = conf ? (pwd.value === conf.value && pwd.value.length > 0) : true;
        return !(validRules && match);
    }

    document.querySelectorAll('form').forEach(form => {
        const pwd = form.querySelector('input[type="password"][name="password"]');
        if (!pwd) return; // only attach to forms that create/change password

        // avoid duplicating UI
        if (pwd._pwdReqAttached) return;
        pwd._pwdReqAttached = true;

        // find or create the confirmation field
        const conf = form.querySelector('input[type="password"][name="password_confirmation"]');

        // apply eye toggles and modal styling
        addEyeToggle(pwd);
        if (conf) addEyeToggle(conf);

        const reqNode = createRequirementsNode();
        pwd.insertAdjacentElement('afterend', reqNode);

        // create match indicator under confirmation field if present
        let matchNode = null;
        if (conf) {
            matchNode = document.createElement('div');
            matchNode.className = 'password-match mt-2 text-sm text-red-600 flex items-center gap-2';
            matchNode.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg><span>Passwords match</span>';
            conf.insertAdjacentElement('afterend', matchNode);
        }

        function updateMatch() {
            if (!matchNode) return;
            const ok = pwd.value.length > 0 && pwd.value === conf.value;
            matchNode.classList.toggle('text-green-600', ok);
            matchNode.classList.toggle('text-red-600', !ok);
            matchNode.querySelector('svg').innerHTML = ok ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        }

        // live update on input
        pwd.addEventListener('input', () => {
            updateRequirements(reqNode, pwd.value);
            updateMatch();
        });

        if (conf) {
            conf.addEventListener('input', () => {
                updateMatch();
            });
        }

        // prevent submission if password invalid
        form.addEventListener('submit', (e) => {
            if (formHasInvalidPassword(form)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                pwd.focus();
                // flash red border
                pwd.classList.add('ring-2', 'ring-red-500');
                setTimeout(() => pwd.classList.remove('ring-2','ring-red-500'), 2000);
                return false;
            }
            return true;
        });
    });
});
