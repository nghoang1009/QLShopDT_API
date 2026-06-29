# Homepage Functionality Test Plan

## Application Overview

Test cases for the homepage of QLShopDT API, covering the hero section, search/filter controls, featured products, CTA buttons, and top navigation.

## Test Scenarios

### 1. Homepage Functionality

**Seed:** `tests/seed.spec.ts`

#### 1.1. Load homepage and verify main content

**File:** `tests/homepage.test.plan.md`

**Steps:**
  1. -
    - expect: The browser loads http://localhost/QLShopDT_API/app.php successfully.
    - expect: The page title contains "Trang Chủ — PhoneShop".
    - expect: The hero heading "Công nghệ đỉnh cao — Giá tốt nhất" is visible.
    - expect: The homepage search input with placeholder "Tìm kiếm sản phẩm..." is displayed.
    - expect: The featured product section title "SẢN PHẨM NỔI BẬT" is visible.
  2. -
    - expect: The top navigation links for Trang chủ, Sản phẩm, Danh mục, Khách hàng, Nhân viên, Thống kê, Đơn hàng, Giao hàng, Giỏ hàng, and Thanh toán are present.

#### 1.2. Search products from homepage

**File:** `tests/homepage.test.plan.md`

**Steps:**
  1. Click the "🔍 Tìm" button.
    - expect: The search input retains the entered keyword.
    - expect: The featured product area updates to show matching results.
    - expect: At least one product card containing the search keyword is visible.

#### 1.3. Filter products by category

**File:** `tests/homepage.test.plan.md`

**Steps:**
  1. Click the "🔍 Tìm" button.
    - expect: The category filter value is set to "Điện thoại".
    - expect: The product list updates to show products labeled "Điện thoại".
    - expect: Product cards visible after filtering have the category label "📂 Điện thoại".

#### 1.4. Open a featured product detail page

**File:** `tests/homepage.test.plan.md`

**Steps:**
  1. Click the first "Xem chi tiết" link for a featured product.
    - expect: The browser navigates to a product detail page.
    - expect: The URL contains "/views/sanpham/chitietsanpham.php?masp=" or a valid product detail identifier.
    - expect: A product detail heading or product name is visible on the new page.

#### 1.5. Validate homepage CTA buttons

**File:** `tests/homepage.test.plan.md`

**Steps:**
  1. Click the hero section button "Mua ngay".
    - expect: The page scrolls to the featured products section or an element with id "products".
    - expect: The featured product section becomes visible in the viewport.
  2. Click the hero section button "Xem tất cả".
    - expect: The browser navigates to the all-products page.
    - expect: The URL matches /QLShopDT_API/app.php/sanpham or the products listing page.
    - expect: A product listing or catalog page heading is visible.

#### 1.6. Verify primary navigation links from homepage

**File:** `tests/homepage.test.plan.md`

**Steps:**
  1. Click the top navigation link "Sản phẩm".
    - expect: The browser navigates to the products page.
    - expect: The URL contains "/views/sanpham/sanpham.php".
    - expect: The products page heading is visible.
  2. Click the top navigation link "Giỏ hàng".
    - expect: The browser navigates to the cart page.
    - expect: The URL contains "/giohang".
    - expect: A cart page heading or cart-specific element is visible.
