let showAdminMenu = false;
const dropdownLink = document.querySelector('.nav-item.dropdown');
const dropdown = document.querySelector('.dropdown-menu');

if(dropdownLink) {
    dropdownLink.addEventListener('click', (event) => {
        showAdminMenu = !showAdminMenu;
        showAdminMenu ? dropdown.classList.add('show') : dropdown.classList.remove('show');
    });
}

let showBurgerMenu = false;
const navbarToggler = document.querySelector('.navbar-toggler');
const navbarResponsive = document.querySelector('#navbarResponsive');

if(navbarToggler) {
    navbarToggler.addEventListener('click', (event) => {
        showBurgerMenu = !showBurgerMenu;
        showBurgerMenu ? navbarResponsive.classList.add('show') : navbarResponsive.classList.remove('show');
    });
}
