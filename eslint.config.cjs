const eslint = require('@eslint/js');

module.exports = [
    {
        ignores: ['Resources/Public/JavaScript/Vendors/**'],
    },
    eslint.configs.recommended,
    {
        files: ['Resources/Public/JavaScript/**/*.js'],
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
                clearTimeout: 'readonly',
                setTimeout: 'readonly',
            },
        },
        rules: {
            'no-undef': 'off',
            'no-unused-vars': 'off',
        },
    },
];
