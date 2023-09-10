/**
 * Shows or hides an array of elements
 * @method showElements
 * @param {array} elements An array of either strings or DOM elements.
 * @param {boolean} showEl Whether to show the elements or not.
 */
export function showElements (elements, showEl) {
    elements.forEach(
        element => {
            let el;
            if (element instanceof Element) {
                el = element;
            } else {
                el = document.getElementById(element);
                if (el === null) {
                    return;
                }
            }
            if (!showEl) {
                el.classList.add('hidden');
            } else {
                el.classList.remove('hidden');
            }
        }
    );
}

/**
 * Shows or hides an element
 * @method showElements
 * @param {String | HTMLElement} element An element or its ID.
 * @param {boolean} showEl Whether to show the element or not.
 */
export function showElement(element, showEl) {
    showElements([element], showEl);
}
