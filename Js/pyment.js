const orderBtn = document.querySelector(".order_btn");
const modal = document.querySelector(".container_modal");
const layer = document.querySelector(".layer");
const closeModal = document.querySelector(".close_modal");
const addressDiv = document.querySelector(".address");

const sendBtn = document.querySelector(".send_btn");

const inputs = document.querySelectorAll(".form_input");

const userInput = document.querySelector(".input_user");
const phoneInput = document.querySelector(".input_tel");
const streetInput = document.querySelector(".input_address_street");
const unityInput = document.querySelector(".input_address_unit");
const cityInput = document.querySelector(".input_address_city");
const boycottInput = document.querySelector(".input_address_boycott");
const postalInput = document.querySelector(".input_address_postal");

const userChange = document.querySelector(".input_user_change");
const phoneChange = document.querySelector(".input_tel_change");
const streetChange = document.querySelector(".input_address_street_change");
const unityChange = document.querySelector(".input_address_unit_change");
const postalChange = document.querySelector(".input_address_city_change");
const cityChange = document.querySelector(".input_address_boycott_change");
const boycottChange = document.querySelector(".input_address_postal_change");

const paymentCard = document.getElementById("card-payment");
const closeCardBtn = document.getElementById("close_visa_card");
const modalCard = document.querySelector(".modal_card");

const popup = document.querySelector(".popup");
const popupBtn = document.querySelector(".popup_btn");

const orderTotal = document.querySelectorAll(".order_total");

orderTotal.forEach((ele) => {
  ele.textContent = window.localStorage.getItem("total_Price") + " جنيه";
});

paymentCard.addEventListener("click", () => {
  modalCard.classList.add("modal_active");
  layer.classList.add("layer_active");
});

closeCardBtn.addEventListener("click", () => {
  modalCard.classList.remove("modal_active");
  layer.classList.remove("layer_active");
});

orderBtn.addEventListener("click", () => {
  if (addressDiv.innerHTML === "") {
    modal.classList.add("modal_active");
    layer.classList.add("layer_active");
  } else {
    const cartItems = localStorage.getItem("cards");
    if (!cartItems || JSON.parse(cartItems).length === 0) {
      alert("عربة التسوق فارغة!");
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
    formData.append("total_price", window.cartTotalValue || window.localStorage.getItem("total_Price"));
    formData.append("products", cartItems);

    orderBtn.disabled = true;
    orderBtn.textContent = "جاري التنفيذ...";

    let finalPrice = window.cartTotalValue || window.localStorage.getItem("total_Price");
    if (typeof finalPrice === 'string') {
        finalPrice = finalPrice.replace(/[^\d.]/g, '');
    }
    formData.set("total_price", finalPrice || 0);

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
            window.location.href = data.redirect;
          } else {
            alert("حدث خطأ أثناء تسجيل الطلب: " + (data.error || ""));
            orderBtn.disabled = false;
            orderBtn.textContent = "تأكيد الطلب";
          }
      } catch (e) {
          console.error("Server response:", text);
          alert("فشل استجابة السيرفر. راجع الكونسول (F12) لمعرفة السبب.");
          orderBtn.disabled = false;
          orderBtn.textContent = "تأكيد الطلب";
      }
    })
    .catch(err => {
      alert("حدث خطأ في الاتصال: " + err.message);
      orderBtn.disabled = false;
      orderBtn.textContent = "تأكيد الطلب";
    });
  }
});

closeModal.addEventListener("click", () => {
  modal.classList.remove("modal_active");
  layer.classList.remove("layer_active");
  
  inputsEmpty(inputs);
  
  inputs.forEach((input) => {
    input.style.borderColor = "#a9a9a9";
  });
});

