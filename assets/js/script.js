// assets/js/script.js

// --- DATA PRODUK ---
const products = [
  {
    id: 1,
    name: "Sambal Bawang",
    price: 28000,
    img: "https://via.placeholder.com/400?text=Sambal+Bawang",
  },
  {
    id: 2,
    name: "Udang Crispy",
    price: 100000,
    img: "https://via.placeholder.com/400?text=Udang+Crispy",
  },
  {
    id: 3,
    name: "Spikoe Resep Kuno",
    price: 125000,
    img: "https://via.placeholder.com/400?text=Spikoe",
  },
  {
    id: 4,
    name: "Almond Crispy",
    price: 65000,
    img: "https://via.placeholder.com/400?text=Almond",
  },
  {
    id: 5,
    name: "Pecel Madiun",
    price: 25000,
    img: "https://via.placeholder.com/400?text=Pecel",
  },
  {
    id: 6,
    name: "Sambal Bajak",
    price: 30000,
    img: "https://via.placeholder.com/400?text=Sambal+Bajak",
  },
];

let cart = [];
let shippingCost = 0;
let isRegisterMode = false;

// --- INIT ---
document.addEventListener("DOMContentLoaded", () => {
  updateNav();
  const path = window.location.pathname;

  if (path.includes("index.html") || path.endsWith("/")) {
    renderProducts();
  } else if (path.includes("profile.html")) {
    loadProfileData();
  }
});

// --- AUTH SYSTEM ---
function updateNav() {
  const activeUser = localStorage.getItem("buRudyActiveUser");
  const navAuth = document.getElementById("nav-auth");

  // Pastikan elemen nav-auth ada sebelum diisi
  if (navAuth) {
    if (activeUser) {
      const user = JSON.parse(activeUser);
      // Tampilan SUDAH LOGIN: Ada Keranjang & Profil
      navAuth.innerHTML = `
                <div class="flex items-center gap-3">
                    <button onclick="toggleCart()" class="relative bg-gray-100 p-2 rounded-full hover:bg-gray-200 transition">
                        <i class="fas fa-shopping-cart text-gray-600"></i>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-red-700 text-white text-[10px] font-bold h-5 w-5 rounded-full flex items-center justify-center hidden border-2 border-white">0</span>
                    </button>
                    <a href="profile.html" class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-full hover:bg-gray-200 border border-gray-200 transition">
                        <i class="fas fa-user-circle text-xl text-gray-600"></i>
                        <span class="font-bold text-sm text-gray-800 hidden md:inline">${user.name}</span>
                    </a>
                </div>`;

      // PENTING: Restore keranjang lama kalau ada (Opsional, tapi bagus buat UX)
      // Di sini kita reset cart visualnya ke 0 dulu sesuai request 'simulasi'
    } else {
      // Arahkan langsung ke login.php
      navAuth.innerHTML = `
        <a href="login.php" class="bg-red-700 text-white px-5 py-2 rounded-full font-bold text-sm hover:bg-red-800 shadow-md transition decoration-none inline-block">
            Masuk / Daftar
        </a>`;
    }
  }
}

function handleAuth(e) {
  e.preventDefault();
  const u = document.getElementById("auth-username").value;
  const p = document.getElementById("auth-password").value;
  let usersDB = JSON.parse(localStorage.getItem("buRudyUsersDB")) || [];

  if (isRegisterMode) {
    const name = document.getElementById("reg-name").value;
    if (!name) return alert("Nama wajib diisi!");
    if (usersDB.find((user) => user.username === u))
      return alert("Username sudah dipakai!");

    const newUser = {
      username: u,
      password: p,
      name: name,
      email: u + "@mail.com",
      phone: "-",
      address: "-",
    };
    usersDB.push(newUser);
    localStorage.setItem("buRudyUsersDB", JSON.stringify(usersDB));
    alert("Daftar berhasil! Silakan login.");
    toggleMode();
  } else {
    const found = usersDB.find(
      (user) => user.username === u && user.password === p
    );
    if (found) {
      localStorage.setItem("buRudyActiveUser", JSON.stringify(found));
      alert(`Selamat datang, ${found.name}!`);
      closeModal();
      updateNav();
      if (window.location.pathname.includes("profile.html")) location.reload();
    } else {
      alert("Username atau Password salah!");
    }
  }
}

