
const filterProducts = (elem, category, bg) => {

    const bgActive    = document.querySelector('.navbar-bg.active');
    const bgNotActive = document.querySelector('.navbar-bg:not(.active)')
    const asideActive = document.querySelector('.aside .aside-link.active');

    console.log(bgActive, bgNotActive, asideActive)

    if(elem !== asideActive) {

        // Category Title
        let titlePageNotActive = bgNotActive.querySelector('.title-page');
        let titlePagetActive   = bgActive.querySelector('.title-page');
        titlePagetActive.classList.add('opacity-0');
        titlePagetActive.classList.add('send-up');
        setTimeout(() =>{
            // Category Title
            titlePageNotActive.innerHTML = category
            titlePageNotActive.classList.remove('opacity-0')
            titlePageNotActive.classList.remove('send-up');

        }, 600);

        // Navbar Background
        bgNotActive.classList.add('active');
        bgNotActive.classList.remove('translateY-100');
        bgNotActive.style.backgroundImage = `url('${bg}')`;
        setTimeout(() =>{
            bgActive.classList.add('translateY-100');
            bgActive.classList.remove('active');
        }, 300);

        // Category link
        asideActive.classList.remove('active');
        elem.classList.add('active');

    }
}
