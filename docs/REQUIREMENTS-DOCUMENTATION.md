<!-- START: imohitmehto | 2026-08-25 | Created: Client requirements documentation for 4-requirement bug fix project -->
# StudyNest - Client Requirements Documentation

## Project Overview
**Client:** StudyNest (studynested.com)
**Scope:** Development, Bug Fixing & UAT
**Timeline:** 25-30 Working Days

---

## Requirement 1: Home Page Filter Fix

### 1.1 Description
Fix School, Class and Product filter functionality on the homepage. Ensure products are displayed correctly based on selected filters. Improve search accuracy and filtering logic.

### 1.2 Current State Analysis

**Files Involved:**
- `resources/views/website/index.blade.php` (Homepage filter form)
- `resources/views/website/school-shop.blade.php` (Results page)
- `app/Http/Controllers/WebsiteController.php` (Schoolshop method)

**Bugs Identified:**

| Bug ID | Description | Severity | File | Lines |
|--------|-------------|----------|------|-------|
| 1.1 | Sort dropdown outside form - `this.form.submit()` fails | High | school-shop.blade.php | 287 |
| 1.2 | Duplicate `name` attributes for desktop/mobile selects | High | index.blade.php | 67-87, 108-128 |
| 1.3 | Dead New Arrivals query (fetched but not displayed) | Low | index.blade.php | 380-411 |

### 1.3 Expected Behavior

1. **Homepage Filter Form:**
   - Three dropdowns: School, Class, Category
   - One search input field
   - Submit button redirects to `/school-shop` with query parameters
   - Desktop and mobile views should share the same form data

2. **School Shop Page:**
   - Sidebar filters (radio buttons for School, Class, Subject, Category, Gender)
   - Sort dropdown (Price Low to High, Price High to Low)
   - Products filtered correctly based on selected school/class/category
   - Search functionality works across product names

3. **Filter Logic:**
   - School filter: Shows products mapped to selected school OR products marked as "all schools"
   - Class filter: Shows products mapped to selected class OR products marked as "all classes"
   - Category filter: Shows products in selected category
   - Search: Matches product name (partial match)
   - Sort: Orders by minimum stock price (low to high or high to low)

### 1.4 Test Cases

| Test Case | Description | Expected Result |
|-----------|-------------|-----------------|
| TC-1.1 | Select School from homepage dropdown, click search | Redirects to /school-shop?school=X, shows only products for that school |
| TC-1.2 | Select School + Class from homepage | Shows products for that school AND class combination |
| TC-1.3 | Select Category from homepage | Shows only products in that category |
| TC-1.4 | Enter search term in homepage | Shows products matching search term |
| TC-1.5 | Click "Sort by: Price Low to High" on school-shop page | Products sorted by price ascending |
| TC-1.6 | Click "Sort by: Price High to Low" on school-shop page | Products sorted by price descending |
| TC-1.7 | Select School radio button on school-shop sidebar | Products filtered by school, page reloads |
| TC-1.8 | Click "Clear All Filters" button | All filters reset, shows all products |
| TC-1.9 | Mobile: Select School dropdown, submit | Works same as desktop (no duplicate values) |
| TC-1.10 | Filter with no results | Shows "No products found" message |

### 1.5 Acceptance Criteria

- [ ] Homepage filter form submits correctly on both desktop and mobile
- [ ] Sort dropdown works on school-shop page
- [ ] Products filter correctly by school, class, category
- [ ] Search returns relevant results
- [ ] No duplicate form submissions
- [ ] Filter state preserved in URL (shareable links)

---

## Requirement 2: Invoice Calculation Fix

### 2.1 Description
Fix GST calculation. Resolve subtotal and grand total calculation issues. Fix invoice amount mismatch and rounding issues. Ensure invoice values are accurate.

### 2.2 Current State Analysis

**Files Involved:**
- `app/Http/Controllers/WebsiteController.php` (placeOrder method)
- `resources/views/website/checkout.blade.php` (Cart totals display)
- `resources/views/management/invoice.blade.php` (Invoice PDF)
- `resources/views/website/my-orders/show.blade.php` (Customer order details)

**Bugs Identified:**

