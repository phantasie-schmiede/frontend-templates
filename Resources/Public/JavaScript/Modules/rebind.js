/*
 * This class can be used to register and re-execute functions that attach events to elements. This is useful for
 * dynamically inserted elements that also need to have events attached to them.
 * To use this class, import the instance exported by this module and register the functions that bind events to
 * elements using the registerBindFunction method. Then, call the rebindEvents method after inserting new elements to
 * attach the events to those elements.
 *
 * IMPORTANT:
 * This class does NOT keep track of the elements that events are attached to. It is the responsibility of the
 * registered functions to ensure that events are not attached multiple times to the same element.
 * Therefore, it is recommended to use a similar approach as in the other scripts of this package and keep track of the
 * registered elements in the bind functions themselves.
 */
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
