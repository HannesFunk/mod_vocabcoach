function reveal(triggeringBox) {
    if (triggeringBox.childNodes[0].style.display === 'none') {
        triggeringBox.childNodes[0].style.display = 'unset';
    }
}

