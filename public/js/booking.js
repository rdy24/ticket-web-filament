const plus = document.getElementById("plus");
const minus = document.getElementById("minus");
const text = document.getElementById("count-text");
const people = document.getElementById("people");
const totalPriceElement = document.getElementById("total-price");
const realTicketPrice = document.getElementById("realTicketPrice");

const pricePerItem = realTicketPrice.value; // default price per item in Rupiah

function formatRupiah(number) {
    return "Rp " + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function updateTotalPrice() {
    let currentValue = parseInt(people.value);
    let totalPrice = currentValue * pricePerItem;
    const ppn = 0.11;
    const totalPpn = totalPrice * ppn;
    const grandTotal = totalPrice + totalPpn;
    totalPriceElement.textContent = formatRupiah(grandTotal);
    document.getElementById("total_ppn").value = totalPpn;
    document.getElementById("sub_total").value = totalPrice;
    document.getElementById("total_amount").value = grandTotal;
}

plus.addEventListener("click", () => {
    let currentValue = parseInt(people.value);
    currentValue++;
    people.value = currentValue;
    text.textContent = currentValue;
    updateTotalPrice();
});

minus.addEventListener("click", () => {
    let currentValue = parseInt(people.value);
    if (currentValue > 1) {
        currentValue--;
        people.value = currentValue;
        text.textContent = currentValue;
        updateTotalPrice();
    }
});

// Initialize total price
updateTotalPrice();
