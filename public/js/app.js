/**
 * Small global helper shared by the AJAX pages.
 * Exposes window.IssueTracker.request(url, options).
 */
(function () {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

    /**
     * JSON fetch wrapper that attaches the CSRF token and the
     * X-Requested-With header so Laravel treats it as AJAX.
     *
     * Resolves with { ok, status, data }. For 422 responses, data.errors
     * holds the validation messages.
     */
    async function request(url, { method = 'GET', body = null } = {}) {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
        };

        const options = { method, headers };

        if (body !== null) {
            headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }

        const response = await fetch(url, options);
        let data = null;
        try {
            data = await response.json();
        } catch (e) {
            data = null;
        }

        return { ok: response.ok, status: response.status, data };
    }

    window.IssueTracker = { request, csrf };
})();
