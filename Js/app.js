document.addEventListener("DOMContentLoaded", function() {
    const profileBtn = document.getElementById('profile-btn');
    const profileMenu = document.getElementById('profile-menu');
    const notifBtn = document.getElementById('notif-btn');
    const notifMenu = document.getElementById('notif-menu');

    const setNotificationMenuOpen = (open) => {
        if (notifBtn && notifMenu) {
            notifMenu.hidden = !open;
            notifBtn.setAttribute('aria-expanded', String(open));
        }
    };
    
    if(profileBtn && profileMenu) {
        profileBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            setNotificationMenuOpen(false);
            if(profileMenu.style.display === 'none' || profileMenu.style.display === '') {
                profileMenu.style.display = 'block';
            } else {
                profileMenu.style.display = 'none';
            }
        });
    }

    document.addEventListener('click', (event) => {
        if (profileMenu && profileBtn && !profileMenu.contains(event.target) && !profileBtn.contains(event.target)) {
            profileMenu.style.display = 'none';
        }
        if (notifMenu && notifBtn && !notifMenu.contains(event.target) && !notifBtn.contains(event.target)) {
            setNotificationMenuOpen(false);
        }
    });

    if (!notifBtn || !notifMenu) return;

    const notifBadge = document.getElementById('notif-badge');
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    const bellIcon = notifBtn.querySelector('.fa-bell');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const updateUnreadCount = (count) => {
        notifBadge.textContent = count > 99 ? '99+' : String(count);
        notifBadge.hidden = count === 0;
        markAllReadBtn.hidden = count === 0;
        bellIcon.classList.toggle('fa-solid', count > 0);
        bellIcon.classList.toggle('fa-regular', count === 0);
    };

    const setItemRead = (item) => {
        item.dataset.isRead = '1';
        item.classList.remove('is-unread');
    };

    const postRead = async (action, body) => {
        try {
            const response = await fetch('/api/notifications/' + action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(body),
                keepalive: true
            });
            if (!response.ok) return null;
            const data = await response.json();
            return data.success && Number.isInteger(data.unread_count) ? data : null;
        } catch {
            return null;
        }
    };

    notifBtn.addEventListener('click', () => setNotificationMenuOpen(notifMenu.hidden));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !notifMenu.hidden) {
            setNotificationMenuOpen(false);
            notifBtn.focus();
        }
    });

    const openNotification = async (event) => {
        const item = event.target.closest('.notif-item');
        if (!item || item.dataset.isRead !== '0' || event.defaultPrevented) return;

        const followInCurrentTab = event.button === 0 && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey;
        if (followInCurrentTab) event.preventDefault();

        const result = await postRead('mark-read', { id: Number(item.dataset.notifId) });
        if (result && item.dataset.isRead === '0') {
            setItemRead(item);
            updateUnreadCount(result.unread_count);
        }
        if (followInCurrentTab) window.location.assign(item.href);
    };

    notifMenu.addEventListener('click', openNotification);
    notifMenu.addEventListener('auxclick', (event) => {
        if (event.button === 1) openNotification(event);
    });

    markAllReadBtn.addEventListener('click', async () => {
        markAllReadBtn.disabled = true;
        const result = await postRead('mark-all-read', {});
        markAllReadBtn.disabled = false;
        if (result) {
            notifMenu.querySelectorAll('.notif-item.is-unread').forEach(setItemRead);
            updateUnreadCount(result.unread_count);
            notifBtn.focus();
        }
    });
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
let pendingCoupon = null;
try { pendingCoupon = JSON.parse(localStorage.getItem('activeCoupon')); } catch(e) { localStorage.removeItem('activeCoupon'); }

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

const cartItemsById = new Map(arrayOfCards.map(card => [String(card.id), card]));
let stockLimitsChanged = false;
addCartBtn.forEach((btn) => {
    btn.dataset.addToCartLabel = btn.textContent;
    const card = cartItemsById.get(btn.getAttribute('data-id'));
    const maxStock = parseStockLimit(btn.getAttribute('data-stock'));
    if (card && maxStock !== null && card.maxStock !== maxStock) {
        card.maxStock = maxStock;
        stockLimitsChanged = true;
    }
});
if (stockLimitsChanged) addToLocaleStorage(arrayOfCards);

