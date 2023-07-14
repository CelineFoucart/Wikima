function enableSyncInput(inputSelector, targetSelector) {
    const input = document.querySelector(inputSelector);
    input.addEventListener('input', (e) => {
        const target = document.querySelector(targetSelector);
        target.innerHTML = input.value;
    })
}

function enableDatabaseHelper() {
    enableSyncInput('#installation_DB_NAME', '#dbname');
    enableSyncInput('#installation_DB_USER', '#user');
    enableSyncInput('#installation_DB_PASSWORD', '#password');
    enableSyncInput('#installation_DB_HOST', '#host');
    enableSyncInput('#installation_DB_PORT', '#port');
    enableSyncInput('#installation_serverVersion', '#version');
}
