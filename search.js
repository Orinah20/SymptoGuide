function filterUserData() {
    var input = document.getElementById('searchInput').value.toLowerCase();
    var tableRows = document.querySelectorAll('#userData tr');

    for (var i = 0; i < tableRows.length; i++) {
        var row = tableRows[i];
        var rowData = row.innerHTML.toLowerCase();

        if (rowData.indexOf(input) > -1) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }

}

// Add an event listener for the input event
document.getElementById('searchInput').addEventListener('input', filterUserData);

function filterUserTableData() {
    var input = document.getElementById('searchInput').value.toLowerCase();
    var tableRows = document.querySelectorAll('#userData tr');
    var displayLimit = parseInt(document.getElementById('displayLimit').value);

    for (var i = 0; i < tableRows.length; i++) {
        var row = tableRows[i];
        var rowData = row.innerHTML.toLowerCase();

        if (rowData.indexOf(input) > -1 && i < displayLimit) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// Add an event listener for the input event
document.getElementById('searchInput').addEventListener('input', filterUserTableData);
