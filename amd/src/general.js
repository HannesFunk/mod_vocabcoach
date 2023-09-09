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

export function showElement(element, showEl) {
    showElements([element], showEl);
}
