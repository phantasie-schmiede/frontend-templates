/** @type {import("prettier").Config} */
const config = {
    plugins: ["prettier-plugin-organize-attributes", "@prettier/plugin-xml"],
    singleAttributePerLine: true,
    attributeGroups: ["$DEFAULT"],
    attributeSort: "ASC",
    tabWidth: 4,
    overrides: [
        {
            files: ["*.xml", "*.xlf"],
            options: {
                parser: "xml",
                xmlSortAttributesByKey: true
            }
        }
    ]
};

export default config;