| Bug ID | Description | Severity | File | Lines |
|--------|-------------|----------|------|-------|
| 2.1 | GST DOUBLE-COUNTED for quantities > 1 (Inclusive GST) | **CRITICAL** | WebsiteController.php | 1014-1016, 1028-1030 |
| 2.2 | Checkout page total completely wrong (missing GST, wrong charges) | **CRITICAL** | checkout.blade.php | 130, 138, 153 |
| 2.3 | Invoice subtotal reverse-engineered, may not match | High | invoice.blade.php | 276 |
| 2.4 | order_items `price` field inconsistent with `product_rate * product_qty` | High | WebsiteController.php | 1008, 1013, 1044-1045 |
| 2.5 | Customer order subtotal uses wrong calculation | Medium | my-orders/show.blade.php | 169 |

### 2.3 Expected Behavior

1. **GST Calculation (placeOrder):**
   - For INCLUSIVE GST: `itemPriceBeforeGST = displayPrice / (1 + gstRate/100)`
   - For EXCLUSIVE GST: `itemPriceBeforeGST = displayPrice`
   - GST Amount = `displayPrice - itemPriceBeforeGST` (per unit)
   - CGST = GST Amount / 2
   - SGST = GST Amount / 2
   - Total CGST = CGST × quantity
   - Total SGST = SGST × quantity

2. **Checkout Display:**
   - Show subtotal (sum of itemPriceBeforeGST × quantity for all items)
   - Show GST breakdown (CGST + SGST)
   - Show delivery charges (from active charges)
   - Show grand total (subtotal + CGST + SGST + charges)

3. **Invoice Display:**
   - Show item rate (itemPriceBeforeGST)
   - Show item quantity
   - Show item total (rate × quantity)
   - Show subtotal (sum of all item totals)
   - Show CGST amount
   - Show SGST amount
   - Show delivery charges
   - Show grand total

### 2.4 Mathematical Example

**Scenario:** 2 units of product at ₹118 (inclusive of 18% GST)

**Correct Calculation:**
```
itemPriceBeforeGST = 118 / 1.18 = 100.00
itemGST = 118 - 100 = 18.00 (per unit)
itemCGST = 18 / 2 = 9.00 (per unit)
itemSGST = 18 / 2 = 9.00 (per unit)

For 2 units:
subtotal = 100 × 2 = 200.00
totalCGST = 9 × 2 = 18.00
totalSGST = 9 × 2 = 18.00
grandTotal = 200 + 18 + 18 = 236.00 ✓
```

**Current Buggy Calculation:**
```
itemGST = (118 - 100) × 2 = 36.00 (includes quantity)
itemCGST = 36 / 2 = 18.00 (includes quantity)
itemSGST = 36 / 2 = 18.00 (includes quantity)

totalCGST = 18 × 2 = 36.00 (DOUBLE COUNTED!)
totalSGST = 18 × 2 = 36.00 (DOUBLE COUNTED!)
grandTotal = 200 + 36 + 36 = 272.00 ✗ (Overcharged by ₹36)
```

### 2.5 Test Cases

| Test Case | Description | Expected Result |
|-----------|-------------|-----------------|
| TC-2.1 | 1 unit at ₹118 (inclusive 18% GST) | Subtotal=100, CGST=9, SGST=9, Total=118 |
| TC-2.2 | 2 units at ₹118 (inclusive 18% GST) | Subtotal=200, CGST=18, SGST=18, Total=236 |
| TC-2.3 | 1 unit at ₹100 (exclusive 18% GST) | Subtotal=100, CGST=9, SGST=9, Total=118 |
| TC-2.4 | 3 units at ₹100 (exclusive 18% GST) | Subtotal=300, CGST=27, SGST=27, Total=354 |
| TC-2.5 | Mixed cart: 1 inclusive + 1 exclusive item | Each calculated correctly, totals summed |
| TC-2.6 | Checkout shows correct GST breakdown | GST visible in cart summary |
| TC-2.7 | Invoice shows correct item totals | rate × qty = item total |
| TC-2.8 | Invoice subtotal = sum of item totals | No discrepancy |
| TC-2.9 | Invoice grand total = subtotal + GST + charges | Math checks out |
| TC-2.10 | Customer order page shows correct totals | Matches invoice |

### 2.6 Acceptance Criteria

