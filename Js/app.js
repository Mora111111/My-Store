document.addEventListener("DOMContentLoaded", function() {
    const profileBtn = document.getElementById('profile-btn');
    const profileMenu = document.getElementById('profile-menu');
    
    if(profileBtn && profileMenu) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if(profileMenu.style.display === 'none' || profileMenu.style.display === '') {
                profileMenu.style.display = 'block';
            } else {
                profileMenu.style.display = 'none';
            }
        });
        
        document.addEventListener('click', function(e) {
            if(!profileMenu.contains(e.target) && !profileBtn.contains(e.target)) {
                profileMenu.style.display = 'none';
            }
        });
    }
});

const cartIcon = document.querySelector("#cart-shop");
const cart = document.querySelector(".cart");
const cartClose = document.querySelector("#cart-close");
const addCartBtn = document.querySelectorAll(".card_btn");
const cartContent = document.querySelector(".cart_content");
const total = document.querySelector(".total");
const btnBuy = document.querySelector(".btn_buy");
const cartEmpty = document.querySelector(".cart_empty");

if (cartIcon && cart) {
    cartIcon.addEventListener("click", () => cart.classList.add("showCart"));
}

if (cartClose && cart) {
    cartClose.addEventListener("click", () => cart.classList.remove("showCart"));
}

if (cartEmpty && cart) {
    cartEmpty.addEventListener("click", () => {
        cart.classList.remove("showCart");
    });
}

let arrayOfCards = [];
if (localStorage.getItem("cards")) {
    arrayOfCards = JSON.parse(localStorage.getItem("cards"));
}

if (cartContent && total && btnBuy && cartEmpty) {
    getData();
    if (cartContent.children.length !== 0) {
        total.style.display = "flex";
        btnBuy.style.display = "block";
        cartEmpty.style.display = "none";
    }
    
    cartContent.addEventListener("click", (e) => {
        if (e.target.classList.contains("cart_remove")) {
            e.target.parentElement.parentElement.remove();
            deleteCardWith(
                e.target.parentElement.parentElement.getAttribute("data-id")
            );
            addCartBtn.forEach((btn) => {
                if (
                    btn.parentElement.querySelector(".card_title") && 
                    e.target.parentElement.querySelector(".cart_product_title") &&
                    btn.parentElement.querySelector(".card_title").textContent ===
                    e.target.parentElement.querySelector(".cart_product_title").textContent
                ) {
                    btn.classList.remove("done");
                    btn.textContent = "اضافة إلى العربة";
                }
            });
            if (cartContent.children.length === 0) {
                total.style.display = "none";
                btnBuy.style.display = "none";
                cartEmpty.style.display = "block";
            }
            updateCartCount(arrayOfCards);
            updateTotalPrice();
        }
    });
}

addCartBtn.forEach((btn) => {
    btn.addEventListener("click", (event) => {
        const myCard = event.target.closest(".card");
        const cardImgSrc = myCard.querySelector(".card_image").src;
        const cardTitle = myCard.querySelector(".card_title").textContent;
        const priceEl = myCard.querySelector(".card_price") || myCard.querySelector("p");
        const cardPrice = priceEl ? priceEl.textContent : "0";

        if (btn.classList.contains("done")) {
            return;
        } else {
            addCardToArray(cardImgSrc, cardTitle, cardPrice);
        }
        btn.textContent = "تم أضافة";
        btn.classList.add("done");
        if(total && btnBuy && cartEmpty){
            total.style.display = "flex";
            btnBuy.style.display = "block";
            cartEmpty.style.display = "none";
        }
    });
    arrayOfCards.forEach((e) => {
        if (
            btn.parentElement.querySelector(".card_title") && 
            btn.parentElement.querySelector(".card_title").textContent === e.title
        ) {
            btn.textContent = "تم أضافة";
            btn.classList.add("done");
        }
    });
});

function addCardToArray(cardImgSrc, cardTitle, cardPrice) {
    const cardData = {
        id: Date.now(),
        src: cardImgSrc,
        title: cardTitle,
        price: cardPrice,
        completed: false,
    };
    arrayOfCards.push(cardData);
    if(cartContent) addToCart(arrayOfCards, cardTitle);
    addToLocaleStorage(arrayOfCards);
}

