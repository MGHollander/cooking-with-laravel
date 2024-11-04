function strikeText(element) {
  let target = element;
  // Traverse up the DOM tree, till the first listitem is found.
  while (target && target.parentElement) {
    const listItem = target;
    if (listItem.nodeName === 'LI') {
      listItem.querySelector('.strike-animation').classList.toggle('striked');
      break;
    }
    target = listItem.parentElement;
  }
}
