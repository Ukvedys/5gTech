/* Read-only regression: actual installed WordPress JS + compiled production blocks.
 * Run from any directory: node tools/test-block-serialization.cjs
 * Requires the plugin's installed build dependencies; never saves a WordPress post.
 */
const fs = require('fs');
const path = require('path');
const cp = require('child_process');
const vm = require('vm');
const assert = require('assert/strict');
const root = path.resolve(__dirname, '..');
const plugin = path.join(root, 'wordpress/wp-content/plugins/5gtech-core');
function loadJsdom() {
  try { return require(require.resolve('jsdom', { paths: [plugin] })); }
  catch (_) {
    const store = path.join(plugin, 'node_modules/.pnpm');
    const installed = fs.readdirSync(store).find((name) => name.startsWith('jsdom@'));
    if (!installed) throw new Error('Install the plugin build dependencies first.');
    return require(path.join(store, installed, 'node_modules/jsdom'));
  }
}
const { JSDOM } = loadJsdom();
const dom = new JSDOM('<!doctype html><html><body></body></html>', { url: 'http://5gtech.test', runScripts: 'outside-only', pretendToBeVisual: true });
const w = dom.window;
Object.assign(w, { TextEncoder, TextDecoder, Request, Response, Headers });
w.matchMedia = () => ({ matches: false, addListener() {}, removeListener() {}, addEventListener() {}, removeEventListener() {} });
const run = (code) => vm.runInContext(code, dom.getInternalVMContext());
const php = '$_SERVER["HTTP_HOST"]="5gtech.test";$_SERVER["REQUEST_URI"]="/";require "wordpress/wp-load.php";$s=wp_scripts();$done=[];$walk=function($name)use(&$walk,&$done,$s){if(isset($done[$name]))return;$item=$s->registered[$name]??null;if(!$item)return;foreach($item->deps as $dep)$walk($dep);$done[$name]=$item->src;};foreach(["wp-block-editor","wp-server-side-render","wp-block-library"]as$handle)$walk($handle);echo json_encode($done);';
let checks = 0;
try {
  const scripts = JSON.parse(cp.execFileSync('php', ['-r', php], { cwd: root, encoding: 'utf8' }));
  for (const [handle, src] of Object.entries(scripts)) {
    if (!src) continue;
    try { run(fs.readFileSync(path.join(root, 'wordpress', src), 'utf8')); }
    catch (error) { throw new Error(handle + ': ' + error.message); }
  }
  w.wp.blockLibrary.registerCoreBlocks();
  const names = fs.readdirSync(path.join(plugin, 'build')).filter((name) => fs.existsSync(path.join(plugin, 'build', name, 'block.json')));
  const definitions = {};
  for (const name of names) {
    const meta = JSON.parse(fs.readFileSync(path.join(plugin, 'build', name, 'block.json')));
    definitions[meta.name] = meta;
  }
  w.wp.blocks.unstable__bootstrapServerSideBlockDefinitions(definitions);
  for (const name of names) run(fs.readFileSync(path.join(plugin, 'build', name, 'index.js'), 'utf8'));
  const { createBlock, parse, serialize } = w.wp.blocks;
  const shape = (blocks) => JSON.parse(JSON.stringify(blocks.map((b) => ({ name: b.name, attributes: b.attributes, children: shape(b.innerBlocks) }))));
  const roundTrip = (blocks, label) => {
    const result = parse(serialize(blocks));
    assert.deepEqual(shape(result), shape(blocks), label);
    checks++;
    return result;
  };
  const parents = ['section', 'card-grid', 'home-hero', 'steps', 'check-list', 'home-audiences', 'home-sections', 'about-story', 'about-values', 'about-strategy', 'about-competence'];
  const children = ['card-grid', 'card', 'hero-slide', 'step', 'check-item', 'audience-item', 'card', 'labeled-item', 'labeled-item', 'labeled-item', 'labeled-item'];
  parents.forEach((parent, index) => {
    const child = createBlock('g5tech/' + children[index], { title: 'Žą & "quote"', text: 'Saved child', imageId: 123 });
    roundTrip([createBlock('g5tech/' + parent, {}, [child])], parent);
  });
  roundTrip([createBlock('g5tech/section', {}, [createBlock('g5tech/card-grid', {}, [createBlock('g5tech/card', { title: 'Three levels' })])])], 'Deep nesting');
  // Live published page content is read, not modified. Test all block-based pages/languages.
  const pages = JSON.parse(cp.execFileSync('php', ['-r', '$_SERVER["HTTP_HOST"]="5gtech.test";$_SERVER["REQUEST_URI"]="/";require "wordpress/wp-load.php";echo json_encode(array_map(fn($p)=>["id"=>$p->ID,"content"=>$p->post_content],get_posts(["post_type"=>"page","post_status"=>"publish","numberposts"=>-1,"suppress_filters"=>true,"lang"=>""])));'], { cwd: root, encoding: 'utf8', maxBuffer: 10 * 1024 * 1024 }));
  let pagesChecked = 0;
  for (const page of pages) {
    if (!page.content.includes('wp:g5tech/')) continue;
    roundTrip(parse(page.content), 'Published page ' + page.id);
    pagesChecked++;
  }
  console.log(`PASS: ${checks} serialization round-trips (${parents.length} parent types, deep nesting, ${pagesChecked} published pages).`);
} finally { dom.window.close(); }
