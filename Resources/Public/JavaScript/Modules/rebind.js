class Rebind {
    constructor() {
        this.bindFunctions = [];
    }

    rebindEvents() {
        this.bindFunctions.forEach((bindFunction) => {
            bindFunction();
        });
    }

    registerBindFunction(bindFunction) {
        this.bindFunctions.push(bindFunction);
    }
}

export default new Rebind();
