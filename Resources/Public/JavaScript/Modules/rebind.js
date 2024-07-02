class Rebind {
    constructor() {
        this.bindFunctions = [];
    }

    rebindEvents(rootNode = null) {
        this.bindFunctions.forEach((bindFunction) => {
            bindFunction(rootNode);
        });
    }

    registerBindFunction(bindFunction) {
        this.bindFunctions.push(bindFunction);
    }
}

export default new Rebind();
