let showAdminMenu = false;
const dropdownLink = document.querySelector('.nav-item.dropdown');
const dropdown = document.querySelector('.dropdown-menu');

if(dropdownLink) {
    dropdownLink.addEventListener('click', (event) => {
        showAdminMenu = !showAdminMenu;
        showAdminMenu ? dropdown.classList.add('show') : dropdown.classList.remove('show');
    });
}

