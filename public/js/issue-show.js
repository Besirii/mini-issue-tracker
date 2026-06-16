/**
 * Issue detail page interactions, all via AJAX (no page reloads):
 *   - Comments: paginated load + "Load more" + add (with inline validation)
 *   - Tags: attach / detach
 *   - Members: assign / remove
 */
(function () {
    const request = window.IssueTracker.request;

    /* --------------------------------------------------------------------
     * Comments
     * ------------------------------------------------------------------ */
    const commentsRoot = document.getElementById('comments');
    if (commentsRoot) {
        const indexUrl = commentsRoot.dataset.indexUrl;
        const storeUrl = commentsRoot.dataset.storeUrl;
        const list = document.getElementById('comments-list');
        const countEl = document.getElementById('comments-count');
        const loadMoreBtn = document.getElementById('load-more-comments');
        const emptyEl = document.getElementById('comments-empty');
        const form = document.getElementById('comment-form');

        let nextPage = 1;

        async function loadComments(page) {
            const { ok, data } = await request(`${indexUrl}?page=${page}`);
            if (!ok || !data) return;

            list.insertAdjacentHTML('beforeend', data.html);
            countEl.textContent = data.total;
            nextPage = data.current_page + 1;

            loadMoreBtn.classList.toggle('d-none', !data.has_more);
            emptyEl.classList.toggle('d-none', data.total !== 0);
        }

        loadMoreBtn.addEventListener('click', () => loadComments(nextPage));

        function clearErrors() {
            form.querySelectorAll('[data-error]').forEach((el) => (el.textContent = ''));
        }

        function showErrors(errors) {
            Object.keys(errors).forEach((field) => {
                const el = form.querySelector(`[data-error="${field}"]`);
                if (el) el.textContent = errors[field][0];
            });
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearErrors();

            const payload = {
                author_name: form.author_name.value,
                body: form.body.value,
            };

            const { ok, status, data } = await request(storeUrl, {
                method: 'POST',
                body: payload,
            });

            if (status === 422 && data && data.errors) {
                showErrors(data.errors);
                return;
            }

            if (ok && data) {
                list.insertAdjacentHTML('afterbegin', data.html);
                countEl.textContent = data.total;
                emptyEl.classList.add('d-none');
                form.reset();
            }
        });

        // Initial page of comments.
        loadComments(1);
    }

    /* --------------------------------------------------------------------
     * Generic attach/detach helper for the tag + member panels
     * ------------------------------------------------------------------ */
    function setupRelation({ panelId, listId, selectId, buttonId, idField, emptyText }) {
        const panel = document.getElementById(panelId);
        const list = document.getElementById(listId);
        if (!panel || !list) return;

        const select = document.getElementById(selectId);
        const button = document.getElementById(buttonId);
        const attachUrl = panel.dataset.attachUrl || panel.dataset.assignUrl;

        function removeEmptyPlaceholder() {
            const placeholder = list.querySelector('[data-empty]');
            if (placeholder) placeholder.remove();
        }

        function maybeAddEmptyPlaceholder() {
            if (list.children.length === 0) {
                const span = document.createElement('span');
                span.className = 'text-muted small';
                span.setAttribute('data-empty', '');
                span.textContent = emptyText;
                list.appendChild(span);
            }
        }

        function addOption(value, label) {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            select.appendChild(option);
        }

        // Attach / assign.
        if (button && select) {
            button.addEventListener('click', async () => {
                const value = select.value;
                if (!value) return;

                const { ok, data } = await request(attachUrl, {
                    method: 'POST',
                    body: { [idField]: value },
                });

                if (ok && data) {
                    removeEmptyPlaceholder();
                    list.insertAdjacentHTML('beforeend', data.html);
                    const chosen = select.querySelector(`option[value="${value}"]`);
                    if (chosen) chosen.remove();
                    select.value = '';
                }
            });
        }

        // Detach / remove (delegated).
        list.addEventListener('click', async (event) => {
            const btn = event.target.closest('button[data-url]');
            if (!btn) return;

            const { ok, data } = await request(btn.dataset.url, { method: 'DELETE' });
            if (!ok) return;

            const chip = btn.closest('[data-tag-id], [data-user-id]');
            if (chip) chip.remove();

            // Put the option back into the select so it can be re-added.
            if (select && data) {
                const entity = data.tag || data.user;
                if (entity) addOption(entity.id, entity.name);
            }

            maybeAddEmptyPlaceholder();
        });
    }

    setupRelation({
        panelId: 'tags-panel',
        listId: 'issue-tags',
        selectId: 'tag-select',
        buttonId: 'attach-tag-btn',
        idField: 'tag_id',
        emptyText: 'No tags attached.',
    });

    setupRelation({
        panelId: 'members-panel',
        listId: 'issue-members',
        selectId: 'member-select',
        buttonId: 'assign-member-btn',
        idField: 'user_id',
        emptyText: 'No members assigned.',
    });
})();
