document.addEventListener("DOMContentLoaded", () => {
    const orderBtn = document.querySelector(".order_btn");
    const modal = document.querySelector(".container_modal"); 
    const layer = document.querySelector(".layer");
    const closeModal = document.querySelector(".close_modal");
    const addressDiv = document.querySelector(".address");
    const sendBtn = document.querySelector(".send_btn");

    // Address Inputs
    const userInput = document.querySelector(".input_user");
    const phoneInput = document.querySelector(".input_tel");
    const streetInput = document.querySelector(".input_address_street");
    const unityInput = document.querySelector(".input_address_unit");
    const cityInput = document.querySelector(".input_address_city");
    const boycottInput = document.querySelector(".input_address_boycott");
    const postalInput = document.querySelector(".input_address_postal");
    const inputs = [userInput, phoneInput, streetInput, unityInput, cityInput, boycottInput, postalInput];

    // Edit Address Modals & Inputs
    const modalChange = document.querySelector(".modal_change");
    const userChange = document.querySelector(".input_user_change");
    const phoneChange = document.querySelector(".input_tel_change");
    const streetChange = document.querySelector(".input_address_street_change");
    const unityChange = document.querySelector(".input_address_unit_change");
    const cityChange = document.querySelector(".input_address_city_change");
    const boycottChange = document.querySelector(".input_address_boycott_change");
    const postalChange = document.querySelector(".input_address_postal_change");
    const inputsChange = [userChange, phoneChange, streetChange, unityChange, cityChange, boycottChange, postalChange];

    // Payment Method Modal
    const paymentCard = document.getElementById("card-payment");
    const closeCardBtn = document.getElementById("close_visa_card");
    const modalCard = document.querySelector(".modal_card");
    const visaCardBtn = document.querySelector(".visa_card_btn");

    // --- SAFE JSON PARSING FOR CART ---
    let cartItems = [];
    try {
        const stored = localStorage.getItem("cards");
        if (stored) {
            cartItems = JSON.parse(stored);
        }
    } catch (error) {
        console.error("Failed to parse cart items from localStorage:", error);
        cartItems = [];
    }

    // Allow updateTotalPrice() from app.js to control the rendering of final-total-price
    const orderTotals = document.querySelectorAll(".cart-total-price");
    const localTotal = window.localStorage.getItem("total_Price");
    orderTotals.forEach(el => el.textContent = localTotal ? localTotal : "0 ج.م");

    // --- Payment Modal Handlers ---
    if (paymentCard) {
        paymentCard.addEventListener("click", () => {
            modalCard.classList.add("modal_active");
            layer.classList.add("layer_active");
        });
    }
    if (closeCardBtn) {
        closeCardBtn.addEventListener("click", () => {
            modalCard.classList.remove("modal_active");
            layer.classList.remove("layer_active");
        });
    }
    if (visaCardBtn) {
        visaCardBtn.addEventListener("click", (e) => {
            e.preventDefault();
            modalCard.classList.remove("modal_active");
            layer.classList.remove("layer_active");
        });
    }

    // --- Address Modal Handlers ---
    if (addressDiv && addressDiv.innerHTML.trim() === "") {
        addressDiv.innerHTML = `
          <h4 class="title_payment">عنوان الشحن</h4>
          <div class="box_address" style="cursor:pointer; background:#f8fafc; padding:15px; border-radius:8px; border:2px dashed #cbd5e1; text-align:center;">
            <span style="color:#3b82f6; font-weight:bold;"><i class="fa-solid fa-plus"></i> إضافة عنوان الشحن</span>
          </div>
        `;
        const boxAddress = document.querySelector(".box_address");
        if (boxAddress) {
            boxAddress.addEventListener("click", () => {
                modal.classList.add("modal_active");
                layer.classList.add("layer_active");
            });
        }
    }

    if (closeModal) {
        closeModal.addEventListener("click", () => {
            modal.classList.remove("modal_active");
            layer.classList.remove("layer_active");
            inputs.forEach(input => { if (input) input.style.borderColor = "#e2e8f0"; });
        });
    }

    function checkInputs(inputElements) {
        inputElements.forEach(input => {
            if (!input) return;
            input.style.borderColor = input.value.trim() === "" ? "#ef4444" : "#e2e8f0";
            input.addEventListener("keyup", () => {
                input.style.borderColor = input.value.trim() === "" ? "#ef4444" : "#e2e8f0";
            });
        });
    }

    if (sendBtn) {
        sendBtn.addEventListener("click", (e) => {
            e.preventDefault();
            const isEmpty = inputs.some(input => !input || input.value.trim() === "");
            
            if (isEmpty) {
                checkInputs(inputs);
            } else {
                modal.classList.remove("modal_active");
                layer.classList.remove("layer_active");

                addressDiv.innerHTML = `
                    <div class="address_details" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <h4 class="title_payment">عنوان الشحن</h4>
                        <span class="change_address" style="color:var(--main-color); cursor:pointer; font-weight:bold;"><i class="fa-solid fa-pen"></i> تعديل</span>
                    </div>
                    <div class="content_address" style="background:#f8fafc; padding:15px; border-radius:8px; line-height:1.8; border:1px solid #e2e8f0;">
                        <h5 id="user-Address" style="margin:0; font-size:16px; color:#0f172a;">${userInput.value}</h5>
                        <span id="phone-Address" style="display:block; color:#64748b;">${phoneInput.value}</span>
                        <hr style="border:0; border-top:1px solid #e2e8f0; margin:10px 0;">
                        <span id="street-Address">${streetInput.value}</span>، 
                        <span id="unity-Address">${unityInput.value}</span><br>
                        <span id="city-Address">${cityInput.value}</span> - 
                        <span id="boycott-Address">${boycottInput.value}</span><br>
                        <span style="color:#64748b;">الرمز البريدي: </span><span id="postal-Address">${postalInput.value}</span>
                    </div>
                `;

                const changeAddressBtn = document.querySelector(".change_address");
                if (changeAddressBtn) {
                    changeAddressBtn.addEventListener("click", () => {
                        modalChange.classList.add("modal_active");
                        layer.classList.add("layer_active");
                        
                        userChange.value = document.getElementById("user-Address").textContent;
                        phoneChange.value = document.getElementById("phone-Address").textContent;
                        streetChange.value = document.getElementById("street-Address").textContent;
                        unityChange.value = document.getElementById("unity-Address").textContent;
                        cityChange.value = document.getElementById("city-Address").textContent;
                        boycottChange.value = document.getElementById("boycott-Address").textContent;
                        postalChange.value = document.getElementById("postal-Address").textContent;
                    });
                }
            }
        });
    }

    const closeChangeBtn = document.querySelector(".close_change_btn");
    const doneChangeBtn = document.querySelector(".done_change_btn");

    if (closeChangeBtn) {
        closeChangeBtn.addEventListener("click", (e) => {
            e.preventDefault();
            modalChange.classList.remove("modal_active");
            layer.classList.remove("layer_active");
        });
    }

    if (doneChangeBtn) {
        doneChangeBtn.addEventListener("click", (e) => {
            e.preventDefault();
            const isEmpty = inputsChange.some(input => !input || input.value.trim() === "");
            
            if (isEmpty) {
                checkInputs(inputsChange);
            } else {
                modalChange.classList.remove("modal_active");
                layer.classList.remove("layer_active");

                document.getElementById("user-Address").textContent = userChange.value;
                document.getElementById("phone-Address").textContent = phoneChange.value;
                document.getElementById("street-Address").textContent = streetChange.value;
                document.getElementById("unity-Address").textContent = unityChange.value;
                document.getElementById("city-Address").textContent = cityChange.value;
                document.getElementById("boycott-Address").textContent = boycottChange.value;
                document.getElementById("postal-Address").textContent = postalChange.value;
            }
        });
    }

    // --- CHECKOUT SUBMISSION LOGIC ---
    if (orderBtn) {
        orderBtn.addEventListener("click", () => {
            let userAddressEl = document.getElementById("user-Address");
            if (!userAddressEl || userAddressEl.textContent.trim() === "") {
                if (modal && layer) {
                    modal.classList.add("modal_active");
                    layer.classList.add("layer_active");
                }
                return;
            }

            if (!cartItems || cartItems.length === 0) {
                alert("عربة التسوق فارغة!");
                window.location.href = "/products";
                return;
            }

            const formData = new FormData();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            formData.append("csrf_token", csrfToken);
            formData.append("ajax_checkout", "1");
            formData.append("full_name", document.getElementById("user-Address").textContent);
            formData.append("phone", document.getElementById("phone-Address").textContent);
            formData.append("address_line1", document.getElementById("street-Address").textContent);
            formData.append("address_line2", document.getElementById("unity-Address").textContent);
            formData.append("city", document.getElementById("city-Address").textContent);
            formData.append("governorate", document.getElementById("boycott-Address").textContent);
            formData.append("zip_code", document.getElementById("postal-Address").textContent);
            
            const hiddenPromo = document.getElementById('hidden-promo-code');
            if (hiddenPromo && hiddenPromo.value) {
                formData.append("applied_promo_code", hiddenPromo.value);
            }

            const rawTotal = document.getElementById('hidden-total-price')?.value || window.localStorage.getItem("total_Price") || 0;
            let finalPrice = rawTotal;
            if (typeof finalPrice === 'string') {
                finalPrice = finalPrice.replace(/[^\d.]/g, '');
            }
            formData.append("total_price", finalPrice || 0);

            formData.append("products", JSON.stringify(cartItems.map(item => {
                item.number = parseInt(item.number || item.quantity || item.qty || 1);
                return item;
            })));

            orderBtn.disabled = true;
            orderBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التنفيذ...';

            fetch("/checkout/process", {
                method: "POST",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
            .then(async res => {
                const text = await res.text();
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        localStorage.removeItem("cards");
                        localStorage.removeItem("total_Price");
                        localStorage.removeItem("activeCoupon");
                        
                        const popup = document.querySelector(".popup");
                        if (popup) {
                            popup.classList.add("modal_active");
                            layer.classList.add("layer_active");
                        } else {
                            window.location.href = data.redirect || "/my-orders";
                        }
                    } else {
                        alert("حدث خطأ أثناء تسجيل الطلب: " + (data.error || ""));
                        orderBtn.disabled = false;
                        orderBtn.textContent = "تأكيد الطلب";
                    }
                } catch (e) {
                    console.error("Server response:", text);
                    alert("فشل استجابة السيرفر. برجاء المحاولة مرة أخرى.");
                    orderBtn.disabled = false;
                    orderBtn.textContent = "تأكيد الطلب";
                }
            })
            .catch(err => {
                alert("حدث خطأ في الاتصال: " + err.message);
                orderBtn.disabled = false;
                orderBtn.textContent = "تأكيد الطلب";
            });
        });
    }
});