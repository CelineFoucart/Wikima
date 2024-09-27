/**
 * Display a tab body from the get params in the url.
 * 
 * @returns
 */
export function getTabTargetFromUrl() {
    const search = window.location.search.substring(1);

    if (search.length > 1) {
        const parameters = search.substring(1).split('=');
        if (parameters[0] !== "tab") {
            return;
        }
        const id = parameters[1];

        const target = document.querySelector(`#${id}`);

        if (target) {
            document.querySelectorAll('.active').forEach(item => {
                item.classList.remove('active');
                item.classList.remove('in');
            });

            target.classList.add("active");
            target.classList.add("in");
            const tabs = document.querySelector(`a[href="#${id}"]`).parentElement;
            tabs.classList.add('active');
        }
    }
}
