document.addEventListener('DOMContentLoaded', function () {
    // === DROPDOWN SIDEBAR ===
    var dropdown = document.getElementsByClassName("dropdown-btn");

    function saveDropdownState(index, isOpen) {
        localStorage.setItem('dropdownState_' + index, isOpen ? 'open' : 'closed');
    }

    function loadDropdownState(index) {
        return localStorage.getItem('dropdownState_' + index) === 'open';
    }

    for (var i = 0; i < dropdown.length; i++) {
        var dropdownContent = dropdown[i].nextElementSibling;

        // Buka dropdown jika sebelumnya tersimpan open
        if (dropdownContent && loadDropdownState(i)) {
            dropdownContent.style.display = "block";

            var caret = dropdown[i].querySelector('.fa-caret-down');
            if (caret) {
                caret.classList.add('rotate');
            }
        }

        dropdown[i].addEventListener("click", function (index) {
            return function () {
                var content = this.nextElementSibling;
                var caret = this.querySelector('.fa-caret-down');

                if (!content) return;

                if (content.style.display === "block") {
                    content.style.display = "none";

                    if (caret) {
                        caret.classList.remove('rotate');
                    }

                    saveDropdownState(index, false);
                } else {
                    content.style.display = "block";

                    if (caret) {
                        caret.classList.add('rotate');
                    }

                    saveDropdownState(index, true);
                }
            };
        }(i));
    }

    // === TOGGLE SIDEBAR ===
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.querySelector('.sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('toggled');

            localStorage.setItem(
                'sidebarToggled',
                sidebar.classList.contains('toggled') ? 'true' : 'false'
            );
        });

        if (localStorage.getItem('sidebarToggled') === 'true') {
            sidebar.classList.add('toggled');
        }
    }
});