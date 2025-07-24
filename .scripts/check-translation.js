import fs from 'fs';
import path from 'path';
import { exit } from 'process';
import { fileURLToPath } from 'url';
import { execSync } from 'child_process';

// Resolve __dirname for ESM
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Paths
const SRC_DIR = path.join(__dirname, '../');
const LANG_FILE = path.join(__dirname, '../lang/id.json');
const OUTPUT_FILE = path.join(__dirname, '../.missing-translation.json');

console.log('check-translation-frontend: 🔍 Scanning for missing frontend translations...');

// Load translation keys
let translationKeys;
try {
	const translations = JSON.parse(fs.readFileSync(LANG_FILE, 'utf8'));
	translationKeys = new Set(Object.keys(translations));
} catch (err) {
	console.error('Error loading translation file:', err.message);
	exit(1);
}

// Get staged files only (git diff --cached)
const stagedFiles = execSync('git diff --cached --name-only', { encoding: 'utf8' })
	.split('\n')
	.map((f) => f.trim())
	.filter((f) => f && (f.endsWith('.js') || f.endsWith('.vue')) && fs.existsSync(path.join(SRC_DIR, f)));

// Extract translation keys
function extractTranslationKeys(content) {
	const regex = /[\s.\n+]\$?t\(\s*['"`](?!.*\+)(.+?)['"`]\s*\)/g;
	const keys = [];
	let match;
	while ((match = regex.exec(content)) !== null) {
		keys.push(match[1]);
	}
	return keys;
}

// Find used translation keys in staged files
console.log(`check-translation-frontend: 🔎 Checking ${stagedFiles.length} staged file(s)...`);
const usedKeys = new Set();
stagedFiles.forEach((filePath) => {
	const absolutePath = path.join(SRC_DIR, filePath);
	const content = fs.readFileSync(absolutePath, 'utf8');
	const keys = extractTranslationKeys(content);
	keys.forEach((key) => usedKeys.add(key));
});

// Compare with existing translations
const missingKeys = [...usedKeys].filter((key) => !translationKeys.has(key));

// Write missing translation keys
const missingTranslations = {};
missingKeys.forEach((key) => {
	missingTranslations[key] = null;
});
fs.writeFileSync(OUTPUT_FILE, JSON.stringify(missingTranslations, null, 2), 'utf8');

if (missingKeys.length > 0) {
	console.error('check-translation-frontend: ❌ Missing translations found in staged files.');
	console.info(`check-translation-frontend: Results saved to ${OUTPUT_FILE}`);
	exit(1);
} else {
	console.log('check-translation-frontend: ✅ No missing frontend translations in staged files.\n');
}

// Backend PHP translation check (optional but unchanged)
console.log('check-translation-backend: 🔍 Scanning for missing backend translations...');
execSync('php artisan translations:find-missing --print --source=id > storage/logs/missing-translation.log');

const translationLogFile = fs.readFileSync('storage/logs/missing-translation.log', 'utf8');
const missingPhpTranslations = translationLogFile
	.split('\n')
	.filter((line) => line.trim() !== '' && !line.includes('Finding translations...'))
	.map((line) => line.trim().replace(/:$/, ''));

if (missingPhpTranslations.length > 0) {
	const missing = {};
	missingPhpTranslations.forEach((key) => {
		missing[key] = null;
	});
	fs.writeFileSync(OUTPUT_FILE, JSON.stringify(missing, null, 2), 'utf8');
	console.error('check-translation-backend: ❌ Missing backend translations found.');
	console.info(`check-translation-backend: Results saved to ${OUTPUT_FILE}`);
	console.info(`Run: "php artisan translations:check id --translate-missing" to update.`);
	exit(1);
}
