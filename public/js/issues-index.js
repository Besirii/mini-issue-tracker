/**
 * Issues index: filter (status/priority/tag) and debounced text search.
 * The server returns the rendered list partial for AJAX requests, which we
 * swap into #issues-list without reloading the page.
 */
(function () {
    const form = document.getElementById('issue-filters');
    const listContainer = document.getElementById('issues-list');
    if (!form || !listContainer) return;

    const baseUrl = form.getAttribute('data-url');
    let debounceTimer = null;

    function currentQuery() {
        const params = new URLSearchParams();
        new FormData(form).forEach((value, key) => {
            if (value !== '') params.append(key, value);
        });
        return params.toString();
    }

    async function load(url) {
        listContainer.classList.add('opacity-50');
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            listContainer.innerHTML = await response.text();
            window.history.replaceState({}, '', url);
        } catch (e) {
            listContainer.innerHTML =
                '<div class="alert alert-danger">Could not load issues. Please try again.</div>';
        } finally {
            listContainer.classList.remove('opacity-50');
        }
    }

    function reload() {
        const query = currentQuery();
        load(query ? `${baseUrl}?${query}` : baseUrl);
    }

    // Selects reload immediately.
    form.querySelectorAll('select').forEach((el) =>
        el.addEventListener('change', reload)
    );

    // Search input is debounced (300ms).
    const search = form.querySelector('input[name="search"]');
    if (search) {
        search.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(reload, 300);
        });
    }

    // Keep pagination links inside the AJAX boundary.
    listContainer.addEventListener('click', (event) => {
        const link = event.target.closest('.pagination a');
        if (!link) return;
        event.preventDefault();
        load(link.getAttribute('href'));
    });
})();
