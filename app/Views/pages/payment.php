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
    
    .container_modal { border-radius: 25px !important; padding: 35px !important; }
    
    .item_payment.address:empty {
        display: none !important;
    }
</style>



<div class="container_payment">
  <div class="content_payment">
    <div class="item_payment address"></div>
    
    <div class="item_payment" id="order-review-section">
      <h4 class="title_payment">المنتجات في طلبك</h4>
      <div id="review-products-container" style="display: flex; flex-direction: column; gap: 15px;"></div>
    </div>

    <div class="item_payment">
      <h4 class="title_payment">طرق السداد</h4>
      <div class="cards_payment">
        <div class="card_payment" id="card-payment">
          <img src="/images/payment/payMent.png" class="card_payment_img" />
          <span class="card_payment_p">إضافة بطاقة جديدة</span>
        </div>
        <div class="card_payment">
          <img src="/images/payment/payment_1.png" class="visa_img" />
          <img src="/images/payment/payment_2.png" class="visa_img" />
          <img src="/images/payment/payment_3.png" class="visa_img" />
          <img src="/images/payment/payment_4.png" class="visa_img" />
        </div>
      </div>
    </div>
  </div>

  <div class="content_payment">
    <div class="item_payment" id="item-payment">
      <h4 class="title_payment">الملخص</h4>
      <div class="boxs_order_total">
        <div class="box_order_total">
          <span>إجمالي الطلب</span>
          <span class="order_total cart-total-price"></span>
        </div>
        <div class="box_order_total">
          <span>تكاليف الشحن</span>
          <span>مجاني</span>
        </div>
      </div>
      <div class="boxs_order_total">
        <div class="box_order_total">
          <span>الإجمالي</span>
          <span class="order_total cart-total-price"></span>
        </div>
        <button class="order_btn">تأكيد الطلب</button>
      </div>
    </div>
    <div class="item_payment item_payment_safety">
      <img src="/images/logos/logo.png" alt="Safety Logo" />
      <p>يحافظ MY Store على أمان معلوماتك ومدفوعاتك</p>
      <img src="/images/payment/payment_5.png" alt="Safety" />
    </div>
  </div>

  <div class="container_modal">
    <div class="modal_header">
      <h4 class="modal_title">إضافة عنوان الشحن</h4>
    </div>
    <div class="modal_body">
      <div class="form_section">
        <?= CSRF::getField() ?>
        <input type="hidden" name="products" id="hidden-products" value="[]">
        <input type="hidden" name="total_price" id="hidden-total-price" value="0">
        <div class="form_body">
          <div class="form_title">البيانات الشخصية</div>
          <div class="form_box_modal">
            <input type="text" name="full_name" placeholder="* اسم العميل" required class="form_input input_user" />
            <input type="text" name="phone" placeholder="* رقم الهاتف" required class="form_input input_tel" />
          </div>
          <div class="form_title">العنوان</div>
          <div class="form_box_modal">
            <input type="text" name="address_line1" placeholder="* الشارع، المنزل/الشقة/الوحدة السكنية" required class="form_input input_address_street" />
            <input type="text" name="address_line2" placeholder="* الشقة، الجناح، الوحدة، إلخ..." required class="form_input input_address_unit" />
          </div>
          <div class="form_box_modal">
            <input type="text" name="city" placeholder="* المدينة" required class="form_input input_address_city" />
            <input type="text" name="governorate" placeholder="* المحافظة" required class="form_input input_address_boycott" />
            <input type="text" name="zip_code" placeholder="* الرقم البريدي" required class="form_input input_address_postal" />
          </div>
          <div class="form_box_modal">
            <input type="button" value="إضافة عنوان الشحن" class="send_btn" />
          </div>
          <i class="fa-solid fa-xmark close_modal"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="container_modal modal_change">
    <div class="modal_header">
      <h4 class="modal_title">تعديل عنوان الشحن</h4>
    </div>
    <div class="modal_body">
      <div class="form_section">
        <div class="form_body">
          <div class="form_title">البيانات الشخصية</div>
          <div class="form_box_modal">
            <input type="text" placeholder="* اسم العميل" required class="form_input input_user_change" />
            <input type="text" placeholder="* رقم الهاتف" required class="form_input input_tel_change" />
          </div>
          <div class="form_title">العنوان</div>
          <div class="form_box_modal">
            <input type="text" placeholder="* الشارع، المنزل/الشقة/الوحدة السكنية" required class="form_input input_address_street_change" />
            <input type="text" placeholder="* الشقة، الجناح، الوحدة، إلخ..." required class="form_input input_address_unit_change" />
          </div>
          <div class="form_box_modal">
            <input type="text" placeholder="* المدينة" required class="form_input input_address_city_change" />
            <input type="text" placeholder="* المحافظة" required class="form_input input_address_boycott_change " />
            <input type="text" placeholder="* الرقم البريدي" required class="form_input input_address_postal_change" />
          </div>
          <div class="box_change_btn">
            <button class="change_modal_btn done_change_btn">تأكيد</button>
            <button class="change_modal_btn close_change_btn">إلغاء</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container_modal modal_card">
    <div class="modal_header">
      <h4 class="modal_title_card">قدِّم مزيدًا من المعلومات</h4>
      <div class="description_visa_card">
        <img src="/images/payment/payment_s.png" alt="">
        <span>معلومات الدفع الخاصة بك في أمان معنا</span>
      </div>
      <div class="modal_row">
        <img src="/images/payment/payMent.png" alt="" class="card_payment_img">
        <span>إضافة بطاقة جديدة</span>
        <img src="/images/payment/payment_1.png" alt="" class="visa_img">
        <img src="/images/payment/payment_2.png" alt="" class="visa_img">
        <img src="/images/payment/payment_3.png" alt="" class="visa_img">
        <img src="/images/payment/payment_4.png" alt="" class="visa_img">
      </div>
    </div>
    <div class="modal_body">
      <div class="form_section">
        <div class="form_body">
          <div class="form_box_modal">
            <input type="text" placeholder="رقم البطاقة" class="form_input">
            <input type="text" placeholder="اسم صاحب البطاقة" class="form_input">
          </div>
          <div class="form_box_modal">
            <input type="date"  class="form_input">
            <input type="text" placeholder="CVV" class="form_input">
          </div>
          <div class="form_button_section">
            <input type="button" value="حفظ وتأكيد" class="send_btn visa_card_btn">
          </div>
        </div>
      </div>
      <i class="fa-solid fa-xmark close_modal" id="close_visa_card"></i>
    </div>
  </div>

  <div class="container_modal popup">
    <div class="popup_content">
      <i class="fa-solid fa-circle-check popup_icon" style="font-size: 70px; color: #10b981;"></i>
      <p class="popup_p" style="font-size: 24px; font-weight: bold; margin: 20px 0;">تم تأكيد الطلب بنجاح</p>
    </div>
    <button class="popup_btn" style="background: #10b981; color: white; border: none; padding: 15px 40px; border-radius: 40px; font-size: 20px; font-weight: bold; cursor: pointer;" onclick="localStorage.removeItem('cards'); window.location.href='/my-orders'">حسناً</button>
  </div>
