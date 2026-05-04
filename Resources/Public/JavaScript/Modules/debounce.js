/*
 * This function serves as an intermediary between event triggering and the execution of a callback. It ensures that
 * the callback is only invoked after a specified delay has elapsed since the last event, which helps to optimize
 * performance for frequently fired events like scroll or input.
 */
function debounce(fn, delay) {
    let timeout;

    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            fn.apply(this, args);
        }, delay);
    };
}

export default debounce;