sendBtn.addEventListener("click", () => {
  if (
    userInput.value === "" ||
    phoneInput.value === "" ||
    streetInput.value === "" ||
    cityInput.value === "" ||
    boycottInput.value === ""
  ) {
    chickInputs(inputs);
    return;
  } else {
    addAddress();

    const changeBtn = document.querySelector(".change_btn");
    changeBtn.addEventListener("click", () => {
      const modalChange = document.querySelector(".modal_change");
      const closeChangeBtn = document.querySelector(".close_change_btn");
      const doneChangeBtn = document.querySelector(".done_change_btn");

      modalChange.classList.add("modal_active");
      layer.classList.add("layer_active");
      
      getData();
      
      chickInputs(inputs);

      closeChangeBtn.addEventListener("click", () => {
        modalChange.classList.remove("modal_active");
        layer.classList.remove("layer_active");
      });

      doneChangeBtn.addEventListener("click", () => {
        if (
          userChange.value === "" ||
          phoneChange.value === "" ||
          streetChange.value === "" ||
          cityChange.value === "" ||
          boycottChange.value === ""
        ) {
          chickInputs(inputs);
          return;
        } else {
          document.getElementById("user-Address").textContent = userChange.value;
          document.getElementById("phone-Address").textContent = phoneChange.value;
          document.getElementById("street-Address").textContent = streetChange.value;
          document.getElementById("unity-Address").textContent = unityChange.value;
          document.getElementById("postal-Address").textContent = postalChange.value;
          document.getElementById("city-Address").textContent = cityChange.value;
          document.getElementById("boycott-Address").textContent = boycottChange.value;

          modalChange.classList.remove("modal_active");
          layer.classList.remove("layer_active");
        }
      });
    });

    orderBtn.innerHTML = "تأكيد الطلب";
    
    inputsEmpty(inputs);

    modal.classList.remove("modal_active");
    layer.classList.remove("layer_active");
  }
});

popupBtn.addEventListener("click", () => {
  popup.classList.remove("modal_active");
  layer.classList.remove("layer_active");
  orderBtn.textContent = "إكمال الطلب";
  document.getElementById("item-payment").innerHTML = "";
  addressDiv.innerHTML = "";
  window.localStorage.clear();
  window.location.href = "my_orders.php";
});

function inputsEmpty(inputs) {
  inputs.forEach((input) => (input.value = ""));
}

function addAddress() {
  addressDiv.innerHTML = `
  <h4 class="title_payment">عنوان التسليم</h4>
        <div class="details_payment">
          <div>
            <p class="address_payment">
              <span id='user-Address'>${userInput.value}</span>
              <span> , </span>
              <span id='phone-Address'>${phoneInput.value}</span>
            </p>

            <p class="address_payment">
              <span id='street-Address'>${streetInput.value}</span>
              <span> / </span>
              <span id='unity-Address'>${unityInput.value}</span>
            </p>

            <p class="address_payment">
              <span id='postal-Address'>${postalInput.value}</span>
              <span> , </span>
              <span id='city-Address'>${cityInput.value}</span>
              <span> - </span>
              <span id='boycott-Address'>${boycottInput.value}</span>
              <span> - </span>
              <span>مصر</span>
            </p>
          </div>

          <a href="javascript:;" class="change_btn">تغير</a>
        </div>
  `;
}

function getData() {
  userChange.value = document.getElementById("user-Address").textContent;
  phoneChange.value = document.getElementById("phone-Address").textContent;
  streetChange.value = document.getElementById("street-Address").textContent;
  unityChange.value = document.getElementById("unity-Address").textContent;
  postalChange.value = document.getElementById("postal-Address").textContent;
  cityChange.value = document.getElementById("city-Address").textContent;
  boycottChange.value = document.getElementById("boycott-Address").textContent;
}

function chickInputs(inputs) {
  inputs.forEach((input) => {
    if (input.value === "") {
      input.style.borderColor = "#d9534f";
    } else {
      input.style.borderColor = "#a9a9a9";
    }
    input.addEventListener("keyup", (e) => {
      if (input.value.length > 0) {
        input.style.borderColor = "#a9a9a9";
      }
    });
  });
}