</div>

<div class="layer"></div>

<script src="/Js/pyment.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cartItemsStr = localStorage.getItem('cards');
        let cartTotal = 0;
        if(cartItemsStr){
            try {
                const cartItems = JSON.parse(cartItemsStr);
                cartTotal = cartItems.reduce((sum, item) => {
                    let price = parseFloat(item.price.replace(/[^\d.]/g, ''));
                    let qty = parseInt(item.number || 1);
                    return sum + (price * qty);
                }, 0);
            } catch(e){}
        }
        
        document.querySelectorAll('.cart-total-price').forEach(el => {
            el.textContent = cartTotal.toFixed(2) + ' جنيه';
        });
        window.cartTotalValue = cartTotal;
        const hiddenProducts = document.getElementById('hidden-products');
     const hiddenTotal = document.getElementById('hidden-total-price');
     if (hiddenProducts) hiddenProducts.value = cartItemsStr;
     if (hiddenTotal) hiddenTotal.value = cartTotal;

        const reviewContainer = document.getElementById('review-products-container');
        if (reviewContainer && cartItemsStr) {
            const cartItems = JSON.parse(cartItemsStr);
            cartItems.forEach(item => {
                let productImg = item.img || item.image || item.image_url || item.imgSrc || item.productImg || item.src || '/images/logos/logo.png';
                let productTitle = item.title || item.name || item.productName || 'منتج إلكتروني';
                let productId = item.id || item.productId || item.product_id || item.Id || item.ID;

               reviewContainer.innerHTML += `
                 <div class="checkout-item" style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; transition: 0.3s;">
                     <a href="${productId ? '/product?id=' + productId : 'javascript:void(0);'}" style="display: flex; align-items: center; gap: 15px; text-decoration: none; cursor: pointer;">
                         <img src="${productImg}" style="width: 65px; height: 65px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; padding: 5px; background: #fff; transition: 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" title="عرض تفاصيل المنتج">
                         <div>
                             <div class="card_title_wrapper" style="margin-bottom: 5px;">
                                 <p style="margin: 0; font-weight: bold; color: #0f172a; font-size: 16px; transition: color 0.3s;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#0f172a'">${productTitle}</p>
                             </div>
                             <p style="margin: 0; font-size: 14px; color: #64748b;">الكمية: ${item.number || 1}</p>
                         </div>
                     </a>
                     <div style="font-weight: bold; color: #f97316; font-size: 17px;">${item.price}</div>
                 </div>
             `;
            });
        }
    });
</script>