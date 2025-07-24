import { execSync } from 'child_process';

console.log('🔄 Checking if your branch is up-to-date with origin/development...');

try {
	execSync('git fetch origin master');
	const mergeBase = execSync('git merge-base HEAD origin/development').toString().trim();
	const masterHead = execSync('git rev-parse origin/development').toString().trim();

	if (mergeBase !== masterHead) {
		console.log('❌ Your branch is behind development. Please run: git pull --rebase origin development');
		process.exit(1);
	} else {
		console.log('✅ Your branch is up-to-date with development.');
	}
} catch (error) {
	console.error('Error checking branch status:', error.message);
	process.exit(1);
}
