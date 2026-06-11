const eslint = require('@eslint/js');

module.exports = [
    eslint.configs.recommended,
    {
        files: ['Resources/Public/JavaScript/**/*.js'],
        ignores: ['Resources/Public/JavaScript/Vendors/**'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: {
                bootstrap: 'readonly',
                console: 'readonly',
                CustomEvent: 'readonly',
                document: 'readonly',
                Event: 'readonly',
                fetch: 'readonly',
                FormData: 'readonly',
                HTMLElement: 'readonly',
                Node: 'readonly',
                URLSearchParams: 'readonly',
                window: 'readonly',
                XMLHttpRequest: 'readonly',
            },
        },
    },
];
