document.addEventListener("DOMContentLoaded", () => {
    let itemPayment = document.getElementById("item-payment");
    let layer = document.querySelector(".layer");
    
    let cartItems = JSON.parse(localStorage.getItem("cards")) || [];
    let totalPriceSum = 0;

    if (cartItems.length > 0 && itemPayment) {
        let productsContainer = document.createElement('div');
        
        cartItems.forEach((item) => {
            let itemPrice = parseFloat(item.price) || 0;
            let itemQty = item.quantty || 1;
            totalPriceSum += (itemPrice * itemQty);
            
            productsContainer.innerHTML += `
            <div class="boxs_order_total" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px;">
                <div class="box_order_total" style="display:flex; align-items:center; gap:10px;">
                    <img src="${item.src}" alt="" class="order_img" style="width:50px; height:50px; object-fit:contain;">
                    <span class="order_name">${item.title} <small>(x${itemQty})</small></span>
                </div>
                <div class="box_order_total">
                    <span class="order_price">${(itemPrice * itemQty).toFixed(2)} جنيه</span>
                </div>
            </div>
            `;
        });
        
        itemPayment.insertBefore(productsContainer, itemPayment.querySelector('.boxs_order_total'));
        
        let totalElements = document.querySelectorAll('.order_total');
        totalElements.forEach(el => {
            el.textContent = totalPriceSum.toFixed(2) + ' جنيه';
        });
    }

    let address = document.querySelector(".address");
    let modal = document.querySelector(".container_modal");
    let modalChange = document.querySelector(".modal_change");

    if (address) {
        address.innerHTML = `
          <h4 class="title_payment">عنوان الشحن</h4>
          <div class="box_address">
            <img src="images/payment/payment_location.png" alt="" class="address_img">
            <span>إضافة عنوان الشحن</span>
          </div>
        `;

        let boxAddress = document.querySelector(".box_address");
        if(boxAddress) {
            boxAddress.addEventListener("click", () => {
              modal.classList.add("modal_active");
              layer.classList.add("layer_active");
            });
        }
    }

    let closeModal = document.querySelector(".close_modal");
    if (closeModal) {
        closeModal.addEventListener("click", () => {
          modal.classList.remove("modal_active");
          layer.classList.remove("layer_active");
        });
    }

    let sendBtn = document.querySelector(".send_btn");
    let inputUser = document.querySelector(".input_user");
    let inputTel = document.querySelector(".input_tel");
    let inputAddressStreet = document.querySelector(".input_address_street");
    let inputAddressUnit = document.querySelector(".input_address_unit");
    let inputAddressCity = document.querySelector(".input_address_city");
    let inputAddressBoycott = document.querySelector(".input_address_boycott");
    let inputAddressPostal = document.querySelector(".input_address_postal");

    let inputs = [inputUser, inputTel, inputAddressStreet, inputAddressUnit, inputAddressCity, inputAddressBoycott, inputAddressPostal];

    if (sendBtn && inputUser) {
        sendBtn.addEventListener("click", (e) => {
          e.preventDefault();
          
          let isEmpty = inputs.some(input => input.value.trim() === "");
          
          if (isEmpty) {
            chickInputs(inputs);
          } else {
            modal.classList.remove("modal_active");
            layer.classList.remove("layer_active");

            address.innerHTML = `
                <div class="address_details" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                    <h4 class="title_payment">عنوان الشحن</h4>
                    <span class="change_address" style="color:var(--main-color); cursor:pointer; font-weight:bold;">تعديل</span>
                </div>
                <div class="content_address" style="background:#f9f9f9; padding:15px; border-radius:8px; line-height:1.8;">
                    <h5 id="user-Address" style="margin:0; font-size:16px; color:#333;">${inputUser.value}</h5>
                    <span id="phone-Address" style="display:block; color:#666;">${inputTel.value}</span>
                    <hr style="border:0; border-top:1px solid #ddd; margin:10px 0;">
                    <span id="street-Address">${inputAddressStreet.value}</span>، 
                    <span id="unity-Address">${inputAddressUnit.value}</span><br>
                    <span id="city-Address">${inputAddressCity.value}</span>، 
                    <span id="boycott-Address">${inputAddressBoycott.value}</span><br>
                    <span>الرمز البريدي: </span><span id="postal-Address">${inputAddressPostal.value}</span>
                </div>
            `;

            let changeAddressBtn = document.querySelector(".change_address");
            if (changeAddressBtn) {
                changeAddressBtn.addEventListener("click", () => {
                  modalChange.classList.add("modal_active");
                  layer.classList.add("layer_active");
                  changeInputs();
                });
            }
          }
        });
    }

    let closeChangeBtn = document.querySelector(".close_change_btn");
    let doneChangeBtn = document.querySelector(".done_change_btn");

    if (closeChangeBtn) {
        closeChangeBtn.addEventListener("click", (e) => {
          e.preventDefault();
          modalChange.classList.remove("modal_active");
          layer.classList.remove("layer_active");
        });
    }

    let userChange = document.querySelector(".input_user_change");
    let telChange = document.querySelector(".input_tel_change");
    let streetChange = document.querySelector(".input_address_street_change");
    let unitChange = document.querySelector(".input_address_unit_change");
    let cityChange = document.querySelector(".input_address_city_change");
    let boycottChange = document.querySelector(".input_address_boycott_change");
    let postalChange = document.querySelector(".input_address_postal_change");

    let inputsChange = [userChange, telChange, streetChange, unitChange, cityChange, boycottChange, postalChange];

    if (doneChangeBtn && userChange) {
        doneChangeBtn.addEventListener("click", (e) => {
          e.preventDefault();
          let isEmpty = inputsChange.some(input => input.value.trim() === "");
          
          if (isEmpty) {
            chickInputs(inputsChange);
          } else {
            modalChange.classList.remove("modal_active");
            layer.classList.remove("layer_active");

            document.getElementById("user-Address").textContent = userChange.value;
            document.getElementById("street-Address").textContent = streetChange.value;
            document.getElementById("unity-Address").textContent = unitChange.value;
            document.getElementById("city-Address").textContent = cityChange.value;
            document.getElementById("boycott-Address").textContent = boycottChange.value;
            document.getElementById("postal-Address").textContent = postalChange.value;
            document.getElementById("phone-Address").textContent = telChange.value;
          }
        });
    }

    let cardPayment = document.getElementById("card-payment");
    let modalCard = document.querySelector(".modal_card");
    let closeVisaCard = document.getElementById("close_visa_card");
    let visaCardBtn = document.querySelector(".visa_card_btn");

    if (cardPayment && modalCard) {
        cardPayment.addEventListener("click", () => {
          modalCard.classList.add("modal_active");
          layer.classList.add("layer_active");
        });
    }

    if (closeVisaCard) {
        closeVisaCard.addEventListener("click", () => {
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

    function changeInputs() {
      if(userChange && document.getElementById("user-Address")){
          userChange.value = document.getElementById("user-Address").textContent;
          telChange.value = document.getElementById("phone-Address").textContent;
          streetChange.value = document.getElementById("street-Address").textContent;
          unitChange.value = document.getElementById("unity-Address").textContent;
          postalChange.value = document.getElementById("postal-Address").textContent;
          cityChange.value = document.getElementById("city-Address").textContent;
          boycottChange.value = document.getElementById("boycott-Address").textContent;
      }
    }

    function chickInputs(inputsArray) {
      inputsArray.forEach((input) => {
        if (!input) return;
        if (input.value.trim() === "") {
          input.style.borderColor = "#d9534f";
        } else {
          input.style.borderColor = "#a9a9a9";
        }
        input.addEventListener("keyup", () => {
          if (input.value.length > 0) {
            input.style.borderColor = "#a9a9a9";
          }
        });
      });
    }
});