- [ ] GST calculated correctly for inclusive and exclusive types
- [ ] No double-counting of GST for multi-quantity orders
- [ ] Checkout page shows accurate total matching placeOrder calculation
- [ ] Invoice math is consistent (all subtotals sum correctly)
- [ ] Customer sees correct amounts in My Orders
- [ ] Delivery charges calculated from active charges (not non-existent column)

---

## Requirement 3: User Data Management

### 3.1 Description
Fix customer data not saving in the database. Ensure customer details are stored correctly. Verify data retrieval across the application.

### 3.2 Current State Analysis

**Files Involved:**
- `app/Http/Controllers/WebsiteAuthController.php` (Registration, Login)
- `app/Http/Controllers/WebsiteController.php` (Checkout, Order placement)
- `app/Models/Customer.php` (Customer model)
- `resources/views/website/checkout.blade.php` (Checkout form)

**Bugs Identified:**

| Bug ID | Description | Severity | File | Lines |
|--------|-------------|----------|------|-------|
| 3.1 | Checkout does NOT pre-fill address fields from customer record | Medium | checkout.blade.php | 54, 58, 70, 75, 79, 83 |
| 3.2 | Customer record NEVER updated with delivery details after order | High | WebsiteController.php | 957-969 |
| 3.3 | Registration `sendOtp()` bypassed (dead code) | Medium | WebsiteAuthController.php | 69 |
| 3.4 | CartItem price sourced from ProductVariant (no price fields) | **CRITICAL** | checkout.blade.php | 130 |
| 3.5 | Password hash could leak in certain serialization contexts | Low | Customer.php | 35-37 |

### 3.3 Expected Behavior

1. **Checkout Pre-fill:**
   - First Name: Pre-filled from `auth()->user()->cust_firstname`
   - Last Name: Pre-filled from `auth()->user()->cust_lastname`
   - Email: Pre-filled from `auth()->user()->cust_email`
   - Phone: Pre-filled from `auth()->user()->cust_mobile`
   - Address: Pre-filled from `auth()->user()->cust_address`
   - City: Pre-filled from `auth()->user()->cust_city`
   - State: Pre-filled from `auth()->user()->cust_state`
   - Pincode: Pre-filled from `auth()->user()->cust_pincode`

2. **Order Placement:**
   - Save delivery details to order JSON (existing)
   - Update customer profile with delivery details (new)

3. **Data Retrieval:**
   - Customer profile shows saved address
   - Next checkout pre-fills saved address
   - Order history shows delivery address from order record

### 3.4 Test Cases

| Test Case | Description | Expected Result |
|-----------|-------------|-----------------|
| TC-3.1 | Login, go to checkout | All fields pre-filled from profile |
| TC-3.2 | Place order with different address | Order saved with new address |
| TC-3.3 | Go to checkout after placing order | New address pre-filled |
| TC-3.4 | Check customer profile | Address updated with delivery details |
| TC-3.5 | View order history | Shows correct delivery address |
| TC-3.6 | Register new user | Account created successfully |
| TC-3.7 | Login with correct credentials | Login successful |
| TC-3.8 | Login with wrong password | Error message shown |

### 3.5 Acceptance Criteria

- [ ] Checkout pre-fills all customer data (name, email, phone, address)
- [ ] After order placement, customer profile updated with delivery details
- [ ] Next checkout uses updated address
- [ ] Customer data saved correctly in database
- [ ] Customer data retrieved correctly across application

---

## Requirement 4: Order Management

### 4.1 Description
Improve complete order management workflow. Fix order processing functionality. Update order status management. Ensure smooth backend order handling.

### 4.2 Current State Analysis

**Files Involved:**
- `app/Http/Controllers/WebsiteController.php` (Order placement, cancellation)
- `app/Http/Controllers/Management\OrderController.php` (Admin order management)
- `app/Enums/OrderStatus.php` (Status enum)
- `resources/views/management/order-details.blade.php` (Admin order details)
- `resources/views/management/order-report.blade.php` (Order reports)
- `resources/views/website/my-orders/` (Customer order views)

**Bugs Identified:**

