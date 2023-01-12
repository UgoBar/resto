import axios from "axios";

Vue.filter('toCurrency', function (value) {
    if (typeof value !== "number") {
        return value;
    }
    let formatter = new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    });
    return formatter.format(value);
});

new Vue({
    el: "#app",
    delimiters: ['${', '}'],
    data: {
        currentFilter: "TOUS",
        dishes: dishes,
        showCart: false,
        total: 0,
        cart: {},
        arrayOfPrice: {}
    },
    methods: {
        setFilter: function (filter) {
            this.currentFilter = filter;
        },
        addDishToCart: function(dish) {
            if(dish.userId) {
                axios.post(ADD_DISH_TO_CART_URL, { row: dish })
                    .then(response => {
                        if(!this.cart[dish.name]) {
                            Vue.set(this.cart, dish.name, {
                                name: dish.name,
                                id: dish.id,
                                price: dish.price,
                                quantity: 1,
                                total: (dish.price * 1),
                                tagId: 'dish-' + dish.id,
                                rowId: response.data.rowId
                            })
                        } else {
                            this.cart[dish.name].quantity ++;
                            console.log('cart', this.cart)
                        }
                        this.calculTotalRow(dish.name);
                        this.totalPrice();
                    });
            } else {
                Swal.fire({
                    title: 'Connectez-vous pour créer votre panier !',
                    confirmButtonText: `<a class="custom-btn custom-btn-yellow custom-btn-small text-uppercase" href="${LOGIN_URL}">Se connecter</a>`,
                })
            }

        },
        // CALCULATE TOTAL ROW
        calculTotalRow: function(name) {
            this.cart[name].total   = this.cart[name].quantity * this.cart[name].price;
            this.arrayOfPrice[name] = this.cart[name].total
        },
        // CALCULATE TOTAL PRICE
        totalPrice: function() {
            if(Object.keys(this.arrayOfPrice).length) {
                this.total = Object.values(this.arrayOfPrice).reduce((prev, curr) => prev + curr)
            } else {
                this.total = 0
            }
        },
        // TOGGLE ASIDE CART MENU
        toggleCart: function () {
            const aside = document.querySelector('aside');
            this.showCart = !this.showCart
            this.showCart ? aside.style.right = 0 : aside.style.right = "-575px";
        },
        // ADD ITEM TO CART
        addItem: function(name) {
            this.cart[name].quantity ++;
            axios.post(ADD_DISH_TO_CART_URL, { row: this.cart[name] }).then(response => {
                this.calculTotalRow(name);
                this.totalPrice();
            });
        },
        // REMOVE ITEM FROM CART
        removeItem: function(name) {

            this.cart[name].quantity --;

            if (this.cart[name].quantity) {
                // Remove one quantity from row and update total cart
                axios.post(REMOVE_FROM_CART_URL, { row: this.cart[name], deleteRow: false }).then(response => {
                    this.calculTotalRow(name);
                });
            } else {
                // Remove one row and update total cart
                axios.post(REMOVE_FROM_CART_URL, { row: this.cart[name], deleteRow: true }).then(response => {
                    if(response.data.success) {
                        // do stuff
                    }
                });
                delete this.cart[name];
                delete this.arrayOfPrice[name];
            }
            this.totalPrice();
        },
        filterProducts: function(elem, category, bg) {
            const bgActive    = document.querySelector('.navbar-bg.active');
            const bgNotActive = document.querySelector('.navbar-bg:not(.active)')
            const asideActive = document.querySelector('.aside .aside-link.active');

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

                }, 300);

                // Navbar Background
                bgNotActive.classList.add('active');
                bgNotActive.classList.remove('translateY-100');
                bgNotActive.style.backgroundImage = `url('${bg}')`;
                setTimeout(() =>{
                    bgActive.classList.add('translateY-100');
                    bgActive.classList.remove('active');
                }, 300);
            }
        }
    }
});
