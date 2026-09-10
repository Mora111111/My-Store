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
const promoSection = document.getElementById("promo-section");
let activeCoupon = null;
try { activeCoupon = JSON.parse(localStorage.getItem('activeCoupon')) || null; } catch(e) { localStorage.removeItem('activeCoupon'); }

if (cartIcon && cart) {
    cartIcon.addEventListener("click", () => {
        cart.classList.add("showCart");
    });
}
if (cartClose && cart) {
    cartClose.addEventListener("click", () => {
        cart.classList.remove("showCart");
    });
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
        if(promoSection) promoSection.style.display = "block";
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
                if(promoSection) promoSection.style.display = "none";
            }
            updateCartCount(arrayOfCards);
            updateTotalPrice();
        }
    });
}

addCartBtn.forEach((btn) => {
    btn.addEventListener("click", (event) => {
        const myCard = event.target.closest(".card") || event.target.closest(".product_details_section");
        const cardImgSrc = myCard.querySelector(".card_image") ? myCard.querySelector(".card_image").src : (myCard.querySelector("#mainProductImage") ? myCard.querySelector("#mainProductImage").src : "");
        const cardTitle = myCard.querySelector(".card_title").textContent;
        const priceEl = myCard.querySelector(".card_price") || myCard.querySelector("p");
        let cardPrice = "0";
        if (priceEl) {
            const spanEl = priceEl.querySelector("span:first-child");
            cardPrice = spanEl ? spanEl.textContent : priceEl.textContent;
        }
        const cardId = btn.getAttribute("data-id");

        if (btn.classList.contains("done")) {
            return;
        } else {
            addCardToArray(cardImgSrc, cardTitle, cardPrice, cardId);
        }
        btn.textContent = "تم أضافة";
        btn.classList.add("done");
        if(total && btnBuy && cartEmpty){
            total.style.display = "flex";
            btnBuy.style.display = "block";
            cartEmpty.style.display = "none";
            if(promoSection) promoSection.style.display = "block";
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

function addCardToArray(cardImgSrc, cardTitle, cardPrice, cardId) {
    const cardData = {
        id: cardId,
        src: cardImgSrc,
        title: cardTitle,
        price: cardPrice,
        number: 1,
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
            <span class="number">${card.number || 1}</span>
            <button id="decrement" style="color: ${card.number > 1 ? '#333' : '#999'}">-</button>
        </div>
        <i class="fa-solid fa-trash cart_remove"></i>
        </div>
    `;
        cartContent.appendChild(cartBox);
        let cartQuantity = cartBox.querySelector(".cart_quantity");
        cartQuantity.addEventListener("click", (e) => {
            const numberElement = cartBox.querySelector(".number");
            const decrementBtn = cartBox.querySelector("#decrement");
            let quantity = Number(numberElement.textContent);
            
            if (e.target.id === "decrement" && quantity > 1) {
                quantity--;
                if (quantity === 1) {
                    decrementBtn.style.color = "#999";
                }
            } else if (e.target.id === "increment") {
                quantity++;
                decrementBtn.style.color = "#333";
            }
            
            numberElement.textContent = quantity;
            
            const currentCard = arrayOfCards.find(c => c.id == card.id);
            if(currentCard) {
                currentCard.number = quantity;
                addToLocaleStorage(arrayOfCards);
            }
            
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
    let subtotal = 0;
    let specificProductSubtotal = 0;

    cartBoxes.forEach((cartBox) => {
        const priceText = cartBox.querySelector(".cart_price").textContent;
        const cleanPrice = priceText.toString().replace(/,/g, '').replace(/[^\d.]/g, '');
        const price = parseFloat(cleanPrice) || 0;
        const quantityElement = cartBox.querySelector(".number");
        const quantity = quantityElement ? parseInt(quantityElement.textContent) : 1;
        const itemId = parseInt(cartBox.getAttribute('data-id')) || 0;
        const itemTotal = price * quantity;
        
        subtotal += itemTotal;
        
        const tType = activeCoupon ? (activeCoupon.target_type || activeCoupon.target) : null;
        const targetId = activeCoupon ? parseInt(activeCoupon.target_product_id || activeCoupon.product_id) : 0;

        if (activeCoupon && tType === 'specific_product' && targetId === itemId) {
            specificProductSubtotal += itemTotal;
        }
    });

    let discountTotal = 0;
    if (activeCoupon) {
        const tType = activeCoupon.target_type || activeCoupon.target;
        const dType = activeCoupon.discount_type || activeCoupon.type;
        const dValue = parseFloat(activeCoupon.discount_value || activeCoupon.value || 0);

        if (tType === 'all') {
            if (dType === 'percentage') {
                discountTotal = subtotal * (dValue / 100);
            } else {
                discountTotal = dValue; 
            }
        } else if (tType === 'specific_product' && specificProductSubtotal > 0) {
            if (dType === 'percentage') {
                discountTotal = specificProductSubtotal * (dValue / 100);
            } else {
                discountTotal = dValue; 
            }
            if (discountTotal > specificProductSubtotal) discountTotal = specificProductSubtotal;
        } else if (tType === 'specific_product' && specificProductSubtotal === 0) {
            activeCoupon = null;
            localStorage.removeItem('activeCoupon');
            const msgEl = document.getElementById('promo_message');
            if (msgEl) {
                msgEl.textContent = 'المنتج المشمول بالخصم غير موجود بالسلة.';
                msgEl.style.color = '#ef4444';
            }
        }
    }

    if (isNaN(subtotal)) subtotal = 0;
    let finalTotal = subtotal - discountTotal;
    if (finalTotal < 0) finalTotal = 0;

    let html = `${finalTotal.toFixed(2)} ج.م`;
    if (discountTotal > 0) {
        html += `<br><span style="color:#10b981; font-size:14px; font-weight:bold;">(تم خصم ${discountTotal.toFixed(2)} ج.م)</span>`;
    }
    totalPriceElement.innerHTML = html;
    window.localStorage.setItem("total_Price", `${finalTotal.toFixed(2)} ج.م`);

    const checkoutTotalElements = document.querySelectorAll('.cart-total-price');
    const checkoutFinalElements = document.querySelectorAll('.final-total-price');
    const discountRow = document.getElementById('discount-row');
    const discountDisplay = document.getElementById('display-discount-amount');
    const discountTitle = document.getElementById('discount-title');
    const hiddenPromo = document.getElementById('hidden-promo-code');
    const hiddenTotal = document.getElementById('hidden-total-price');
    const shippingCostEl = document.getElementById('display-shipping-cost');
    
    if (checkoutTotalElements.length > 0) {
        checkoutTotalElements.forEach(el => el.textContent = subtotal.toFixed(2) + ' ج.م');
        
        let shippingCost = 0;
        if (shippingCostEl) {
            const cleanShip = shippingCostEl.textContent.toString().replace(/,/g, '').replace(/[^\d.]/g, '');
            shippingCost = parseFloat(cleanShip) || 0;
        }
        
        if (discountTotal > 0 && activeCoupon) {
            if(discountRow) discountRow.style.display = 'flex';
            if(discountTitle) discountTitle.textContent = 'كوبون الخصم (' + activeCoupon.code + ')';
            if(discountDisplay) discountDisplay.textContent = '- ' + discountTotal.toFixed(2) + ' ج.م';
        } else {
            if(discountRow) discountRow.style.display = 'none';
        }
        
        const checkoutFinal = finalTotal + shippingCost;
        checkoutFinalElements.forEach(el => el.textContent = checkoutFinal.toFixed(2) + ' ج.م');
        
        if(hiddenTotal) hiddenTotal.value = checkoutFinal;
        if(hiddenPromo) hiddenPromo.value = activeCoupon ? activeCoupon.code : '';
    }
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



document.addEventListener('click', async (e) => {
    if(e.target.id === 'apply_promo_btn') {
        e.preventDefault();
        const codeInput = document.getElementById('promo_code_input');
        const code = codeInput.value.trim();
        const msgEl = document.getElementById('promo_message');

        if(!code) {
            msgEl.textContent = 'أدخل الكود أولاً';
            msgEl.style.color = '#ef4444';
            return;
        }

        let cartIds = [];
        try {
            const cards = JSON.parse(localStorage.getItem("cards")) || [];
            cartIds = cards.map(c => parseInt(c.id));
        } catch(err) {}

        e.target.textContent = '...';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        try {
            const res = await fetch('/api/validate-coupon', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ code: code, cart_ids: cartIds })
            });
            
            if (!res.ok) throw new Error('HTTP Error: ' + res.status);
            
            const data = await res.json();

            if(data.success) {
                activeCoupon = data.coupon;
                localStorage.setItem('activeCoupon', JSON.stringify(activeCoupon));
                msgEl.textContent = data.message;
                msgEl.style.color = '#10b981';
            } else {
                activeCoupon = null;
                localStorage.removeItem('activeCoupon');
                msgEl.textContent = data.message;
                msgEl.style.color = '#ef4444';
            }
            updateTotalPrice();
        } catch (err) {
            msgEl.textContent = 'حدث خطأ في الشبكة.';
            msgEl.style.color = '#ef4444';
        }
        e.target.textContent = 'تطبيق';
    }
});