if (cartContent && total && btnBuy && cartEmpty) {
    if (localStorage.getItem("cards")) addToCart();
    if (cartContent.children.length !== 0) {
        total.style.display = "flex";
        btnBuy.style.display = "block";
        cartEmpty.style.display = "none";
        if(promoSection) promoSection.style.display = "block";
    }
    
    cartContent.addEventListener("click", (e) => {
        if (e.target.classList.contains("cart_remove")) {
            const cartBox = e.target.parentElement.parentElement;
            const cardId = cartBox.getAttribute("data-id");
            cartBox.remove();
            deleteCardWith(cardId);
            updateCartButtons(cardId, false);
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
        if (btn.disabled) return;
        const maxStock = parseStockLimit(btn.getAttribute('data-stock'));
        if (maxStock !== null && maxStock <= 0) return;

        const cardId = btn.getAttribute("data-id");
        if (btn.classList.contains("done") || cartItemsById.has(cardId)) return;

        const myCard = event.target.closest(".card") || event.target.closest(".product_details_section");
        const cardImgSrc = myCard.querySelector(".card_image") ? myCard.querySelector(".card_image").src : (myCard.querySelector("#mainProductImage") ? myCard.querySelector("#mainProductImage").src : "");
        const cardTitle = myCard.querySelector(".card_title").textContent;
        const priceEl = myCard.querySelector(".card_price") || myCard.querySelector("p");
        let cardPrice = "0";
        if (priceEl) {
            const spanEl = priceEl.querySelector("span:first-child");
            cardPrice = spanEl ? spanEl.textContent : priceEl.textContent;
        }
        addCardToArray(cardImgSrc, cardTitle, cardPrice, cardId, maxStock);
        updateCartButtons(cardId, true);
        if(total && btnBuy && cartEmpty){
            total.style.display = "flex";
            btnBuy.style.display = "block";
            cartEmpty.style.display = "none";
            if(promoSection) promoSection.style.display = "block";
        }
    });
    if (cartItemsById.has(btn.getAttribute("data-id"))) {
        setCartButtonAdded(btn, true);
    }
});

function parseStockLimit(rawStock) {
    const stock = parseInt(rawStock, 10);
    return Number.isNaN(stock) ? null : stock;
}

function setCartButtonAdded(btn, added) {
    if (btn.disabled) return;
    btn.classList.toggle("done", added);
    btn.textContent = added ? "تم أضافة" : btn.dataset.addToCartLabel;
}

function updateCartButtons(cardId, added) {
    addCartBtn.forEach((btn) => {
        if (btn.getAttribute("data-id") === cardId) setCartButtonAdded(btn, added);
    });
}

function addCardToArray(cardImgSrc, cardTitle, cardPrice, cardId, maxStock) {
    const cardData = {
        id: cardId,
        src: cardImgSrc,
        title: cardTitle,
        price: cardPrice,
        maxStock,
        number: 1,
        completed: false,
    };
    arrayOfCards.push(cardData);
    cartItemsById.set(cardId, cardData);
    addToCart();
    addToLocaleStorage(arrayOfCards);
}

