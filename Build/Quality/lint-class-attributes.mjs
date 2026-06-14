#!/usr/bin/env node

import { readdir, readFile, writeFile } from 'node:fs/promises';
import path from 'node:path';

const fileExtension = '.html';
const dryRunMode = process.argv.includes('--dry-run');
const repositoryRoot = process.cwd();
const targets = ['Resources/Private', 'ContentBlocks'];

const classAttributePattern = /(\bclass\s*=\s*)(["'])([^"']*)(\2)/g;
const fluidExpressionPattern = /[{}]/;

const lintErrors = [];

const compareClassNames = (left, right) =>
    left.localeCompare(right, 'en', {
        numeric: true,
        sensitivity: 'base',
    });

const collectFiles = async (directory) => {
    const entries = await readdir(directory, { withFileTypes: true });
    const files = [];

    for (const entry of entries) {
        const absolutePath = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            files.push(...(await collectFiles(absolutePath)));
            continue;
        }

        if (entry.isFile() && absolutePath.endsWith(fileExtension)) {
            files.push(absolutePath);
        }
    }

    return files;
};

const normalizeClassAttribute = (value) => {
    const classes = value.trim().split(/\s+/).filter(Boolean);
    const sortedClasses = [...classes].sort(compareClassNames);

    return {
        changed: classes.join(' ') !== sortedClasses.join(' '),
        sorted: sortedClasses.join(' '),
    };
};

const lintFile = async (filePath) => {
    const originalContent = await readFile(filePath, 'utf8');
    let updatedContent = originalContent;
    let foundIssue = false;

    updatedContent = updatedContent.replace(
        classAttributePattern,
        (match, prefix, quote, classValue) => {
            if (fluidExpressionPattern.test(classValue)) {
                return match;
            }

            const normalized = normalizeClassAttribute(classValue);

            if (!normalized.changed) {
                return match;
            }

            foundIssue = true;

            return `${prefix}${quote}${normalized.sorted}${quote}`;
        },
    );

    if (!foundIssue) {
        return;
    }

    const relativePath = path.relative(repositoryRoot, filePath);

    if (!dryRunMode) {
        await writeFile(filePath, updatedContent, 'utf8');
        console.log(`Fixed class order in ${relativePath}`);
        return;
    }

    lintErrors.push(relativePath);
};

const run = async () => {
    const files = [];

    for (const target of targets) {
        const absoluteTarget = path.join(repositoryRoot, target);
        files.push(...(await collectFiles(absoluteTarget)));
    }

    for (const filePath of files) {
        await lintFile(filePath);
    }

    if (0 === lintErrors.length) {
        return;
    }

    console.error('Class attributes are not sorted alphabetically in:');
    for (const lintError of lintErrors) {
        console.error(`- ${lintError}`);
    }
    console.error('Run `npm run fix:classes` to fix class order.');
    process.exitCode = 1;
};

await run();
