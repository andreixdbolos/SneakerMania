let minusBtn = document.querySelector("minus-btn");
let cantitate = document.querySelector("cantitatex");

if (cantitate.textContent.includes("1")) {
  minusBtn.classList.add("hidden");
}

console.log(cantitate.textContent);