| Bug ID | Description | Severity | File | Lines |
|--------|-------------|----------|------|-------|
| 4.1 | Status label MISMATCH between enum and ALL admin views | **CRITICAL** | order-details.blade.php + 4 views | 62-67 |
| 4.2 | Export transaction report has THIRD different status mapping | **CRITICAL** | OrderController.php | 471-477 |
| 4.3 | Order report PDF only recognizes status 1 as "Delivered" | High | order-report-pdf.blade.php | 33 |
| 4.4 | Admin order-details modal uses wrong status labels in dropdown | **CRITICAL** | order-details.blade.php | 347-350 |
| 4.5 | Order report filter dropdown uses wrong status labels | High | order-report.blade.php | 54-57 |
| 4.6 | Stock incorrectly restored when cancelling unpaid orders | High | WebsiteController.php | 1391-1408 |
| 4.7 | `updatePaymentStatus()` does not sync order_status | High | OrderController.php | 315-326 |
| 4.8 | COD orders saved twice (PENDING then immediately CONFIRMED) | Low | WebsiteController.php | 1122, 1180-1181 |
| 4.9 | `cancelOrder()` logs errors for successful operations | Low | WebsiteController.php | 1432, 1435 |

### 4.3 Expected Behavior

1. **Status Labels (Enum):**
   ```
   0 = Cancelled (danger/red)
   1 = Pending (warning/yellow)
   2 = Confirmed (primary/blue)
   3 = Delivered (success/green)
   ```

2. **Admin Views:**
   - Order list shows correct status labels
   - Order details shows correct status label
   - Status dropdown shows correct labels
   - Filter dropdown shows correct labels
   - Export shows correct labels
   - PDF shows correct labels

3. **Order Workflow:**
   - Online order: PENDING → (payment success) → CONFIRMED → DELIVERED
   - COD order: PENDING → CONFIRMED (immediately) → DELIVERED
   - Any order can be CANCELLED

4. **Stock Management:**
   - Stock decremented on order placement (COD) or payment success (online)
   - Stock restored ONLY if it was decremented (not for unpaid orders)

5. **Payment Sync:**
   - When payment status updated to "Paid", order status should also update to "Confirmed"

### 4.4 Test Cases

| Test Case | Description | Expected Result |
|-----------|-------------|-----------------|
| TC-4.1 | Admin views PENDING order | Shows "Pending" (not "Delivered") |
| TC-4.2 | Admin views CONFIRMED order | Shows "Confirmed" (not "Order Placed") |
| TC-4.3 | Admin views DELIVERED order | Shows "Delivered" (not "Order Confirmed") |
| TC-4.4 | Admin changes status to "Delivered" | Actual status = 3 (DELIVERED) |
| TC-4.5 | Admin filters by "Pending" status | Shows only status=1 orders |
| TC-4.6 | Admin exports order report | All statuses correctly labeled |
| TC-4.7 | Admin marks unpaid order as "Paid" | Order status also changes to CONFIRMED |
| TC-4.8 | Customer cancels PENDING online order | Stock NOT restored (was never decremented) |
| TC-4.9 | Customer cancels CONFIRMED COD order | Stock restored correctly |
| TC-4.10 | Admin views order report PDF | Shows correct status for all orders |

### 4.5 Acceptance Criteria

- [ ] All admin views show correct status labels matching enum
- [ ] Status dropdown values match enum values
- [ ] Filter dropdown values match enum values
- [ ] Export and PDF show correct status labels
- [ ] Payment status update syncs with order status
- [ ] Stock only restored if it was decremented
- [ ] No duplicate saves for COD orders
- [ ] Debug messages not logged as errors

---

## Development Priority Order

### Phase 1: Low Complexity (Quick Wins)
1. **Requirement 1: Home Page Filter Fix** - 3 bugs, mostly view fixes
2. **Requirement 4 (partial):** Status label fixes in views

### Phase 2: Medium Complexity
3. **Requirement 3: User Data Management** - Customer data flow fixes
4. **Requirement 4 (partial):** Stock management and payment sync

### Phase 3: High Complexity
5. **Requirement 2: Invoice Calculation Fix** - GST math fixes, checkout display

---

## Testing Strategy

### Unit Tests
- GST calculation logic
- Status label mapping
- Filter query building

### Integration Tests
- Full checkout flow with GST
- Order placement with stock management
- Customer data persistence

### Manual Testing
- Homepage filter functionality
- Admin order management
- Invoice generation
- Customer order history

---

## Sign-off Checklist

- [ ] All bugs fixed per requirements
- [ ] All test cases passing
- [ ] Code review completed
- [ ] No regressions introduced
- [ ] Documentation updated