function addToCart(arrayOfCards, cardTitle) {
    if(!cartContent) return;
    const cartItems = cartContent.querySelectorAll(".cart_product_title");
    for (let i of cartItems) {
        if (i.textContent === cardTitle) {
            return;
        }
    }
    cartContent.innerHTML = "";
    arrayOfCards.forEach((card) => {
        const cartBox = document.createElement("div");
        cartBox.className = "cart_box";
        cartBox.setAttribute("data-id", card.id);
        cartBox.innerHTML = `
        <img src="${card.src}" class="cart_img">
        <div class="cart_detail">
        <p class="cart_product_title">${card.title}</p>
        <span class="cart_price">${card.price}</span>
        <div class="cart_quantity">
            <button id="increment">+</button>
            <span class="number">1</span>
            <button id="decrement">-</button>
        </div>
        <i class="fa-solid fa-trash cart_remove"></i>
        </div>
    `;
        cartContent.appendChild(cartBox);
        let cartQuantity = cartBox.querySelector(".cart_quantity");
        cartQuantity.addEventListener("click", (e) => {
            const numberElement = cartBox.querySelector(".number");
            const decrementBtn = cartBox.querySelector("#decrement");
            let quantity = numberElement.textContent;
            if ((e.target.id === "decrement") & (quantity > 1)) {
                quantity--;
                if (quantity === 1) {
                    decrementBtn.style.color = "#999";
                }
            } else if (e.target.id === "increment") {
                quantity++;
                decrementBtn.style.color = "#333";
            }
            numberElement.textContent = quantity;
            updateTotalPrice();
        });
    });
    updateCartCount(arrayOfCards);
    updateTotalPrice();
    arrayOfCards.forEach((e) => {
        if (e.completed === false) {
            e.completed = true;
        }
    });
}

function addToLocaleStorage(arrayOfCards) {
    window.localStorage.setItem("cards", JSON.stringify(arrayOfCards));
}

function getData() {
    let data = window.localStorage.getItem("cards");
    if (data) {
        let cards = JSON.parse(data);
        if(cartContent) addToCart(cards);
    }
}

function deleteCardWith(cardId) {
    arrayOfCards = arrayOfCards.filter((card) => card.id != cardId);
    addToLocaleStorage(arrayOfCards);
}

function updateTotalPrice() {
    let totalPriceElement = document.querySelector(".total_price");
    if(!totalPriceElement) return;
    const cartBoxes = document.querySelectorAll(".cart_box");
    let total = 0;
    cartBoxes.forEach((cartBox) => {
        const priceElement = cartBox.querySelector(".cart_price").textContent;
        const quantityElement = cartBox.querySelector(".number");
        const price = Number(priceElement.replace(/[^\d.]/g, ""));
        const quantity = Number(quantityElement.textContent);
        total += price * quantity;
    });
    totalPriceElement.textContent = `${total.toFixed(2)} جنيه`;
    window.localStorage.setItem("total_Price", `${total.toFixed(2)} جنيه`);
}

function updateCartCount(arrayOfCards) {
    const cartCountElement = document.querySelector(".cart_count");
    if(!cartCountElement) return;
    if (arrayOfCards.length > 0) {
        cartCountElement.style.visibility = "visible";
        cartCountElement.textContent = arrayOfCards.length;
    } else {
        cartCountElement.style.visibility = "hidden";
        cartCountElement.textContent = arrayOfCards.length;
    }
}

const allFilterCards = document.querySelectorAll(".card");
const allFilterBtns = document.querySelectorAll(".filter_btn");
allFilterBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
        showFilterContent(btn);
    });
});

function showFilterContent(btn) {
    allFilterCards.forEach((card) => {
        if (card.classList.contains(btn.id)) {
            removeActiveBtn();
            btn.classList.add("active_btn");
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
}

function removeActiveBtn() {
    allFilterBtns.forEach((btn) => {
        btn.classList.remove("active_btn");
    });
}
// منع تكرار الإرسال لأي فورم في المنصة (رسائل، تقييم، تسجيل)
document.addEventListener('submit', function (e) {
    if (e.target && e.target.tagName === 'FORM') {
        const submitBtn = e.target.querySelector('button[type="submit"], input[type="submit"], .btn-submit, .send_btn');
        
        if (submitBtn) {
            // تعطيل الزر برمجياً بعد جزء من الثانية لضمان نجاح الإرسال
            setTimeout(() => {
                submitBtn.disabled = true;
                submitBtn.style.cursor = 'not-allowed';
                submitBtn.style.opacity = '0.7';
                
                if (submitBtn.tagName === 'INPUT') {
                    submitBtn.value = 'جاري الإرسال...';
                } else {
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...';
                }
            }, 0);
        }
    }
});
// تفعيل قائمة الموبايل (Hamburger Menu)
const navMenu = document.getElementById('nav-menu');
const navToggle = document.getElementById('nav-toggle');
const navClose = document.getElementById('menu-close');

if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
        navMenu.classList.add('show_menu');
    });
}

if (navClose && navMenu) {
    navClose.addEventListener('click', () => {
        navMenu.classList.remove('show_menu');
    });
}

// إغلاق القائمة تلقائياً عند الضغط على أي رابط بداخلها
const navLinks = document.querySelectorAll('.nav_link');
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        if (typeof navMenu !== 'undefined' && navMenu && navMenu.classList.contains('show_menu')) {
            navMenu.classList.remove('show_menu');
        }
    });
});