function handleLogout() {
  if (confirm("Yakin mau logout?")) {
    localStorage.removeItem("buRudyActiveUser");
    window.location.href = "index.html";
  }
}

// --- LOGIC KERANJANG (YANG KAMU MINTA) ---

function renderProducts() {
  const grid = document.getElementById("product-grid");
  if (!grid) return;

  grid.innerHTML = products
    .map(
      (p) => `
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 group hover:shadow-xl transition duration-300 flex flex-col h-full">
            <div class="h-48 bg-gray-100 mb-4 rounded-lg overflow-hidden relative">
                <img src="${
                  p.img
                }" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-lg text-gray-800">${p.name}</h3>
                <p class="text-red-600 font-bold mb-3">Rp ${p.price.toLocaleString()}</p>
            </div>
            <button onclick="addToCart(${
              p.id
            })" class="w-full bg-white border border-red-600 text-red-600 py-2 rounded-lg font-bold hover:bg-red-600 hover:text-white transition active:scale-95">
                + Keranjang
            </button>
        </div>
    `
    )
    .join("");
}

function addToCart(id) {
  // 1. Cek Login
  if (!localStorage.getItem("buRudyActiveUser")) {
    alert("Login dulu sayang kalau mau belanja! 😜");
    openModal("login");
    return;
  }

  // 2. Masukkan ke Array Cart
  const product = products.find((p) => p.id === id);
  cart.push(product);

  // 3. Update Angka di Navbar (Badge)
  updateCartUI();

  // 4. Feedback Simpel (Tanpa Buka Keranjang)
  // Kita pakai alert kecil atau console log biar gak ganggu,
  // tapi karena requestmu "masukin aja dulu", alert standar is okay.
  // Atau bisa dihapus kalau mau silent banget.
  alert(`✅ ${product.name} berhasil masuk keranjang!`);
}

function updateCartUI() {
  // Update Angka di Badge Merah
  const badge = document.getElementById("cart-badge");
  if (badge) {
    badge.innerText = cart.length;
    if (cart.length > 0) {
      badge.classList.remove("hidden"); // Munculin badge
      badge.classList.add("animate-bounce"); // Efek genit dikit biar sadar nambah
      setTimeout(() => badge.classList.remove("animate-bounce"), 1000);
    } else {
      badge.classList.add("hidden");
    }
  }

  // Render List Item di dalam Modal (Persiapan kalau nanti dibuka)
  const container = document.getElementById("cart-items");
  if (container) {
    if (cart.length === 0) {
      container.innerHTML = `
                <div class="text-center py-10">
                    <i class="fas fa-shopping-basket text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-400">Keranjang masih kosong.</p>
                </div>`;
    } else {
      container.innerHTML = cart
        .map(
          (item, index) => `
                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg mb-2 border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded overflow-hidden">
                            <img src="${
                              item.img
                            }" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-gray-800">${
                              item.name
                            }</h4>
                            <span class="text-xs text-red-600 font-bold">Rp ${item.price.toLocaleString()}</span>
                        </div>
                    </div>
                    <button onclick="removeFromCart(${index})" class="text-gray-400 hover:text-red-600 transition p-2">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `
        )
        .join("");
    }
  }

  calculateTotal();
}

function removeFromCart(index) {
  cart.splice(index, 1); // Hapus item
  updateCartUI(); // Update tampilan
}

function calculateTotal() {
  const subtotal = cart.reduce((sum, item) => sum + item.price, 0);
  const grandTotal = subtotal + shippingCost;

  const elSub = document.getElementById("val-subtotal");
  const elTotal = document.getElementById("val-total");

  if (elSub) elSub.innerText = `Rp ${subtotal.toLocaleString()}`;
  if (elTotal) elTotal.innerText = `Rp ${grandTotal.toLocaleString()}`;
}

function toggleCart() {
  const modal = document.getElementById("cart-modal");
  if (modal) modal.classList.toggle("hidden");
}

