<style>
    body { font-family: 'Tajawal', sans-serif; background-color: #f4f6f9; }

    .container_payment { padding-top: 120px !important; }
    .box_payment { max-width: 1200px; margin: 0 auto; padding: 0 25px; display: flex; align-items: center; justify-content: space-between; }
    
    .logo_payment { height: 60px !important; background-color: rgba(255, 255, 255, 0.95); padding: 5px 15px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: 0.3s; }
    .logo_payment:hover { transform: scale(1.05); }

    .back-link { background: rgba(255, 255, 255, 0.15); color: #ffffff; text-decoration: none; font-weight: 700; font-size: 18px; padding: 10px 20px; border-radius: 40px; display: inline-flex; align-items: center; gap: 10px; border: 1px solid rgba(255, 255, 255, 0.2); transition: 0.3s; }
    .back-link:hover { background: #38bdf8; color: #0f172a; border-color: #38bdf8; transform: translateX(-5px); }

    .item_payment { padding: 35px !important; border-radius: 20px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important; background: #fff; border: none !important; margin-bottom: 25px; }
    .title_payment { font-size: 24px !important; font-weight: bold !important; margin-bottom: 25px !important; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; color: #0f172a; }

    .box_order_total span { font-size: 20px !important; }
    .order_total { font-size: 28px !important; color: #0f172a !important; font-weight: bold; }

    .order_btn { font-size: 22px !important; padding: 18px 20px !important; border-radius: 50px !important; background: linear-gradient(145deg, #f97316, #ea580c) !important; color: white !important; font-weight: bold !important; transition: 0.3s !important; box-shadow: 0 8px 15px rgba(249, 115, 22, 0.3) !important; border: none !important; width: 100%; cursor: pointer; }
    .order_btn:hover { transform: translateY(-3px) !important; background: linear-gradient(145deg, #ea580c, #c2410c) !important; }

    .form_input { font-size: 18px !important; padding: 16px !important; border-radius: 12px !important; border: 2px solid #e2e8f0 !important; width: 100%; margin-bottom: 15px; font-family: 'Tajawal', sans-serif; }
    .form_input:focus { border-color: #f97316 !important; outline: none !important; }

    .send_btn, .change_modal_btn { font-size: 20px !important; padding: 15px 30px !important; border-radius: 40px !important; font-weight: bold !important; border: none; cursor: pointer; transition: 0.3s; }
    .send_btn { background: #f97316; color: white; }
    .send_btn:hover { background: #ea580c; }
    .done_change_btn { background: #10b981; color: white; }
    
    .container_modal { 
        border-radius: 25px !important; 
        padding: 20px !important; 
        z-index: 1005 !important;
    }

    .item_payment.address:empty { 
        display: none !important; 
    }

    .layer {
        z-index: 1004 !important;
        position: fixed !important; 
    }
</style>

<script>
    const savedAddress = <?php echo json_encode($lastOrder ?: null); ?>;
</script>

<div class="container_payment">
  <div class="content_payment">
    <div class="item_payment address"></div>
    
    <div class="item_payment" id="order-review-section">
      <h4 class="title_payment"><?= lang('order_products_title') ?></h4>
      <div id="review-products-container" style="display: flex; flex-direction: column; gap: 15px;"></div>
    </div>

    <div class="item_payment">
      <h4 class="title_payment"><?= lang('payment_methods_title') ?></h4>
      <div class="payment-methods-container" style="display: flex; flex-direction: column; gap: 15px;">
        
        <label style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border: 2px solid var(--main-color); border-radius: 12px; cursor: pointer; background: #f8fafc; transition: 0.3s;" id="label_cod">
            <div style="display: flex; align-items: center; gap: 15px;">
                <input type="radio" name="payment_method" value="cod" checked style="width: 20px; height: 20px; accent-color: var(--main-color);">
                <div>
                    <h5 style="margin: 0; font-size: 16px; color: #0f172a;"><?= lang('cod_title') ?></h5>
                    <span style="font-size: 13px; color: #64748b;"><?= lang('cod_desc') ?></span>
                </div>
            </div>
            <i class="fa-solid fa-hand-holding-dollar" style="font-size: 24px; color: #64748b;"></i>
        </label>

        <?php if(!empty($site_settings['enable_online_payment'])): ?>
        <label style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; background: #fff; transition: 0.3s;" id="label_online_card">
            <div style="display: flex; align-items: center; gap: 15px;">
                <input type="radio" name="payment_method" value="online_card" style="width: 20px; height: 20px; accent-color: var(--main-color);">
                <div>
                    <h5 style="margin: 0; font-size: 16px; color: #0f172a;"><?= lang('online_card_title') ?></h5>
                    <span style="font-size: 13px; color: #10b981; font-weight: bold;"><?= lang('free_shipping_badge') ?></span>
                </div>
            </div>
            <div style="display: flex; gap: 5px;">
                <img src="/images/payment/payment_1.png" style="height: 20px;">
                <img src="/images/payment/payment_2.png" style="height: 20px;">
            </div>
        </label>

        <label style="display: flex; align-items: center; justify-content: space-between; padding: 15px; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; background: #fff; transition: 0.3s;" id="label_online_wallet">
            <div style="display: flex; align-items: center; gap: 15px;">
                <input type="radio" name="payment_method" value="online_wallet" style="width: 20px; height: 20px; accent-color: var(--main-color);">
                <div>
                    <h5 style="margin: 0; font-size: 16px; color: #0f172a;"><?= lang('online_wallet_title') ?></h5>
                    <span style="font-size: 13px; color: #10b981; font-weight: bold;"><?= lang('free_shipping_badge') ?></span>
                </div>
            </div>
            <div style="display: flex; gap: 5px;">
                <i class="fa-solid fa-wallet" style="font-size: 24px; color: #64748b;"></i>
            </div>
        </label>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <div class="content_payment">
    <div class="item_payment" id="item-payment">
      <h4 class="title_payment"><?= lang('summary_title') ?></h4>
      <div class="boxs_order_total">
        <div class="box_order_total">
          <span><?= lang('order_total_title') ?></span>
          <span class="order_total cart-total-price"></span>
        </div>
        <div class="box_order_total">
          <span><?= lang('shipping_costs_title') ?></span>
          <span id="display-shipping-cost"><?php echo isset($site_settings['shipping_cost']) && $site_settings['shipping_cost'] > 0 ?$site_settings['shipping_cost'] . ' ' . lang('currency_egp') : lang('free_text'); ?></span>
        </div>
        <div class="box_order_total" id="discount-row" style="color: #10b981; font-weight: bold; display: none;">
          <span id="discount-title"><?= lang('discount_coupon_title') ?></span>
          <span id="display-discount-amount">- 0 <?= lang('currency_egp') ?></span>
        </div>
      </div>
      <div class="boxs_order_total">
        <div class="box_order_total">
          <span><?= lang('final_total_title') ?></span>
          <span class="order_total final-total-price"></span>
        </div>
        <button class="order_btn"><?= lang('confirm_order_btn') ?></button>
      </div>
    </div>
    <div class="item_payment item_payment_safety">
      <img src="/images/logos/logo.png" alt="Safety Logo" />
      <p><?= lang('secure_payment_msg') ?></p>
      <img src="/images/payment/payment_5.png" alt="Safety" />
    </div>
  </div>

  <!-- Address Modals -->
  <div class="container_modal" id="modal-add-address">
    <div class="modal_header">
      <h4 class="modal_title"><?= lang('add_shipping_address') ?></h4>
    </div>
    <div class="modal_body">
      <div class="form_section">
        <?= CSRF::getField() ?>
        <div class="form_body">
          <div class="form_title"><?= lang('personal_details') ?></div>
          <div class="form_box_modal">
            <input type="text" name="full_name" placeholder="<?= lang('customer_name_ph') ?>" required class="form_input input_user" />
            <input type="text" name="phone" placeholder="<?= lang('phone_number_ph') ?>" required class="form_input input_tel" />
          </div>
          <div class="form_title"><?= lang('address_details') ?></div>
          <div class="form_box_modal">
            <input type="text" name="address_line1" placeholder="<?= lang('street_ph') ?>" required class="form_input input_address_street" />
            <input type="text" name="address_line2" placeholder="<?= lang('unit_ph') ?>" required class="form_input input_address_unit" />
          </div>
          <div class="form_box_modal">
            <input type="text" name="city" placeholder="<?= lang('city_ph') ?>" required class="form_input input_address_city" />
            <input type="text" name="governorate" placeholder="<?= lang('governorate_ph') ?>" required class="form_input input_address_boycott" />
            <input type="text" name="zip_code" placeholder="<?= lang('zip_code_ph') ?>" required class="form_input input_address_postal" />
          </div>
          <div class="form_box_modal" style="justify-content: center;">
            <input type="button" value="<?= lang('add_shipping_address') ?>" class="send_btn" id="btn-save-address" />
          </div>
          <i class="fa-solid fa-xmark close_modal"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="container_modal modal_change" id="modal-edit-address">
    <div class="modal_header">
      <h4 class="modal_title"><?= lang('edit_shipping_address') ?></h4>
    </div>
    <div class="modal_body">
      <div class="form_section">
        <div class="form_body">
          <div class="form_title"><?= lang('personal_details') ?></div>
          <div class="form_box_modal">
            <input type="text" placeholder="<?= lang('customer_name_ph') ?>" required class="form_input input_user_change" />
            <input type="text" placeholder="<?= lang('phone_number_ph') ?>" required class="form_input input_tel_change" />
          </div>
          <div class="form_title"><?= lang('address_details') ?></div>
          <div class="form_box_modal">
            <input type="text" placeholder="<?= lang('street_ph') ?>" required class="form_input input_address_street_change" />
            <input type="text" placeholder="<?= lang('unit_ph') ?>" required class="form_input input_address_unit_change" />
          </div>
          <div class="form_box_modal">
            <input type="text" placeholder="<?= lang('city_ph') ?>" required class="form_input input_address_city_change" />
            <input type="text" placeholder="<?= lang('governorate_ph') ?>" required class="form_input input_address_boycott_change " />
            <input type="text" placeholder="<?= lang('zip_code_ph') ?>" required class="form_input input_address_postal_change" />
          </div>
          <div class="box_change_btn">
            <button class="change_modal_btn done_change_btn" id="btn-update-address"><?= lang('confirm_btn') ?></button>
            <button class="change_modal_btn close_change_btn" id="btn-cancel-edit"><?= lang('cancel_btn') ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container_modal popup" id="modal-success">
    <div class="popup_content">
      <i class="fa-solid fa-circle-check popup_icon" style="font-size: 70px; color: #10b981;"></i>
      <p class="popup_p" style="font-size: 24px; font-weight: bold; margin: 20px 0;"><?= lang('order_success_msg') ?></p>
    </div>
    <button class="popup_btn" style="background: #10b981; color: white; border: none; padding: 15px 40px; border-radius: 40px; font-size: 20px; font-weight: bold; cursor: pointer;" onclick="localStorage.removeItem('cards'); window.location.href='/my-orders'"><?= lang('ok_btn') ?></button>
  </div>
</div>

<div class="layer"></div>

<script> const BASE_SHIPPING_COST = <?php echo floatval($site_settings['shipping_cost'] ?? 0); ?>; </script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 1. بناء السلة ---
        const reviewContainer = document.getElementById('review-products-container');
        let cartItems = [];
        try {
            const stored = localStorage.getItem('cards');
            if (stored) cartItems = JSON.parse(stored);
        } catch (e) { cartItems = []; }

        const displayShippingCost = document.getElementById('display-shipping-cost');
        const finalTotalElements = document.querySelectorAll('.final-total-price');
        const subTotalElements = document.querySelectorAll('.cart-total-price');
        
        let calculatedSubTotal = 0;
        const baseShippingCost = typeof BASE_SHIPPING_COST !== 'undefined' ? BASE_SHIPPING_COST : 0;

        if (reviewContainer && cartItems.length > 0) {
            cartItems.forEach(item => {
                let rawImg = item.img || item.image || item.image_url || item.imgSrc || item.productImg || item.src || 'images/logos/logo.png';
                let productImg = rawImg.startsWith('http') ? rawImg : '<?= BASE_URL ?>' + rawImg.replace(/^\/+/, '');
                let productTitle = item.title || item.name || item.productName || '<?= lang("electronic_product") ?>';
                let productId = item.id || item.productId || item.product_id || item.Id || item.ID;
                let qty = parseInt(item.number || item.quantity || item.qty || 1);

                let cleanPriceString = (item.price || "0").toString().replace(/,/g, '');
                let numericPrice = parseFloat(cleanPriceString.replace(/[^\d.]/g, '')) || 0;
                calculatedSubTotal += (numericPrice * qty);

                reviewContainer.innerHTML += `
                    <div class="checkout-item" style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; transition: 0.3s;">
                        <a href="${productId ? '/product?id=' + productId : 'javascript:void(0);'}" style="display: flex; align-items: center; gap: 15px; text-decoration: none; cursor: pointer;">
                            <img src="${productImg}" style="width: 65px; height: 65px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; padding: 5px; background: #fff; transition: 0.3s;">
                            <div>
                                <div class="card_title_wrapper" style="margin-bottom: 5px;">
                                    <p style="margin: 0; font-weight: bold; color: #0f172a; font-size: 16px;">${productTitle}</p>
                                </div>
                                <p style="margin: 0; font-size: 14px; color: #64748b;"><?= lang('quantity_text') ?>${qty}</p>
                            </div>
                        </a>
                        <div style="font-weight: bold; color: #f97316; font-size: 17px;">${(numericPrice * qty).toFixed(2)} <?= lang('currency_egp') ?></div>
                    </div>
                `;
            });
            subTotalElements.forEach(el => { el.textContent = calculatedSubTotal.toFixed(2) + ' <?= lang("currency_egp") ?>'; });
        }

        const updateFinalTotal = () => {
            let currentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';
            let currentShipping = (currentMethod === 'online_card' || currentMethod === 'online_wallet') ? 0 : baseShippingCost;
            
            if (displayShippingCost) {
                displayShippingCost.textContent = currentShipping > 0 ? currentShipping.toFixed(2) + ' <?= lang("currency_egp") ?>' : '<?= lang("free_text") ?>';
            }
            
            let baseSubTotal = calculatedSubTotal;
            const localTotalStr = window.localStorage.getItem("total_Price");
            if (localTotalStr) {
                baseSubTotal = parseFloat(localTotalStr.replace(/[^\d.]/g, '')) || calculatedSubTotal;
            }
            
            let finalTotal = baseSubTotal + currentShipping;
            finalTotalElements.forEach(el => { el.textContent = finalTotal.toFixed(2) + ' <?= lang("currency_egp") ?>'; });
        };

        updateFinalTotal();

        // --- 2. ستايل أزرار طرق الدفع ---
        const paymentLabels = document.querySelectorAll('.payment-methods-container label');
        paymentLabels.forEach(label => {
            label.addEventListener('click', () => {
                const radio = label.querySelector('input[type="radio"]');
                if(radio) radio.checked = true;
                
                paymentLabels.forEach(lbl => {
                    lbl.style.borderColor = '#e2e8f0';
                    lbl.style.background = '#fff';
                });
                label.style.borderColor = 'var(--main-color)';
                label.style.background = '#f8fafc';
                
                updateFinalTotal();
            });
        });

        // --- 3. منطق العنوان المنبثق ---
        const layer = document.querySelector(".layer");
        const modalAdd = document.getElementById("modal-add-address");
        const modalEdit = document.getElementById("modal-edit-address");
        const addressDiv = document.querySelector(".address");
        
        // إظهار زر الإضافة أول مرة
        if (addressDiv && addressDiv.innerHTML.trim() === "") {
            if (savedAddress) {
                document.querySelector(".input_user").value = savedAddress.full_name || "";
                document.querySelector(".input_tel").value = savedAddress.phone || "";
                document.querySelector(".input_address_street").value = savedAddress.address_line1 || "";
                document.querySelector(".input_address_unit").value = savedAddress.address_line2 || "";
                document.querySelector(".input_address_city").value = savedAddress.city || "";
                document.querySelector(".input_address_boycott").value = savedAddress.governorate || "";
                document.querySelector(".input_address_postal").value = savedAddress.zip_code || "";

                addressDiv.innerHTML = `
                    <div class="address_details" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <h4 class="title_payment"><?= lang('shipping_address_title') ?></h4>
                        <span class="change_address" id="btn-open-edit-modal" style="color:var(--main-color); cursor:pointer; font-weight:bold;"><i class="fa-solid fa-pen"></i> <?= lang('edit_text') ?></span>
                    </div>
                    <div class="content_address" style="background:#f8fafc; padding:15px; border-radius:8px; line-height:1.8; border:1px solid #e2e8f0;">
                        <h5 id="user-Address" style="margin:0; font-size:16px; color:#0f172a;">${savedAddress.full_name}</h5>
                        <span id="phone-Address" style="display:block; color:#64748b;">${savedAddress.phone}</span>
                        <hr style="border:0; border-top:1px solid #e2e8f0; margin:10px 0;">
                        <span id="street-Address">${savedAddress.address_line1}</span>، 
                        <span id="unity-Address">${savedAddress.address_line2}</span><br>
                        <span id="city-Address">${savedAddress.city}</span> - 
                        <span id="boycott-Address">${savedAddress.governorate}</span><br>
                        <span style="color:#64748b;"><?= lang('zip_code_label') ?></span><span id="postal-Address">${savedAddress.zip_code}</span>
                    </div>
                `;

                setTimeout(() => {
                    const changeAddressBtn = document.getElementById("btn-open-edit-modal");
                    if (changeAddressBtn) {
                        changeAddressBtn.addEventListener("click", () => {
                            modalEdit.classList.add("modal_active");
                            layer.classList.add("layer_active");
                            
                            document.querySelector(".input_user_change").value = document.getElementById("user-Address").textContent;
                            document.querySelector(".input_tel_change").value = document.getElementById("phone-Address").textContent;
                            document.querySelector(".input_address_street_change").value = document.getElementById("street-Address").textContent;
                            document.querySelector(".input_address_unit_change").value = document.getElementById("unity-Address").textContent;
                            document.querySelector(".input_address_city_change").value = document.getElementById("city-Address").textContent;
                            document.querySelector(".input_address_boycott_change").value = document.getElementById("boycott-Address").textContent;
                            document.querySelector(".input_address_postal_change").value = document.getElementById("postal-Address").textContent;
                        });
                    }
                }, 100);
            } else {
                addressDiv.innerHTML = `
                  <h4 class="title_payment"><?= lang('shipping_address_title') ?></h4>
                  <div class="box_address" id="btn-open-add-modal" style="cursor:pointer; background:#f8fafc; padding:15px; border-radius:8px; border:2px dashed #cbd5e1; text-align:center;">
                    <span style="color:#3b82f6; font-weight:bold;"><i class="fa-solid fa-plus"></i> <?= lang('add_shipping_address') ?></span>
                  </div>
                `;
                setTimeout(() => {
                    const boxAddress = document.getElementById("btn-open-add-modal");
                    if (boxAddress) {
                        boxAddress.addEventListener("click", () => {
                            modalAdd.classList.add("modal_active");
                            layer.classList.add("layer_active");
                        });
                    }
                }, 100);
            }
        }

        // إغلاق النوافذ
        document.querySelectorAll(".close_modal").forEach(btn => {
            btn.addEventListener("click", () => {
                document.querySelectorAll(".container_modal").forEach(m => m.classList.remove("modal_active"));
                layer.classList.remove("layer_active");
            });
        });

        // التحقق من الحقول
        function checkInputs(inputElements) {
            let hasError = false;
            inputElements.forEach(input => {
                if (!input) return;
                if(input.value.trim() === "") {
                    input.style.borderColor = "#ef4444";
                    hasError = true;
                } else {
                    input.style.borderColor = "#e2e8f0";
                }
                input.addEventListener("keyup", () => {
                    input.style.borderColor = input.value.trim() === "" ? "#ef4444" : "#e2e8f0";
                });
            });
            return hasError;
        }

        // حفظ العنوان الجديد
        const btnSaveAddress = document.getElementById("btn-save-address");
        if (btnSaveAddress) {
            btnSaveAddress.addEventListener("click", (e) => {
                e.preventDefault();
                const inputs = [
                    document.querySelector(".input_user"), document.querySelector(".input_tel"),
                    document.querySelector(".input_address_street"), document.querySelector(".input_address_unit"),
                    document.querySelector(".input_address_city"), document.querySelector(".input_address_boycott"),
                    document.querySelector(".input_address_postal")
                ];

                if (checkInputs(inputs)) return;

                modalAdd.classList.remove("modal_active");
                layer.classList.remove("layer_active");

                addressDiv.innerHTML = `
                    <div class="address_details" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <h4 class="title_payment"><?= lang('shipping_address_title') ?></h4>
                        <span class="change_address" id="btn-open-edit-modal" style="color:var(--main-color); cursor:pointer; font-weight:bold;"><i class="fa-solid fa-pen"></i> <?= lang('edit_text') ?></span>
                    </div>
                    <div class="content_address" style="background:#f8fafc; padding:15px; border-radius:8px; line-height:1.8; border:1px solid #e2e8f0;">
                        <h5 id="user-Address" style="margin:0; font-size:16px; color:#0f172a;">${inputs[0].value}</h5>
                        <span id="phone-Address" style="display:block; color:#64748b;">${inputs[1].value}</span>
                        <hr style="border:0; border-top:1px solid #e2e8f0; margin:10px 0;">
                        <span id="street-Address">${inputs[2].value}</span>، 
                        <span id="unity-Address">${inputs[3].value}</span><br>
                        <span id="city-Address">${inputs[4].value}</span> - 
                        <span id="boycott-Address">${inputs[5].value}</span><br>
                        <span style="color:#64748b;"><?= lang('zip_code_label') ?></span><span id="postal-Address">${inputs[6].value}</span>
                    </div>
                `;

                setTimeout(() => {
                    const changeAddressBtn = document.getElementById("btn-open-edit-modal");
                    if (changeAddressBtn) {
                        changeAddressBtn.addEventListener("click", () => {
                            modalEdit.classList.add("modal_active");
                            layer.classList.add("layer_active");
                            
                            document.querySelector(".input_user_change").value = document.getElementById("user-Address").textContent;
                            document.querySelector(".input_tel_change").value = document.getElementById("phone-Address").textContent;
                            document.querySelector(".input_address_street_change").value = document.getElementById("street-Address").textContent;
                            document.querySelector(".input_address_unit_change").value = document.getElementById("unity-Address").textContent;
                            document.querySelector(".input_address_city_change").value = document.getElementById("city-Address").textContent;
                            document.querySelector(".input_address_boycott_change").value = document.getElementById("boycott-Address").textContent;
                            document.querySelector(".input_address_postal_change").value = document.getElementById("postal-Address").textContent;
                        });
                    }
                }, 100);
            });
        }

        // إلغاء التعديل
        const btnCancelEdit = document.getElementById("btn-cancel-edit");
        if (btnCancelEdit) {
            btnCancelEdit.addEventListener("click", (e) => {
                e.preventDefault();
                modalEdit.classList.remove("modal_active");
                layer.classList.remove("layer_active");
            });
        }

        // تأكيد التعديل
        const btnUpdateAddress = document.getElementById("btn-update-address");
        if (btnUpdateAddress) {
            btnUpdateAddress.addEventListener("click", (e) => {
                e.preventDefault();
                const inputsChange = [
                    document.querySelector(".input_user_change"), document.querySelector(".input_tel_change"),
                    document.querySelector(".input_address_street_change"), document.querySelector(".input_address_unit_change"),
                    document.querySelector(".input_address_city_change"), document.querySelector(".input_address_boycott_change"),
                    document.querySelector(".input_address_postal_change")
                ];

                if (checkInputs(inputsChange)) return;

                modalEdit.classList.remove("modal_active");
                layer.classList.remove("layer_active");

                document.getElementById("user-Address").textContent = inputsChange[0].value;
                document.getElementById("phone-Address").textContent = inputsChange[1].value;
                document.getElementById("street-Address").textContent = inputsChange[2].value;
                document.getElementById("unity-Address").textContent = inputsChange[3].value;
                document.getElementById("city-Address").textContent = inputsChange[4].value;
                document.getElementById("boycott-Address").textContent = inputsChange[5].value;
                document.getElementById("postal-Address").textContent = inputsChange[6].value;
            });
        }

        // --- 4. معالجة الدفع وإنهاء الطلب ---
        const orderBtn = document.querySelector(".order_btn");
        if (orderBtn) {
            orderBtn.addEventListener("click", () => {
                let userAddressEl = document.getElementById("user-Address");
                if (!userAddressEl || userAddressEl.textContent.trim() === "") {
                    modalAdd.classList.add("modal_active");
                    layer.classList.add("layer_active");
                    return;
                }

                if (!cartItems || cartItems.length === 0) {
                    alert("<?= lang('cart_empty_alert') ?>");
                    window.location.href = "/products";
                    return;
                }

                const formData = new FormData();
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                formData.append("csrf_token", csrfToken);
                formData.append("full_name", document.getElementById("user-Address").textContent);
                formData.append("phone", document.getElementById("phone-Address").textContent);
                formData.append("address_line1", document.getElementById("street-Address").textContent);
                formData.append("address_line2", document.getElementById("unity-Address").textContent);
                formData.append("city", document.getElementById("city-Address").textContent);
                formData.append("governorate", document.getElementById("boycott-Address").textContent);
                formData.append("zip_code", document.getElementById("postal-Address").textContent);
                
                const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';
                formData.append("payment_method", selectedPaymentMethod);
                
                const activeCoupon = JSON.parse(localStorage.getItem('activeCoupon') || 'null');
                if (activeCoupon) {
                    formData.append("applied_promo_code", activeCoupon.code);
                }

                formData.append("products", JSON.stringify(cartItems.map(item => {
                    item.number = parseInt(item.number || item.quantity || item.qty || 1);
                    return item;
                })));

                orderBtn.disabled = true;
                orderBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <?= lang("processing_btn") ?>';
                
                fetch("/checkout/process", {
                    method: "POST",
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(async res => {
                    const text = await res.text();
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            if (data.redirect && data.redirect.includes('/payment/pay')) {
                                window.location.href = data.redirect;
                            } else {
                                localStorage.removeItem("cards");
                                localStorage.removeItem("total_Price");
                                localStorage.removeItem("activeCoupon");
                                
                                const popup = document.getElementById("modal-success");
                                if (popup) {
                                    popup.classList.add("modal_active");
                                    layer.classList.add("layer_active");
                                } else {
                                    window.location.href = data.redirect || "/my-orders";
                                }
                            }
                        } else {
                            alert("<?= lang('order_error_msg') ?>" + (data.error || ""));
                            orderBtn.disabled = false;
                            orderBtn.textContent = "<?= lang('confirm_order_btn') ?>";
                        }
                    } catch (e) {
                        alert("<?= lang('server_error_msg') ?>");
                        orderBtn.disabled = false;
                        orderBtn.textContent = "<?= lang('confirm_order_btn') ?>";
                    }
                })
                .catch(err => {
                    alert("<?= lang('connection_error_msg') ?>" + err.message);
                    orderBtn.disabled = false;
                    orderBtn.textContent = "<?= lang('confirm_order_btn') ?>";
                });
            });
        }
    });
</script>