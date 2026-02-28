/**
 * Transaction System Logic
 */

// Adds a new product row to the item list
function addItemRow() {
    const container = document.getElementById('item-list');
    const firstRow = container.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);
    
    // Reset values in the new row
    newRow.querySelectorAll('input').forEach(i => i.value = '');
    newRow.querySelector('.row-qty').value = 0;
    newRow.querySelector('.row-total').value = '0.00';
    newRow.querySelector('select').selectedIndex = 0;
    
    container.appendChild(newRow);
}

// Removes a specific row and updates totals
function removeRow(btn) {
    if (document.querySelectorAll('.item-row').length > 1) {
        btn.closest('.item-row').remove();
        calculateGrandTotal();
    }
}

// Updates price and stock info when an item is selected
function updateRow(select) {
    const row = select.closest('.item-row');
    const opt = select.options[select.selectedIndex];
    row.querySelector('.row-stock').value = opt.dataset.stock || 0;
    row.querySelector('.row-price').value = opt.dataset.price || 0;
    calculateRow(row.querySelector('.row-qty'));
}

// Calculates the total for a single row
function calculateRow(input) {
    const row = input.closest('.item-row');
    const qty = parseFloat(input.value) || 0;
    const price = parseFloat(row.querySelector('.row-price').value) || 0;
    row.querySelector('.row-total').value = (qty * price).toFixed(2);
    calculateGrandTotal();
}

// Synchronizes percentage and value discounts
function syncDiscount(type) {
    let subtotal = 0;
    document.querySelectorAll('.row-total').forEach(i => subtotal += parseFloat(i.value) || 0);
    const discP = document.getElementsByName('discount_percent')[0];
    const discV = document.getElementsByName('discount_val')[0];
    
    if (subtotal > 0) {
        if (type === 'percent') {
            discV.value = ((parseFloat(discP.value || 0) / 100) * subtotal).toFixed(2);
        } else {
            discP.value = ((parseFloat(discV.value || 0) / subtotal) * 100).toFixed(2);
        }
    }
    calculateGrandTotal();
}

// Calculates the final amount including VAT and Discounts
function calculateGrandTotal() {
    let subtotal = 0;
    document.querySelectorAll('.row-total').forEach(i => subtotal += parseFloat(i.value) || 0);
    
    const vatP = parseFloat(document.getElementsByName('vat')[0].value) || 0;
    const discV = parseFloat(document.getElementsByName('discount_val')[0].value) || 0;
    
    let total = (subtotal + (subtotal * (vatP / 100))) - discV;
    document.getElementById('grand-total').value = total.toFixed(2);
    calculateChange();
}

// Calculates change due based on amount tendered
function calculateChange() {
    const total = parseFloat(document.getElementById('grand-total').value) || 0;
    const tendered = parseFloat(document.getElementById('tendered').value) || 0;
    document.getElementById('change-due').value = (tendered - total).toFixed(2);
}