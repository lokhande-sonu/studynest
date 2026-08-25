<!-- START: imohitmehto | 2026-08-25 | Created: QA test plan for 4-requirement bug fix verification -->
# StudyNest QA Test Plan — 4-Requirement Bug Fix Verification

**Version:** 1.0
**Date:** 2026-08-25
**Scope:** Verify all bug fixes across Requirements 1–4 before release
**Severity Levels:** P0 (blocker/financial data integrity), P1 (major usability), P2 (minor/visual)

---

## Table of Contents

1. [Requirement 1: Home Page Filter Fix](#requirement-1-home-page-filter-fix)
2. [Requirement 2: Invoice Calculation Fix](#requirement-2-invoice-calculation-fix)
3. [Requirement 3: User Data Management](#requirement-3-user-data-management)
4. [Requirement 4: Order Management](#requirement-4-order-management)
5. [Cross-cutting Regression Checks](#cross-cutting-regression-checks)
6. [Bugs Found During Review](#bugs-found-during-review)

---

## Requirement 1: Home Page Filter Fix

### 1.1 — Sort Dropdown JS Fix (school-shop.blade.php)

**Bug Fixed:** Sort dropdown was outside the `<form>` element, so the `onchange="this.form.submit()"` approach broke. Now uses JavaScript to append `sort_by` URL param directly.

**Source Code (line 287):**
```js
onchange="var url = new URL(window.location); url.searchParams.set('sort_by', this.value); window.location = url.toString();"
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 1.1.1 | Sort dropdown loads correctly | Navigate to shop page with no filters | Sort dropdown visible with "Sort By" as placeholder, "Price Low to High" and "Price High to Low" options | P1 |
| 1.1.2 | Sort by Price Low to High | Click Sort dropdown → select "Price Low to High" | URL updates with `?sort_by=price_low_high`, page reloads, products ordered cheapest first | P1 |
| 1.1.3 | Sort by Price High to Low | Click Sort dropdown → select "Price High to Low" | URL updates with `?sort_by=price_high_low`, page reloads, products ordered most expensive first | P1 |
| 1.1.4 | Sort preserves existing filters | Apply a School filter first, then change sort | Both `school=X` and `sort_by=Y` present in URL; both filters active | P1 |
| 1.1.5 | Sort persists across reload | Select "Price Low to High" → reload page | Dropdown still shows "Price Low to High" selected (check `{{ request('sort_by') == 'price_low_high' ? 'selected' : '' }}`) | P1 |
| 1.1.6 | Clear All Filters removes sort | Apply sort, then click "Clear All Filters" | URL is clean — no `sort_by` param, products return to default order | P2 |

### 1.2 — Duplicate Mobile Selects Removed (index.blade.php)

**Bug Fixed:** Home page had duplicate `<select>` elements for mobile that sent array values (e.g. `school[0]`, `school[1]`) on form submit. Now uses CSS `d-sm-none`/`d-sm-flex` on the single set of selects.

**Source Code (line 105):**
```html
<!-- Mobile duplicates removed: desktop selects use d-sm-flex/d-none for responsive visibility -->
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 1.2.1 | Form has only 3 select elements | Open browser DevTools → inspect the home page form `form-location-wrapper` | Exactly 3 `<select>` elements (school, class, category) — no duplicates | P0 |
| 1.2.2 | Submit with selections — no array values | Select School + Class + Category → click search button (magnifying glass) | Query string has `school=X&class=Y&category=Z` as scalar values, NOT `school[0]=X` | P0 |
| 1.2.3 | Desktop layout shows all 3 selects inline | View on desktop (≥768px) | All 3 selects visible horizontally in one row | P1 |
| 1.2.4 | Mobile layout renders selects properly | View on mobile (<768px) | Selects stack vertically, each at full width, all functional | P1 |
| 1.2.5 | Select2 initializes on mobile | On mobile viewport, open the School dropdown | Select2 dropdown appears (not native `<select>` dropdown) | P2 |
| 1.2.6 | Form submits correctly from mobile | Complete selections on mobile → tap search icon | Redirects to school-shop with correct scalar query params | P1 |

---

## Requirement 2: Invoice Calculation Fix

### 2.1 — GST Double-Counting Fix

**Bug Fixed:** For GST-inclusive products with quantity > 1, GST was being calculated on the total (price × qty) instead of per-unit, effectively double-counting.

**Key Code (WebsiteController.php, lines 1010–1027):**
```php
if ($gstType === 'inclusive') {
    $itemPriceBeforeGST = round($itemPriceBeforeGST / (1 + ($gstRate / 100)), 2);
    $itemGST = round($displayPrice - $itemPriceBeforeGST, 2);
    $itemCGST = round($itemGST / 2, 2);
    $itemSGST = round($itemGST / 2, 2);
}
// Then multiplied by quantity:
$subTotal += $itemPriceBeforeGST * $item->quantity;
$totalCGST += $itemCGST * $item->quantity;
$totalSGST += $itemSGST * $item->quantity;
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 2.1.1 | Single item, qty=1, inclusive GST | Add 1× ₹118 product (18% inclusive GST) to cart → place COD order | Pre-GST = ₹100, GST = ₹18, CGST = ₹9, SGST = ₹9, Total = ₹118 | P0 |
| 2.1.2 | Single item, qty=3, inclusive GST | Add 3× ₹118 product (18% inclusive GST) | Pre-GST per unit = ₹100, itemTotal = ₹300, GST per unit = ₹18, totalGST = ₹54, Grand total = ₹354 | P0 |
| 2.1.3 | Verify: NO double-counting for qty > 1 | Compare totals for qty=1 vs qty=3 | qty=3 total must be exactly 3× qty=1 total. GST line-item should be `₹18 × qty`, not `₹18 × qty × qty` | P0 |
| 2.1.4 | Exclusive GST with qty > 1 | Add 2× ₹100 product (18% exclusive GST) | Pre-GST = ₹200, CGST = ₹18, SGST = ₹18, Total = ₹236 | P0 |
| 2.1.5 | Mixed cart: inclusive + exclusive items | Add 1 inclusive item + 1 exclusive item | Each item calculated independently; no cross-contamination of GST amounts | P0 |
| 2.1.6 | Invoice PDF GST breakdown | Place order → Download Invoice PDF from management panel | PDF shows per-item: product_rate (pre-GST), GST rate, CGST amount, SGST amount, GST total. All correct. | P1 |

### 2.2 — Checkout Price Source Fix

**Bug Fixed:** Checkout was reading price from `ProductVariant` model (which has no price fields). Now reads from `ProductStockInventory`.

**Key Code (checkout.blade.php, lines 131–138):**
```php
$sQuery = \App\Models\ProductStockInventory::where('prod_id', $item->product_id);
if ($item->variant_id) {
    $sQuery->where('prod_variant_id', $item->variant_id);
} else {
    $sQuery->whereNull('prod_variant_id');
}
$stock = $sQuery->first();
$price = $stock ? ($stock->discounted_unit_price ?? $stock->unit_price) : ($item->product->discounted_price ?? $item->product->p_price);
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 2.2.1 | Checkout shows correct price for variant product | Add a variant product to cart → go to checkout | Subtotal matches the price shown on the product card for that variant | P0 |
| 2.2.2 | Checkout shows correct price for non-variant product | Add a non-variant product to cart → go to checkout | Subtotal matches the product's stock inventory price | P0 |
| 2.2.3 | Checkout uses discounted price when available | Product has `discounted_unit_price = ₹80` and `unit_price = ₹100` | Checkout shows ₹80, not ₹100 | P0 |
| 2.2.4 | Checkout fallback to product price | Product has no stock inventory record | Falls back to `discounted_price` or `p_price` from products table | P1 |
| 2.2.5 | Price consistency: product card → cart → checkout | Note price on product card, add to cart, go to checkout | All three locations show the same price | P0 |

### 2.3 — Delivery Charges Fix

**Bug Fixed:** Delivery charges referenced a non-existent column. Now iterates `Charge` model where `charge_status = 1`, supporting both `fixed` and `percentage` types.

**Key Code (checkout.blade.php, lines 161–170):**
```php
$activeCharges = \App\Models\Charge::where('charge_status', 1)->get();
$deliveryCharge = 0;
foreach ($activeCharges as $charge) {
    if ($charge->charge_type === 'fixed') {
        $deliveryCharge += $charge->charge_value;
    } elseif ($charge->charge_type === 'percentage') {
        $deliveryCharge += ($subtotal * $charge->charge_value) / 100;
    }
}
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 2.3.1 | Fixed delivery charge applied | Set Charge record: `charge_type=fixed, charge_value=50, charge_status=1` → go to checkout | "Delivery Charge" line shows ₹50.00 | P0 |
| 2.3.2 | Percentage delivery charge applied | Set Charge record: `charge_type=percentage, charge_value=10, charge_status=1` | Delivery charge = 10% of subtotal | P0 |
| 2.3.3 | Multiple active charges sum correctly | Two fixed charges: ₹50 + ₹30 | Delivery charge shows ₹80.00 | P1 |
| 2.3.4 | Inactive charges excluded | Set a charge to `charge_status=0` | That charge NOT included in delivery calculation | P0 |
| 2.3.5 | Zero subtotal produces zero delivery | Remove all cart items (empty cart edge) or if subtotal = 0 | Delivery charge = ₹0.00 (code has `$subTotal == 0` guard in placeOrder) | P1 |
| 2.3.6 | Checkout total = subtotal + delivery | Note subtotal, note delivery charge | Total = subtotal + delivery charge (verify arithmetic) | P0 |
| 2.3.7 | Delivery charges match in checkout & order placement | Compare checkout display with `order_charges` saved in DB | Same delivery charge values saved to order | P0 |

### 2.4 — Customer Order Subtotal Fix

**Bug Fixed:** Order subtotal on customer-facing "My Orders > Show" page was calculated incorrectly.

**Key Code (my-orders/show.blade.php, lines 169–171):**
```php
$subTotal = collect($order->order_items)->sum(function ($item) {
    return $item['product_rate'] * $item['product_qty'];
});
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 2.4.1 | Subtotal = sum of (rate × qty) for all items | Place order with 2 different items (different prices/qty) → View order details | Subtotal = (item1_rate × item1_qty) + (item2_rate × item2_qty) | P0 |
| 2.4.2 | Subtotal does NOT include delivery/GST | Compare subtotal line against grand total | Grand total = subtotal + all charges (delivery + GST). Subtotal alone ≠ grand total | P0 |
| 2.4.3 | Subtotal matches order creation values | Compare displayed `product_rate` × `product_qty` per item | Mathematically correct; sum matches the Subtotal line | P0 |
| 2.4.4 | Single item order subtotal | Order with 1 item, qty=5, rate=₹100 | Subtotal = ₹500.00 | P1 |
| 2.4.5 | Grand Total consistency | Verify: Grand Total = Subtotal + Σ Charges (including CGST, SGST, delivery) | Arithmetic matches; no off-by-rounding | P0 |

---

## Requirement 3: User Data Management

### 3.1 — Checkout Address Pre-fill

**Bug Fixed:** Checkout form fields were not pre-filled from the customer's stored profile. Now uses `auth()->user()->cust_*` fields.

**Key Code (checkout.blade.php, lines 70–83):**
```php
value="{{ old('address', auth()->user()->cust_address ?? '') }}"
value="{{ old('city', auth()->user()->cust_city ?? '') }}"
value="{{ old('state', auth()->user()->cust_state ?? '') }}"
value="{{ old('pincode', auth()->user()->cust_pincode ?? '') }}"
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 3.1.1 | Address pre-filled for logged-in user with profile data | Login as user with `cust_address, cust_city, cust_state, cust_pincode` set → go to checkout | All 4 fields pre-filled with stored values | P0 |
| 3.1.2 | Email pre-filled | Same user → checkout | Email field shows `cust_email` | P1 |
| 3.1.3 | Phone pre-filled | Same user → checkout | Phone field shows `cust_mobile` | P1 |
| 3.1.4 | Empty profile fields show blank | Login as user with NULL `cust_address` | Address field is empty (not showing "null" or "N/A") | P1 |
| 3.1.5 | `old()` override on validation failure | Submit checkout with missing required field → validation error | Fields retain user-entered values via `old()`, not re-prefilled from profile | P1 |
| 3.1.6 | New user with no profile data | Login as newly registered user (no address data) | All address fields empty; user can fill manually | P2 |

### 3.2 — Customer Profile Update After Order

**Bug Fixed:** After placing an order, the customer's profile was never updated with the delivery address entered at checkout. Now updates `cust_address, cust_city, cust_state, cust_pincode`.

**Key Code (WebsiteController.php, lines 1210–1218):**
```php
if (Auth::check()) {
    \App\Models\Customer::where('cust_id', Auth::id())->update([
        'cust_address' => $request->address,
        'cust_city' => $request->city,
        'cust_state' => $request->state,
        'cust_pincode' => $request->pincode,
    ]);
}
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 3.2.1 | COD order updates profile | Place COD order with address "123 Test Street, Mumbai" → Check customer DB record | `cust_address = '123 Test Street'`, `cust_city = 'Mumbai'`, etc. updated | P0 |
| 3.2.2 | Online order updates profile | Place online payment order → complete payment successfully | Customer profile updated after successful payment callback | P0 |
| 3.2.3 | Profile update only for logged-in users | Place order while logged in | Profile updated (verify Auth::check() guard) | P0 |
| 3.2.4 | Delivery details differ from profile | Profile has "Old Address" → enter "New Address" at checkout → place order | Profile now shows "New Address"; future checkout pre-fills "New Address" | P0 |
| 3.2.5 | Profile update happens for both COD and online paths | COD path (line 1210–1218) and online path (line 1170–1178) both have the update block | Both code paths update customer profile | P0 |

---

## Requirement 4: Order Management

### 4.1 — Status Labels Consistency

**Bug Fixed:** Status labels were inconsistent across multiple views. Now all views use the same mapping: `0=Cancelled(danger)`, `1=Pending(warning)`, `2=Confirmed(primary)`, `3=Delivered(success)`.

**Canonical Source:** `App\Enums\OrderStatus` class (used in some views) and inline maps (used in others). Both must agree.

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 4.1.1 | Order Details page status labels | Create orders with status 0,1,2,3 → view each in management order-details | 0=Cancelled (danger badge), 1=Pending (warning), 2=Confirmed (primary), 3=Delivered (success) | P0 |
| 4.1.2 | Order Report page status labels | Same orders → view order-report | Identical labels and badge colors as 4.1.1 | P0 |
| 4.1.3 | Transaction Report page status labels | Same orders → view transaction-report | Identical labels and badge colors | P0 |
| 4.1.4 | Customer Profile page status labels | View a customer's order list in customer-profile | Identical labels and badge colors | P0 |
| 4.1.5 | Status dropdown in order-details modal | Open "Update Order Status" modal | Options: Pending(1), Confirmed(2), Delivered(3), Cancelled(0) — all correctly labeled | P0 |
| 4.1.6 | Enum class matches inline maps | Compare `OrderStatus::label()` output with all inline `$orderStatusMap` arrays in views | All identical — no drift | P0 |
| 4.1.7 | Customer-facing "My Orders > Show" badge | Customer views their own order | Badge uses `OrderStatus::badge()` — same labels/colors | P1 |

### 4.2 — Filter Dropdown Labels

**Bug Fixed:** Order Report and Transaction Report filter dropdowns had wrong labels.

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 4.2.1 | Order Report filter options | Open Order Report page → inspect Order Status filter | Options: All, Pending(1), Confirmed(2), Delivered(3), Cancelled(0) | P0 |
| 4.2.2 | Transaction Report filter options | Open Transaction Report page → inspect Order Status filter | Same options as 4.2.1 | P0 |
| 4.2.3 | Transaction Report filter works | Select "Pending" from Order Status filter → submit | Only orders with `order_status = 1` shown in results | P0 |
| 4.2.4 | Transaction Report "Cancelled" filter value | Select "Cancelled" | Filter sends `order_status=0` (not any other value) | P0 |
| 4.2.5 | Payment Status filter values (Transaction Report) | Inspect Payment Status filter dropdown | Options: All, Paid(1), Pending(0) — verify correct value mapping | P1 |

### 4.3 — PDF Status Display Fix

**Bug Fixed:** PDF export only showed "Delivered" or "Other". Now uses `OrderStatus::label()` for all statuses.

**Key Code (order-report-pdf.blade.php, line 33):**
```php
<td>{{ \App\Enums\OrderStatus::label($order->order_status) }}</td>
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 4.3.1 | PDF shows "Cancelled" for status 0 | Export PDF with cancelled order | PDF row shows "Cancelled" (not "Other") | P0 |
| 4.3.2 | PDF shows "Pending" for status 1 | Export PDF with pending order | PDF row shows "Pending" | P0 |
| 4.3.3 | PDF shows "Confirmed" for status 2 | Export PDF with confirmed order | PDF row shows "Confirmed" | P0 |
| 4.3.4 | PDF shows "Delivered" for status 3 | Export PDF with delivered order | PDF row shows "Delivered" | P0 |
| 4.3.5 | PDF payment column correct | Export PDF | Payment column shows "Paid" or "Pending" (not numeric codes) | P0 |
| 4.3.6 | PDF with mixed statuses | Export with orders of all 4 statuses | Each row has correct human-readable label | P0 |

### 4.4 — Export Label Consistency

**Bug Fixed:** Excel exports had a third different status mapping from the views and PDF.

**Key Code (OrderController.php, lines 159–162):**
```php
private function orderStatusLabel($status)
{
    return \App\Enums\OrderStatus::label($status);
}
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 4.4.1 | Order Report Excel export labels | Export Order Report to Excel | All status columns show text labels (Pending/Confirmed/Delivered/Cancelled), not numbers | P0 |
| 4.4.2 | Transaction Report Excel export labels | Export Transaction Report to Excel | Status match enum labels; Payment shows "Paid"/"Pending", not numbers | P0 |
| 4.4.3 | Transaction Report Excel order status mapping | Inspect Excel rows for all status values | 0→Cancelled, 1→Pending, 2→Confirmed, 3→Delivered | P0 |
| 4.4.4 | All three exports consistent | Compare Order Report PDF, Order Report Excel, Transaction Report Excel | Same label text for same status across all formats | P0 |

### 4.5 — Stock Restore for Unpaid Orders Fix

**Bug Fixed:** Stock was restored for unpaid (Pending) orders being cancelled, causing phantom stock increases. Now stock is only restored when cancelling a CONFIRMED order.

**Key Code (OrderController.php, line 252):**
```php
if ($request->status == \App\Enums\OrderStatus::CANCELLED && $oldStatus == \App\Enums\OrderStatus::CONFIRMED) {
```

**Also in WebsiteController.php cancelOrder(), line 1417:**
```php
if ($oldStatus == \App\Enums\OrderStatus::CONFIRMED) {
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 4.5.1 | Cancel CONFIRMED order restores stock | Note product stock (e.g., 10) → Place COD order (auto-confirmed) → Cancel from management → Check stock | Stock restored to 10 | P0 |
| 4.5.2 | Cancel PENDING order does NOT restore stock | Place online order (status=PENDING) → Note stock (e.g., 10) → Cancel order → Check stock | Stock stays at 10 (not 11) | P0 |
| 4.5.3 | Stock decrement only happens for CONFIRMED | Place COD order (goes to CONFIRMED) → Check stock | Stock decremented by order qty | P0 |
| 4.5.4 | Stock decrement happens on PayU success callback | Place online order (PENDING) → Complete payment → Verify stock | Stock decremented only after successful payment sets status to CONFIRMED | P0 |
| 4.5.5 | Cancel after partial payment still correct | Order is PENDING (payment failed) → Cancel | No stock restoration (was never decremented) | P0 |
| 4.5.6 | Double-cancel protection | Cancel a CONFIRMED order (stock restored) → try to cancel again | Second cancel either blocked or no-op for stock (status already CANCELLED) | P1 |

### 4.6 — Payment Status Sync Fix

**Bug Fixed:** When admin marks payment as received, order status now syncs to CONFIRMED.

**Key Code (OrderController.php, lines 325–327):**
```php
if ($request->pay_status == 1) { // Paid
    $order->order_status = \App\Enums\OrderStatus::CONFIRMED;
}
```

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 4.6.1 | Mark payment received → status becomes Confirmed | Pending order → Click "Update Payment Received" | `order_payment_status = 1`, `order_status = 2 (Confirmed)`, `order_paid_amt = order_total_amt` | P0 |
| 4.6.2 | Payment status shows "Paid" after update | After 4.6.1 → refresh order details page | Payment badge shows "Paid" (green) | P0 |
| 4.6.3 | Order status shows "Confirmed" after payment | After 4.6.1 → refresh order details page | Order badge shows "Confirmed" (primary) | P0 |
| 4.6.4 | Due amount zeroed out | After marking payment received | `order_due_amt = 0` | P0 |
| 4.6.5 | Prevent double payment update | Mark as Paid → try to mark as Paid again | Guard: `if ($order->order_payment_status != '1')` — button should not appear | P1 |

### 4.7 — Log Level Fix

**Bug Fixed:** `Log::error()` was used for debug messages. Changed to `Log::debug()` where appropriate.

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| 4.7.1 | Search logs for debug-level messages | Review code for `Log::debug` calls (WebsiteController lines 1455, 1458) | Debug messages use `Log::debug()`, not `Log::error()` | P2 |
| 4.7.2 | Error logs still use Log::error | Check actual error catch blocks (line 1226, etc.) | Real errors still logged as `Log::error()` | P2 |
| 4.7.3 | No Log::error for non-error messages | Grep codebase for `Log::error` | Only genuine error conditions use `Log::error` — no false positives in error monitoring | P2 |

---

## Cross-cutting Regression Checks

These verify that the fixes didn't break adjacent functionality.

| # | Test Case | Steps | Expected Result | Priority |
|---|-----------|-------|-----------------|----------|
| R.1 | Full COD order flow end-to-end | Login → Add to cart → Checkout (verify pre-fill) → Place COD → Verify thank you → Check order in My Orders → Check in Management Panel | All steps complete; order created with correct status, items, amounts | P0 |
| R.2 | Full online order flow end-to-end | Same as R.1 but with online payment → complete PayU flow | Payment callback sets status to Confirmed; stock decremented | P0 |
| R.3 | Cart still works correctly | Add items → change quantity → remove item → proceed to checkout | Cart calculations correct; no phantom array values | P0 |
| R.4 | Product detail page unaffected | Navigate to any product detail page | Page loads; add to cart works; price display correct | P1 |
| R.5 | Bundle add-to-cart unaffected | Expand bundle on shop page → select products → Add to Cart | Bundle items added correctly; no JS errors | P1 |
| R.6 | Search functionality on home page | Type in search box → submit | Redirects to shop with search results — no array value errors | P1 |
| R.7 | Responsive design on home page | Test at 375px, 768px, 1024px, 1440px widths | Layout renders correctly at all breakpoints | P2 |
| R.8 | Pagination with filters | Apply filters → paginate → verify filters persist | Same filters applied on page 2, 3, etc. | P2 |
| R.9 | Order cancellation from customer side | Customer cancels a PENDING online order → verify stock not restored | Stock unchanged (matches fix 4.5.2) | P0 |
| R.10 | Invoice PDF generates without errors | Download invoice from management panel for any order | PDF downloads; shows correct items, amounts, GST breakdown | P1 |

---

## Bugs Found During Review

While reviewing the code for this test plan, the following issues were identified:

### Bug A: Transaction Report Payment Status Filter — Duplicate `name="order_status"` (P1)

**File:** `resources/views/management/transaction-report.blade.php`, lines 54 and 65
**Issue:** Both the "Order Status" filter `<select>` and "Payment Status" filter `<select>` use `name="order_status"`. The payment status filter's value overwrites the order status filter in the request, so **filtering by Order Status on the Transaction Report page is silently broken**.

```html
<!-- Line 54: Order Status filter -->
<select name="order_status" class="form-select form-select-sm">

<!-- Line 65: Payment Status filter — BUG: same name -->
<select name="order_status" class="form-select form-select-sm">
```

**Steps to Reproduce:**
1. Go to Transaction Report page
2. Select Order Status = "Pending" and Payment Status = "Paid"
3. Click Filter
4. Observe: only Payment Status filter is applied; Order Status filter is ignored (both sent as `order_status`)

**Expected:** Payment Status filter should use `name="payment_status"`.
**Actual:** Uses `name="order_status"`, colliding with the order status filter.
**Severity:** P1 — Filter functionality is broken for any combination of Order Status + Payment Status.

---

### Bug B: Transaction Report Payment Status Label Mapping — Value 0 = "Pending" is misleading (P2)

**File:** `resources/views/management/transaction-report.blade.php`, lines 66–68

```html
<option value="1" ...>Paid</option>
<option value="0" ...>Pending</option>
```

But elsewhere in the codebase, payment status `0` means "Failed" and `2` means "Pending" (see `order-details.blade.php` lines 52–55 and `my-orders/show.blade.php` lines 143–148). The Transaction Report filter maps `0→Pending` while every other view maps `0→Failed`.

**Severity:** P2 — Confusing for admin users; may cause incorrect filtering expectations.

---

### Bug C: `order-exportOrderReport()` method writes raw numbers instead of labels (P2)

**File:** `app/Http/Controllers/Management/OrderController.php`, lines 385–386

```php
$sheet->setCellValue('E' . $row, $order->order_payment_status);  // raw number
$sheet->setCellValue('F' . $row, $order->order_status);          // raw number
```

This is in the legacy `exportOrderReport()` method (different from the newer `exportExcel()` which does use labels). If this route is still active, admins would see numeric status codes in the exported Excel.

**Severity:** P2 — Only affects the legacy export endpoint if it's still routable.

---

### Bug D: `updatePaymentStatus` does NOT decrement stock when marking COD payment received (P1)

**File:** `app/Http/Controllers/Management/OrderController.php`, lines 316–331

When admin clicks "Update Payment Received" for a COD order that is still PENDING, the code sets `order_status = CONFIRMED` and `order_payment_status = 1`, but **does not decrement stock**. Stock is only decremented in the COD path of `placeOrder()` (which sets status to CONFIRMED immediately) and in the `payuCallback()`. If a COD order somehow ends up in PENDING state (e.g., a future flow change), marking it as paid won't decrement stock.

**Severity:** P1 — Currently masked because COD orders are set to CONFIRMED at placement time. But the `updatePaymentStatus` path is incomplete. Flag for awareness; test with current flow where COD = CONFIRMED.

---

### Bug E: Online order placed → redirected to PayU → payment fails → cart not cleared but stock not decremented (Edge Case) (P2)

**File:** `app\Http\Controllers\WebsiteController.php`, line 1528

When PayU payment fails, user is redirected to cart with error. Order stays PENDING. Cart is NOT cleared (correct behavior). However, the order was already saved with items. If user abandons the cart and never retries, the PENDING order sits in the database. If admin later cancels it, stock is NOT restored (correct per fix 4.5). But if the user places a NEW online order while the old PENDING one exists, they could have two PENDING orders competing for the same stock.

**Severity:** P2 — Edge case; requires monitoring. Suggestion: add a periodic job to auto-cancel PENDING orders older than X hours.

---

## Test Execution Checklist

- [ ] All P0 test cases from Requirements 1–4 pass
- [ ] All P1 test cases from Requirements 1–4 pass
- [ ] Cross-cutting regression checks R.1–R.10 pass
- [ ] Bug A (duplicate filter name) is fixed and re-tested
- [ ] Bug B (payment status value mapping) is confirmed acceptable or fixed
- [ ] Bug C (legacy export labels) is confirmed acceptable or fixed
- [ ] Bug D (payment status update + stock) is confirmed as known acceptable risk or fixed
- [ ] Bug E (abandoned online orders) is documented for future work
- [ ] No JS console errors on any page tested
- [ ] No 500 errors in Laravel logs during testing
- [ ] Status label consistency verified across ALL management views and exports

---

## Sign-off

| Role | Name | Date | Status |
|------|------|------|--------|
| QA Engineer | | | |
| Lead Engineer | | | |
| Product Manager | | | |
