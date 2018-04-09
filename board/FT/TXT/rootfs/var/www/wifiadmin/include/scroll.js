function scrollElementToEnd (element) {
	if (typeof element.scrollTop != 'undefined' &&
	typeof element.scrollHeight != 'undefined') {
		element.scrollTop = element.scrollHeight;
	}
}

function scrollElementToEndN (elementn) {
	element = document.getElementById(elementn);
	if (element) {
		scrollElementToEnd (element);
	}
}
