/**
  * Process a search in the elements of class .search-item in a container with the given selector
  * 
  * @param {string} inputSelector    the selector of an input
  * @param {string} targetSelector   the search container selector
  * 
  * @return void
  */
export default function searchInput(inputSelector, targetSelector) {

    const input = document.querySelector(inputSelector);
    if (null === input) {
        console.log(`Element ${inputSelector} not found`);
        return;
    }

    const target = document.querySelector(targetSelector);
    if (null === target) {
        console.log(`Element ${targetSelector} not found`);
        return;
    }

    input.addEventListener('input', (e) => {
        e.preventDefault();
        const value = input.value;
        const searchables = target.querySelectorAll('.search-item');
        searchables.forEach(element => {
            const text = element.innerText.toLowerCase();
            if (text.includes(value.toLowerCase())) {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        });
    })
}