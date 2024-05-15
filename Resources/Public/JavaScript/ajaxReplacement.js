var ready = (callback) => {
    if (document.readyState != "loading") {
        callback();
    } else {
        document.addEventListener("DOMContentLoaded", callback);
    }
}

ready(() => {
    // Bind click events to all elements with a data-psb-target attribute.
    var elements = document.querySelectorAll('[data-psb-target]');
    elements.forEach((element) => {
        element.addEventListener('click', function(event) {
            event.preventDefault();

            // XHR request to the href attribute of the clicked element
            let xhr = new XMLHttpRequest();
            xhr.open('GET', element.href, true);
            xhr.responseType = 'json';
            xhr.send();
            xhr.onload = function() {
                if (xhr.status != 200) {
                    console.error(`Error ${xhr.status}: ${xhr.statusText}`);
                } else {
                    let response = xhr.response;
                    let target = document.querySelector(element.dataset.psbTarget);

                    target.innerHTML = response.html;
                }
            }
        });
    });
});
