#!/usr/bin/env node
import fs from 'fs';
import { execSync } from 'child_process';

// Configuration
const TARGET_FUNCTION = 'console';
const IGNORE_FILE = '.gitignore';
const HARDCODED_IGNORES = [
	'.github/',
	'.husky/',
	'.scripts/',
	'.vscode/',
	'composer.json',
	'composer.lock',
	'node_modules/',
	'package.json',
	'public/css/',
	'public/js/',
	'storage/framework/',
	'tests/',
	'vendor/',
	'yarn.lock',
];

console.log(`check-console: 🔍 Scanning for ${TARGET_FUNCTION} calls...`);

// Get ignore patterns from .gitignore + hardcoded patterns
function getIgnorePatterns() {
	const patterns = [...HARDCODED_IGNORES];

	if (fs.existsSync(IGNORE_FILE)) {
		const gitignore = fs
			.readFileSync(IGNORE_FILE, 'utf8')
			.split('\n')
			.map((line) => line.trim())
			.filter((line) => line && !line.startsWith('#'));

		patterns.push(...gitignore);
	}

	return patterns;
}

// Check if file should be ignored
function shouldIgnore(filePath, patterns) {
	return patterns.some((pattern) => {
		// Handle directory patterns
		if (pattern.endsWith('/')) {
			return filePath.startsWith(pattern) || filePath.includes(`/${pattern}`);
		}

		// Handle wildcards
		if (pattern.includes('*')) {
			const regex = new RegExp(pattern.replace(/\./g, '\\.').replace(/\*/g, '.*'));
			return regex.test(filePath);
		}

		// Exact match
		return filePath === pattern || filePath.endsWith(`/${pattern}`);
	});
}

// Main check function
function checkForDebugFunctions() {
	const ignorePatterns = getIgnorePatterns();
	const changedFiles = execSync('git diff --cached --name-only --diff-filter=ACMR', { encoding: 'utf8' })
		.split('\n')
		.filter(Boolean);
	console.log(`check-console: 🔎 Checking ${changedFiles.length} staged file(s)...`);
	let foundIssues = false;

	changedFiles.forEach((file) => {
		if (!file.endsWith('.js') && !file.endsWith('.vue')) return;

		if (shouldIgnore(file, ignorePatterns)) {
			return;
		}

		if (!fs.existsSync(file)) return;

		const content = fs.readFileSync(file, 'utf8');
		const lines = content.split('\n');

		lines.forEach((line, i) => {
			if (line.includes(TARGET_FUNCTION + '.')) {
				console.log(`🚫 Found ${TARGET_FUNCTION} function in ${file}:${i + 1}`);
				console.log(`   ${line.trim()}`);
				foundIssues = true;
			}
		});
	});

	return foundIssues;
}

// Execute
if (checkForDebugFunctions()) {
	console.log(`\n❌ Please remove all ${TARGET_FUNCTION} calls before committing.`);
	process.exit(1);
} else {
	console.log(`check-console: ✅ No ${TARGET_FUNCTION} calls found in staged files.\n`);
	// process.exit(0);
}