// --- API SIMULATION ---
async function checkOngkirAPI() {
  const city = document.getElementById("dest-city").value;
  const btn = document.getElementById("btn-ongkir");
  const info = document.getElementById("ongkir-info");

  if (!city) return alert("Pilih kota tujuan dulu!");

  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  btn.disabled = true;

  await new Promise((r) => setTimeout(r, 1000));

  const rates = { sby: 10000, jkt: 22000, bdg: 24000, bali: 30000 };
  shippingCost = rates[city] || 0;

  document.getElementById(
    "ongkir-val"
  ).innerText = `Rp ${shippingCost.toLocaleString()}`;
  document.getElementById(
    "val-ongkir"
  ).innerText = `Rp ${shippingCost.toLocaleString()}`;
  info.classList.remove("hidden");
  calculateTotal();

  btn.innerHTML = "Cek";
  btn.disabled = false;
}

async function payWithMidtrans() {
  if (cart.length === 0) return alert("Keranjang kosong!");
  if (shippingCost === 0) return alert("Cek ongkir dulu!");

  const btn = document.getElementById("btn-pay");

  const confirmPay = confirm(
    `Total Tagihan: ${
      document.getElementById("val-total").innerText
    }\nLanjutkan Pembayaran?`
  );

  if (confirmPay) {
    btn.innerHTML = "Memproses Pembayaran...";
    btn.classList.add("opacity-50");

    await new Promise((r) => setTimeout(r, 2000));

    alert("✅ PEMBAYARAN BERHASIL!\nTerima kasih.");

    cart = [];
    shippingCost = 0;
    updateCartUI();
    toggleCart();

    btn.innerHTML = "Bayar Sekarang";
    btn.classList.remove("opacity-50");
  }
}

// --- MODAL UTILS ---
function openModal(mode) {
  document.getElementById("auth-modal").classList.remove("hidden");
  isRegisterMode = mode === "register";
  renderModalUI();
}
function closeModal() {
  document.getElementById("auth-modal").classList.add("hidden");
}
function toggleMode() {
  isRegisterMode = !isRegisterMode;
  renderModalUI();
}
function renderModalUI() {
  const title = document.getElementById("modal-title");
  const btn = document.getElementById("btn-submit");
  const toggle = document.getElementById("toggle-text");
  const fieldName = document.getElementById("field-name");
  if (isRegisterMode) {
    title.innerText = "Daftar Akun Baru";
    btn.innerText = "Daftar";
    fieldName.classList.remove("hidden");
    toggle.innerHTML = `Sudah punya akun? <button onclick="toggleMode()" class="text-red-700 font-bold hover:underline">Login</button>`;
  } else {
    title.innerText = "Masuk Akun";
    btn.innerText = "Masuk";
    fieldName.classList.add("hidden");
    toggle.innerHTML = `Belum punya akun? <button onclick="toggleMode()" class="text-red-700 font-bold hover:underline">Daftar</button>`;
  }
}

function loadProfileData() {
  const activeUser = localStorage.getItem("buRudyActiveUser");
  if (!activeUser) {
    alert("Anda belum login!");
    window.location.href = "index.html";
    return;
  }
  const user = JSON.parse(activeUser);
  document.getElementById("display-name").innerText = user.name;
  document.getElementById("display-username").innerText = "@" + user.username;
  document.getElementById("input-name").value = user.name;
  document.getElementById("input-email").value = user.email;
  document.getElementById("input-phone").value = user.phone;
  document.getElementById("input-address").value = user.address;
}

function saveProfile(e) {
  e.preventDefault();
  const newName = document.getElementById("input-name").value;
  const newPhone = document.getElementById("input-phone").value;
  const newAddress = document.getElementById("input-address").value;
  let currentUser = JSON.parse(localStorage.getItem("buRudyActiveUser"));
  let usersDB = JSON.parse(localStorage.getItem("buRudyUsersDB"));

  currentUser.name = newName;
  currentUser.phone = newPhone;
  currentUser.address = newAddress;
  localStorage.setItem("buRudyActiveUser", JSON.stringify(currentUser));

  const idx = usersDB.findIndex((u) => u.username === currentUser.username);
  if (idx !== -1) {
    usersDB[idx] = currentUser;
    localStorage.setItem("buRudyUsersDB", JSON.stringify(usersDB));
  }

  document.getElementById("display-name").innerText = newName;
  alert("Profil berhasil diperbarui!");
}
