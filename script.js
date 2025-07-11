// Fetch products from backend and render
async function fetchAndRenderProducts() {
  try {
    const response = await fetch('products.php');
    const products = await response.json();
    renderProducts(products);
  } catch (error) {
    console.error('Failed to fetch products:', error);
    document.getElementById('productsGrid').innerHTML = '<p>Failed to load products.</p>';
  }
}

function renderProducts(products) {
  const grid = document.getElementById('productsGrid');
  grid.innerHTML = '';
  products.forEach(product => {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
      <img src="${product.img}" alt="${product.name}">
      <h3>${product.name}</h3>
      <p>${product.desc}</p>
      <div class="price">${product.price}</div>
      <button>Add to Cart</button>
    `;
    grid.appendChild(card);
  });
}

function scrollToProducts() {
  document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
}

// Modal logic for Upload Product
const uploadBtn = document.getElementById('uploadProductBtn');
const uploadModal = document.getElementById('uploadModal');
const closeModal = document.getElementById('closeModal');
const uploadForm = document.getElementById('uploadProductForm');
const uploadStatus = document.getElementById('uploadStatus');

uploadBtn.onclick = () => {
  uploadModal.style.display = 'flex';
  uploadStatus.textContent = '';
  uploadForm.reset();
};
closeModal.onclick = () => {
  uploadModal.style.display = 'none';
};
window.onclick = (e) => {
  if (e.target === uploadModal) uploadModal.style.display = 'none';
};

uploadForm.onsubmit = async (e) => {
  e.preventDefault();
  uploadStatus.textContent = 'Uploading...';
  const data = {
    name: uploadForm.name.value,
    desc: uploadForm.desc.value,
    price: uploadForm.price.value,
    img: uploadForm.img.value
  };
  try {
    const res = await fetch('products.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();
    if (res.ok && result.success) {
      uploadStatus.style.color = '#388e3c';
      uploadStatus.textContent = 'Product uploaded!';
      fetchAndRenderProducts();
      setTimeout(() => { uploadModal.style.display = 'none'; }, 1000);
    } else {
      uploadStatus.style.color = '#d32f2f';
      uploadStatus.textContent = result.error || 'Upload failed.';
    }
  } catch (err) {
    uploadStatus.style.color = '#d32f2f';
    uploadStatus.textContent = 'Network error.';
  }
};

// Render products on page load
fetchAndRenderProducts(); 