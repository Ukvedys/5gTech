/* Keep a failed submission in the current form, without persisting personal data. */
(() => {
  const actions = new Set(['g5tech_contact', 'g5tech_application']);
  document.querySelectorAll('form').forEach((form) => {
    if (!actions.has(form.querySelector('[name="action"]')?.value)) return;
    const status = form.querySelector('.form-status');
    if (!status) return;
    const feedback = (message, error) => {
      status.textContent = message;
      status.hidden = false;
      status.classList.toggle('form-status--error', error);
      status.setAttribute('role', error ? 'alert' : 'status');
      status.focus();
      status.scrollIntoView({ block: 'center' });
    };
    if (!status.hidden) feedback(status.textContent, status.classList.contains('form-status--error'));
    if (!window.fetch || !window.FormData) return;
    let pending = false;
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      if (pending) return;
      pending = true;
      const data = new FormData(form);
      data.set('g5tech_async', '1');
      const buttons = [...form.querySelectorAll('[type="submit"]')];
      const disabled = buttons.map((button) => button.disabled);
      buttons.forEach((button) => { button.disabled = true; });
      form.setAttribute('aria-busy', 'true');
      try {
        const response = await fetch(form.action, { method: 'POST', body: data, credentials: 'same-origin', headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Submission unavailable');
        const result = await response.json();
        if (typeof result.success !== 'boolean' || !result.message) throw new Error('Invalid feedback');
        if (result.success) form.reset();
        // A refreshed nonce lets the visitor retry an expired form without losing their text.
        const nonce = form.querySelector('[name="g5tech_nonce"]');
        if (nonce && result.nonce) nonce.value = result.nonce;
        feedback(result.message, !result.success);
      } catch (_) {
        // A network failure can happen after delivery; never retry automatically.
        feedback(window.g5techFormMessages.networkError, true);
      } finally {
        pending = false;
        buttons.forEach((button, index) => { button.disabled = disabled[index]; });
        form.removeAttribute('aria-busy');
      }
    });
  });
})();