function addToCart() {
    if(!cartContent) return;
    cartContent.innerHTML = "";
    arrayOfCards.forEach((card) => {
        const maxStock = parseStockLimit(card.maxStock);
        const isAtStockLimit = (quantity) => maxStock !== null && quantity >= maxStock;
        const cartBox = document.createElement("div");
        cartBox.className = "cart_box";
        cartBox.setAttribute("data-id", card.id);
        cartBox.innerHTML = `
        <img src="${card.src}" class="cart_img">
        <div class="cart_detail">
        <p class="cart_product_title">${card.title}</p>
        <span class="cart_price">${card.price}</span>
        <div class="cart_quantity">
            <button id="increment" aria-disabled="${isAtStockLimit(card.number || 1)}">+</button>
            <span class="number">${card.number || 1}</span>
            <button id="decrement" style="color: ${card.number > 1 ? '#333' : '#999'}">-</button>
        </div>
        <i class="fa-solid fa-trash cart_remove"></i>
        </div>
    `;
        cartContent.appendChild(cartBox);
        const cartQuantity = cartBox.querySelector(".cart_quantity");
        const numberElement = cartBox.querySelector(".number");
        const incrementBtn = cartBox.querySelector("#increment");
        const decrementBtn = cartBox.querySelector("#decrement");
        cartQuantity.addEventListener("click", (e) => {
            let quantity = Number(numberElement.textContent);
            
            if (e.target.id === "decrement" && quantity > 1) {
                quantity--;
            } else if (e.target.id === "increment" && !isAtStockLimit(quantity)) {
                quantity++;
            } else {
                return;
            }
            
            numberElement.textContent = quantity;
            incrementBtn.setAttribute('aria-disabled', String(isAtStockLimit(quantity)));
            decrementBtn.style.color = quantity > 1 ? "#333" : "#999";
            card.number = quantity;
            addToLocaleStorage(arrayOfCards);
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

function deleteCardWith(cardId) {
    arrayOfCards = arrayOfCards.filter((card) => card.id != cardId);
    cartItemsById.delete(cardId);
    addToLocaleStorage(arrayOfCards);
}

async function validateCoupon(code) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const response = await fetch('/api/validate-coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ code, cart_ids: arrayOfCards.map(card => parseInt(card.id)) })
    });
    if (!response.ok) throw new Error('HTTP Error: ' + response.status);
    return response.json();
}

function setActiveCoupon(coupon) {
    pendingCoupon = null;
    activeCoupon = coupon;
    if (coupon) {
        localStorage.setItem('activeCoupon', JSON.stringify(coupon));
    } else {
        localStorage.removeItem('activeCoupon');
    }
}

async function revalidateActiveCoupon() {
    if (arrayOfCards.length === 0) {
        updateTotalPrice();
        return;
    }

    const coupon = pendingCoupon;
    if (!coupon?.code) return;

    let data;
    try {
        data = await validateCoupon(coupon.code);
    } catch {
        return;
    }

    if (pendingCoupon !== coupon) return;
    if (data.success === true) {
        setActiveCoupon(data.coupon);
    } else if (data.success === false) {
        setActiveCoupon(null);
        const message = document.getElementById('promo_message');
        if (message) message.textContent = '';
    } else {
        return;
    }
    updateTotalPrice();
}

document.addEventListener('DOMContentLoaded', revalidateActiveCoupon);

function updateTotalPrice() {
    if (arrayOfCards.length === 0) {
        setActiveCoupon(null);
        localStorage.removeItem('total_Price');
        const message = document.getElementById('promo_message');
        if (message) message.textContent = '';
    }

    const totalPriceElement = document.querySelector(".total_price");
    let subtotal = 0;
    let specificProductSubtotal = 0;

    arrayOfCards.forEach((card) => {
        const cleanPrice = String(card.price).replace(/,/g, '').replace(/[^\d.]/g, '');
        const price = parseFloat(cleanPrice) || 0;
        const quantity = parseInt(card.number || 1);
        const itemId = parseInt(card.id) || 0;
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
            setActiveCoupon(null);
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
    if (totalPriceElement) totalPriceElement.innerHTML = html;
    if (arrayOfCards.length > 0) {
        localStorage.setItem("total_Price", `${finalTotal.toFixed(2)} ج.م`);
    }
    window.dispatchEvent(new CustomEvent('cartTotalsUpdated', {
        detail: { subtotal, finalTotal, discountTotal, couponCode: activeCoupon?.code }
    }));
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

        e.target.textContent = '...';

        try {
            const data = await validateCoupon(code);

            if(data.success) {
                setActiveCoupon(data.coupon);
                msgEl.textContent = data.message;
                msgEl.style.color = '#10b981';
            } else {
                setActiveCoupon(null);
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
