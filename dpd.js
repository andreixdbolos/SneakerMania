let meniu = document.querySelector(".meniu-admin");
let meniuBtn = document.querySelector(".hamburger2");
let liMenu = document.querySelectorAll(".li-menu");

meniuBtn.addEventListener("click", () => {
  if (meniu.classList.contains("hidden")) {
    meniu.classList.remove("hidden");
    meniu.classList.add("visible");
    liMenu.classList.add("visible");
  } else {
    meniu.classList.remove("visible");
    meniu.classList.add("hidden");
    liMenu.classList.add("hidden");
  }
});
