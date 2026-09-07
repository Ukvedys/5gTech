/* DOM regression tests only. Fetch is stubbed: no email or HTTP requests. */
const fs = require('fs');
const path = require('path');
const assert = require('assert/strict');
const root = path.resolve(__dirname, '..');
const plugin = path.join(root, 'wordpress/wp-content/plugins/5gtech-core');
let jsdom;
try { jsdom = require(require.resolve('jsdom', { paths: [plugin] })); }
catch (_) {
  const store = path.join(plugin, 'node_modules/.pnpm');
  jsdom = require(path.join(store, fs.readdirSync(store).find((name) => name.startsWith('jsdom@')), 'node_modules/jsdom'));
}
const source = fs.readFileSync(path.join(plugin, 'assets/form-feedback.js'), 'utf8');
const tick = () => new Promise((resolve) => setTimeout(resolve, 0));
(async () => {
  let checks = 0;
  for (const action of ['g5tech_contact', 'g5tech_application']) {
    const dom = new jsdom.JSDOM(`<!doctype html><form action="http://5gtech.test/wp-admin/admin-post.php"><input type="hidden" name="action" value="${action}"><input name="g5tech_nonce" value="old"><input name="name"><textarea name="message"></textarea><input type="file" name="cv"><button type="submit">Send</button><p class="form-status" tabindex="-1" hidden></p></form>`, { url: 'http://5gtech.test', runScripts: 'outside-only' });
    const w = dom.window;
    w.HTMLElement.prototype.scrollIntoView = function () {};
    w.g5techFormMessages = { networkError: 'Delivery could not be confirmed' };
    let requests = 0;
    let nextResult = { success: false, message: 'Fix the email address', nonce: 'fresh' };
    let networkError = false;
    w.fetch = async (url, options) => {
      requests++;
      assert.equal(url, 'http://5gtech.test/wp-admin/admin-post.php');
      assert.equal(options.body.get('g5tech_async'), '1');
      assert.equal(options.body.get('message'), 'Keep my project brief');
      if (networkError) throw new Error('Offline');
      return { ok: true, json: async () => nextResult };
    };
    try {
      w.eval(source);
      const form = w.document.querySelector('form');
      const message = form.elements.message;
      const status = form.querySelector('.form-status');
      const button = form.querySelector('button');
      message.value = 'Keep my project brief';
      form.elements.name.value = 'Test';
      const submit = () => form.dispatchEvent(new w.Event('submit', { bubbles: true, cancelable: true }));
      submit(); submit();
      assert.equal(button.disabled, true); checks++;
      await tick();
      assert.equal(requests, 1); checks++;
      assert.equal(message.value, 'Keep my project brief'); checks++;
      assert.equal(form.elements.g5tech_nonce.value, 'fresh'); checks++;
      assert.equal(w.document.activeElement, status); checks++;
      assert.equal(status.getAttribute('role'), 'alert'); checks++;
      assert.equal(status.hidden, false); checks++;
      assert.equal(button.disabled, false); checks++;
      networkError = true;
      submit(); await tick();
      assert.equal(message.value, 'Keep my project brief'); checks++;
      assert.equal(status.textContent, w.g5techFormMessages.networkError); checks++;
      networkError = false;
      nextResult = { success: true, message: 'Sent', nonce: 'fresh-2' };
      submit(); await tick();
      assert.equal(message.value, ''); checks++;
      assert.equal(form.elements.name.value, ''); checks++;
      assert.equal(status.getAttribute('role'), 'status'); checks++;
      assert.equal(form.hasAttribute('aria-busy'), false); checks++;
    } finally { w.close(); }
  }
  console.log(`PASS: ${checks} form feedback checks; no requests sent.`);
})().catch((error) => { console.error(error); process.exitCode = 1; });
