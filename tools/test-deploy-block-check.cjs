// Exercise the actual deploy.yml validation, without npm, SSH or deployment.
const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const { spawnSync } = require('node:child_process');

const root = path.resolve(__dirname, '..');
const plugin = path.join(root, 'wordpress/wp-content/plugins/5gtech-core');
const yaml = fs.readFileSync(path.join(root, '.github/workflows/deploy.yml'), 'utf8');
const step = yaml.match(/      - name: Sukompiliuoti blokus\n[\s\S]*?        run: \|\n([\s\S]*?)(?=\n      - name:)/);
assert(step, 'Build step must exist');
const body = step[1].replace(/^ {10}/gm, '');
const marker = 'npm run build\n';
assert(body.includes(marker), 'Build command must precede validation');
const validation = body.slice(body.indexOf(marker) + marker.length);
assert(!/\b(?:npm|ssh|rsync|rm)\b/.test(validation), 'Only execute the post-build validator');
assert.equal(spawnSync('bash', ['-n'], { input: body }).status, 0, 'Build shell syntax');

const temporary = fs.mkdtempSync(path.join(os.tmpdir(), '5gtech-block-validator-'));
try {
  fs.cpSync(path.join(plugin, 'blocks-src'), path.join(temporary, 'blocks-src'), { recursive: true });
  fs.cpSync(path.resolve(process.argv[2] || path.join(plugin, 'build')), path.join(temporary, 'build'), { recursive: true });
  const run = () => spawnSync('bash', ['-e', '-c', validation], { cwd: temporary, encoding: 'utf8' });
  const names = fs.readdirSync(path.join(temporary, 'blocks-src')).filter(name => fs.existsSync(path.join(temporary, 'blocks-src', name, 'block.json')));
  assert(names.length > 0);
  assert(fs.existsSync(path.join(temporary, 'blocks-src/shared/admin-link.js')));
  assert(!fs.existsSync(path.join(temporary, 'build/shared')), 'Fixture must reproduce the shared directory condition');
  const success = run();
  assert.equal(success.status, 0, success.stdout + success.stderr);
  assert(success.stdout.includes(`Sukompiliuota blokų: ${names.length}`));

  for (const filename of ['block.json', 'index.js', 'index.asset.php']) {
    const target = path.join(temporary, 'build', names[0], filename);
    fs.renameSync(target, target + '.saved');
    const failure = run();
    assert.equal(failure.status, 1, `Missing ${filename} must fail`);
    assert(failure.stdout.includes(`${names[0]}/${filename}`));
    fs.renameSync(target + '.saved', target);
  }
  const emptyAsset = path.join(temporary, 'build', names[0], 'index.js');
  fs.writeFileSync(emptyAsset, '');
  assert.equal(run().status, 1, 'Empty compiled asset must fail');
  fs.renameSync(path.join(temporary, 'blocks-src'), path.join(temporary, 'sources-saved'));
  fs.mkdirSync(path.join(temporary, 'blocks-src/shared'), { recursive: true });
  const empty = run();
  assert.equal(empty.status, 1, 'No registered blocks must fail');
  assert(empty.stdout.includes('Nerasta blokų aprašų'));
  console.log(`PASS: ${names.length} real blocks; shared ignored; missing/empty artifacts and absent metadata rejected.`);
} finally {
  fs.rmSync(temporary, { recursive: true, force: true });
}
