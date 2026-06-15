/** @type {import("prettier").Config} */
const config = {
    plugins: ["@prettier/plugin-xml"],
    tabWidth: 4,
    overrides: [
        {
            files: ["*.xml", "*.xlf"],
            options: {
                parser: "xml"
            }
        }
    ]
};

export default config;
