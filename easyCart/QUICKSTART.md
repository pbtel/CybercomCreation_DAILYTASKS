# EasyCart Phase 2 - Quick Start Guide

## 🚀 Get Started in 2 Minutes!

### Step 1: Extract Files
Download and extract the `easycart-phase2` folder to your web server directory.

### Step 2: Start Server

**Option A: PHP Built-in Server (Easiest)**
```bash
cd easycart-phase2
php -S localhost:8000
```
Then open: **http://localhost:8000**

**Option B: XAMPP/WAMP**
- Place folder in `htdocs/`
- Open: **http://localhost/easycart-phase2**

### Step 3: Explore!

#### 🏠 Homepage
- See the auto-sliding banner
- Browse featured products
- Check out categories and brands

#### 🛍️ Shopping
1. Click "Products" in navigation
2. Use filters (Category, Price, Rating)
3. Toggle Grid/List view
4. Click any product

#### 📦 Product Detail
1. View product details
2. Select variants (Color, Storage)
3. Click "Add to Cart"
4. See cart badge update

#### 🛒 Cart
1. Click "Cart" in navigation
2. Update quantities
3. Remove items if needed
4. See dynamic price calculations
5. Click "Proceed to Checkout"

#### 💳 Checkout
1. Fill shipping info
2. Select payment method
3. Review order summary
4. Click "Place Order"

#### 📋 Orders
1. View order history
2. See order statistics
3. Check order status
4. Track shipments

#### 🔐 Login (Optional)
**Demo Credentials:**
- Email: `demo@easycart.com`
- Password: `demo123`

---

## 🎯 What to Test

### ✅ Core Features
- [ ] Browse products
- [ ] Apply filters
- [ ] Add products to cart
- [ ] Update cart quantities
- [ ] View cart summary
- [ ] Complete checkout
- [ ] View orders

### ✅ Design Features
- [ ] Responsive design (resize browser)
- [ ] Auto-sliding banner
- [ ] Grid/List view toggle
- [ ] Smooth hover effects
- [ ] Product badges (Hot, New, Sale)
- [ ] Rating stars display

---

## 📱 Mobile Testing

Resize your browser to see:
- Mobile-optimized layouts
- Stacked columns
- Touch-friendly buttons
- Readable fonts

---

## 🎨 Design Highlights

- **Modern gradient colors** (purple-pink)
- **Soft shadows** and rounded corners
- **Smooth animations** (pure CSS)
- **Card-based** components
- **Clean typography** (Outfit + JetBrains Mono)

---

## 🔥 Key Features

✅ **20+ Products** across 6 categories
✅ **Smart Filtering** by category, price, rating
✅ **Dynamic Cart** with session storage
✅ **Real-time Calculations** (subtotal, shipping, tax)
✅ **Variant Selection** (colors, storage, sizes)
✅ **Order Management** with status tracking
✅ **Free Shipping** above ₹999
✅ **Mobile Responsive** design

---

## 💡 Pro Tips

1. **Clear Cart**: Click "Clear Cart" button to start fresh
2. **Test Filters**: Combine multiple filters for best results
3. **Check Mobile**: Resize browser to see responsive design
4. **View Code**: All PHP files are well-commented
5. **No Database Needed**: Everything uses PHP sessions

---

## 🐛 Troubleshooting

**Cart not working?**
- Ensure PHP sessions are enabled
- Check browser allows cookies

**Styles not loading?**
- Verify `assets/css/style.css` exists
- Check file paths in `header.php`

**Page not found?**
- Make sure you're in the right directory
- Check PHP server is running

---

## 📚 File Structure

```
easycart-phase2/
├── index.php           ← Start here!
├── products.php        ← Product listing
├── product-detail.php  ← Product page
├── cart.php            ← Shopping cart
├── checkout.php        ← Checkout form
├── orders.php          ← Order history
├── login.php           ← Login page
└── includes/           ← PHP logic
    ├── products.php    ← Product data
    ├── session.php     ← Cart functions
    └── header.php      ← Navigation
```

---

## ✨ Next Steps

After testing Phase 2, you can:

1. **Add More Products** (edit `includes/products.php`)
2. **Customize Colors** (edit `assets/css/style.css` CSS variables)
3. **Add Categories** (edit `includes/categories.php`)
4. **Implement Database** (Phase 3)

---

## 🎉 Enjoy!

You now have a fully functional e-commerce website with modern design and complete shopping functionality!

**Questions?** Read the full `README.md` for detailed documentation.

---

*Happy Shopping! 🛍️*
